<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?php echo isset($page_title) ? $page_title : '[Site Name] | [Tagline]'; ?></title>
  <meta name="description" content="<?php echo isset($page_description) ? $page_description : '[Default meta description for the site - 120 to 158 characters]'; ?>">

  <?php if (isset($page_noindex) && $page_noindex): ?>
  <meta name="robots" content="noindex, nofollow">
  <?php else: ?>
  <link rel="canonical" href="<?php echo isset($page_canonical) ? $page_canonical : 'https://[domain.com]/'; ?>">
  <?php endif; ?>

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="/img/[favicon-32.png]">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/[favicon-16.png]">
  <link rel="apple-touch-icon" sizes="180x180" href="/img/[apple-touch-icon.png]">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?php echo isset($og_site_name) ? $og_site_name : '[Site Name]'; ?>">
  <meta property="og:title" content="<?php echo isset($og_title) ? $og_title : (isset($page_title) ? $page_title : '[Site Name]'); ?>">
  <meta property="og:description" content="<?php echo isset($og_description) ? $og_description : (isset($page_description) ? $page_description : '[Default OG description]'); ?>">
  <meta property="og:url" content="<?php echo isset($page_canonical) ? $page_canonical : 'https://[domain.com]/'; ?>">
  <meta property="og:image" content="<?php echo isset($og_image) ? $og_image : 'https://[domain.com]/img/[og-image.png]'; ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">

  <!-- Google Fonts (uncomment and update URL for your project)
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=[Font+Name]:wght@400;500;600;700&display=swap" rel="stylesheet">
  -->

  <!-- Bootstrap 5.3.8 (local) -->
  <link rel="stylesheet" href="/css/bootstrap.min.css">

  <!-- Bootstrap Icons 1.11.3 (local) -->
  <link rel="stylesheet" href="/css/bootstrap-icons.min.css">

  <!-- Site Styles -->
  <link rel="stylesheet" href="/css/styles.css?v=20260717">

  <!-- Google Analytics (uncomment and replace [GA4-ID] for your project)
  <script async src="https://www.googletagmanager.com/gtag/js?id=[GA4-ID]"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '[GA4-ID]');
  </script>
  -->

</head>
<body>
