<?php
/**
 * Sitemap Generator — Builds sitemap.xml by scanning the filesystem.
 * Risingline BBB standard. Runs on every deploy via git-deploy.php AFTER_PULL.
 *
 * What it includes:
 *   Pages — .php files containing id="main-content", EXCEPT noindex pages
 *           (meta robots noindex) and error/system files.
 *   PDFs  — only local PDF files actually LINKED from an included page AND
 *           present on disk. Orphaned/unlinked files are never listed.
 *   lastmod — the file's last git commit date (falls back to file mtime).
 *
 * Canonical origin comes from includes/business-config.php ($business['url']),
 * so there is nothing to configure per site except the secret below.
 *
 * Usage:
 *   CLI:     php sitemap-generator.php
 *   Deploy:  in git-deploy.php →
 *              define("AFTER_PULL", "php ".DIR."sitemap-generator.php");
 *            If the site also has search-indexer.php, chain them (sitemap first):
 *              define("AFTER_PULL",
 *                "php ".DIR."sitemap-generator.php && php ".DIR."search-indexer.php");
 *   Web:     https://site.com/sitemap-generator?key=YOUR_SECRET
 *
 * sitemap.xml is a GENERATED artifact — keep it gitignored (see .gitignore).
 */

define('SITEMAP_SECRET', '[SET-A-PER-SITE-SECRET]');  // for the ?key= web trigger

function buildSitemap(string $baseDir = null, bool $quiet = false): int {
    $baseDir   = rtrim($baseDir ?: __DIR__, '/');
    $outputFile = $baseDir . '/sitemap.xml';
    $contentId = 'main-content';

    // Canonical origin from the single source of truth.
    $siteUrl = '';
    $cfg = $baseDir . '/includes/business-config.php';
    if (is_file($cfg)) {
        include $cfg; // defines $business
        if (!empty($business['url'])) $siteUrl = rtrim($business['url'], '/');
    }
    if ($siteUrl === '' || strpos($siteUrl, '[') !== false) {
        if (!$quiet) fwrite(STDERR, "No usable \$business['url'] in includes/business-config.php — aborting.\n");
        return 0;
    }

    // Files never treated as pages, even if they contain the content id.
    $excludeNames = [
        '404.php', 'sitemap-generator.php', 'search-indexer.php', 'git-deploy.php',
    ];
    // Directories that never contain indexable pages.
    $excludeDirs = [
        '/.git/', '/build/', '/includes/', '/assets/', '/css/', '/js/',
        '/img/', '/images/', '/fonts/', '/audit/', '/docs/', '/favicon/',
        '/vendor/', '/node_modules/',
    ];

    // --- 1. Discover page files -------------------------------------------
    $pages = []; // cleanUrlPath => absolute file path
    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($rii as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') continue;
        $abs  = $file->getPathname();
        $relN = str_replace('\\', '/', substr($abs, strlen($baseDir))); // e.g. /services/index.php

        foreach ($excludeDirs as $d) if (strpos($relN, $d) !== false) continue 2;
        if (in_array(basename($abs), $excludeNames, true)) continue;

        $html = file_get_contents($abs);
        if ($html === false) continue;
        if (!preg_match('/id=["\']' . $contentId . '["\']/', $html)) continue;            // not a real page
        if (preg_match('/<meta[^>]+name=["\']robots["\'][^>]*noindex/i', $html)) continue; // noindex

        $path = preg_replace('/\.php$/', '', $relN);   // strip .php
        $path = preg_replace('#/index$#', '', $path);  // dir/index -> dir
        if ($path === '') $path = '/';
        $pages[$path] = $abs;
    }

    // --- 2. Collect ONLY linked, on-disk, local PDFs ----------------------
    $pdfs = []; // urlPath (decoded) => absolute file path
    foreach ($pages as $abs) {
        $html = file_get_contents($abs);
        if ($html === false) continue;
        if (preg_match_all('#href=["\']([^"\']+?\.pdf)["\']#i', $html, $m)) {
            foreach ($m[1] as $href) {
                if (preg_match('#^(https?:)?//#i', $href)) continue; // skip external PDFs
                $urlPath = '/' . ltrim(rawurldecode($href), '/');    // decode %20 etc.
                $disk    = $baseDir . $urlPath;
                if (is_file($disk)) $pdfs[$urlPath] = $disk;          // include only if it truly exists
            }
        }
    }

    // --- 3. lastmod: git commit date, fallback to mtime -------------------
    $lastmod = function (string $abs) use ($baseDir): string {
        $rel = ltrim(str_replace('\\', '/', substr($abs, strlen($baseDir))), '/');
        $out = []; $code = 1;
        @exec('cd ' . escapeshellarg($baseDir) . ' && ' .
              'git log -1 --format=%cI -- ' . escapeshellarg($rel) . ' 2>/dev/null', $out, $code);
        if ($code === 0 && !empty($out[0])) return trim($out[0]);
        return date('c', @filemtime($abs) ?: time());
    };

    // encode a decoded path into a URL-safe loc (per segment; keeps slashes)
    $enc = function (string $p): string {
        return implode('/', array_map('rawurlencode', explode('/', $p)));
    };

    // --- 4. Order: home first, then pages A-Z, then linked PDFs A-Z --------
    uksort($pages, function ($a, $b) {
        if ($a === '/') return -1;
        if ($b === '/') return 1;
        return strcmp($a, $b);
    });
    ksort($pdfs);

    $entries = [];
    foreach ($pages as $path => $abs) {
        $loc = $siteUrl . ($path === '/' ? '/' : $enc($path));
        $entries[] = [$loc, $lastmod($abs)];
    }
    foreach ($pdfs as $path => $abs) {
        $entries[] = [$siteUrl . $enc($path), $lastmod($abs)];
    }

    // --- 5. Emit XML ------------------------------------------------------
    $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($entries as [$loc, $mod]) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
        $xml .= "    <lastmod>" . $mod . "</lastmod>\n";
        $xml .= "  </url>\n";
    }
    $xml .= "</urlset>\n";

    // Safety: never overwrite a good sitemap with an empty one.
    if (empty($pages)) {
        if (!$quiet) fwrite(STDERR, "No pages found — refusing to write an empty sitemap.\n");
        return 0;
    }

    file_put_contents($outputFile, $xml);

    if (!$quiet) {
        echo "Sitemap written: " . count($entries) . " URLs (" .
             count($pages) . " pages, " . count($pdfs) . " linked PDFs).\n";
    }
    return count($entries);
}

// --- Run directly (CLI, or web with key) ---------------------------------
if (basename($_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['PHP_SELF'] ?? '') === 'sitemap-generator.php') {
    $isCli = (php_sapi_name() === 'cli');
    if (!$isCli) {
        if (($_GET['key'] ?? '') !== SITEMAP_SECRET) {
            http_response_code(403);
            header('Content-Type: text/plain');
            die('Forbidden');
        }
        header('Content-Type: text/plain');
    }
    $count = buildSitemap(__DIR__, false);
    if (!$isCli) echo "\n{$count} URLs.";
}
