<?php
/**
 * Git Webhook Auto-Deploy
 *
 * Receives push events from GitHub and pulls the latest code.
 * Set up a GitHub webhook pointing to: https://[domain.com]/git-deploy.php
 * Content type: application/json
 * Secret: must match TOKEN below
 */

define("TOKEN", "[YOUR-WEBHOOK-SECRET-TOKEN]");                        // GitHub webhook secret
define("REMOTE_REPOSITORY", "git@github.com:dougcase/[repo-name].git"); // SSH URL to your repository
define("DIR", "/home/[cpanel-user]/[domain.com]/");                     // Absolute path to the site root on the server
define("BRANCH", "refs/heads/main");                                    // Branch to deploy (main or master)
define("LOGFILE", "deploy.log");                                        // Log file name
define("GIT", "git");                                                   // Path to git executable
define("MAX_EXECUTION_TIME", 180);                                      // Override for PHP max_execution_time
define("BEFORE_PULL", "");                                              // Command to run before pull (leave empty)
define("AFTER_PULL", "");                                               // Command to run after pull (leave empty)

$content = file_get_contents('php://input');
$json    = json_decode($_POST['payload'], true);
$file    = fopen(LOGFILE, "a");
$time    = time();
$token   = false;
$sha     = false;
$DIR     = preg_match("/\/$/", DIR) ? DIR : DIR . "/";

// Retrieve the token
if (!$token && isset($_SERVER["HTTP_X_HUB_SIGNATURE"])) {
    list($algo, $token) = explode("=", $_SERVER["HTTP_X_HUB_SIGNATURE"], 2) + ["", ""];
} elseif (isset($_SERVER["HTTP_X_GITLAB_TOKEN"])) {
    $token = $_SERVER["HTTP_X_GITLAB_TOKEN"];
} elseif (isset($_GET["token"])) {
    $token = $_GET["token"];
}

// Retrieve the checkout SHA
if (isset($json["checkout_sha"])) {
    $sha = $json["checkout_sha"];
} elseif (isset($_SERVER["checkout_sha"])) {
    $sha = $_SERVER["checkout_sha"];
} elseif (isset($_GET["sha"])) {
    $sha = $_GET["sha"];
}

// Write timestamp to log
date_default_timezone_set("UTC");
fputs($file, date("d-m-Y (H:i:s)", $time) . "\n");

header("Content-Type: text/plain");

if (!empty(MAX_EXECUTION_TIME)) {
    ini_set("max_execution_time", MAX_EXECUTION_TIME);
}

function forbid($file, $reason)
{
    $error = "=== ERROR: " . $reason . " ===\n*** ACCESS DENIED ***\n";
    http_response_code(403);
    fputs($file, $error . "\n\n");
    echo $error;
    fclose($file);
    exit;
}

// Validate token
if (!empty(TOKEN) && isset($_SERVER["HTTP_X_HUB_SIGNATURE"]) && $token !== hash_hmac($algo, $content, TOKEN)) {
    forbid($file, "X-Hub-Signature does not match TOKEN");
} elseif (!empty(TOKEN) && isset($_SERVER["HTTP_X_GITLAB_TOKEN"]) && $token !== TOKEN) {
    forbid($file, "X-GitLab-Token does not match TOKEN");
} elseif (!empty(TOKEN) && isset($_GET["token"]) && $token !== TOKEN) {
    forbid($file, "\$_GET[\"token\"] does not match TOKEN");
} elseif (!empty(TOKEN) && !isset($_SERVER["HTTP_X_HUB_SIGNATURE"]) && !isset($_SERVER["HTTP_X_GITLAB_TOKEN"]) && !isset($_GET["token"])) {
    forbid($file, "No token detected");
} else {
    if ($json["ref"] === BRANCH) {
        fputs($file, $content . PHP_EOL);

        if (file_exists($DIR . ".git") && is_dir($DIR)) {
            chdir($DIR);
            fputs($file, "*** AUTO PULL INITIATED ***" . "\n");

            // Reset to HEAD if requested
            if (!empty($_GET["reset"]) && $_GET["reset"] === "true") {
                fputs($file, "*** RESET TO HEAD INITIATED ***" . "\n");
                exec(GIT . " reset --hard HEAD 2>&1", $output, $exit);
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: Reset to head failed ===\n" . $output;
                }
                fputs($file, $output);
                echo $output;
            }

            // Before pull command
            if (!empty(BEFORE_PULL)) {
                fputs($file, "*** BEFORE_PULL INITIATED ***" . "\n");
                exec(BEFORE_PULL . " 2>&1", $output, $exit);
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: BEFORE_PULL failed ===\n" . $output;
                }
                fputs($file, $output);
                echo $output;
            }

            // Pull
            exec(GIT . " pull 2>&1", $output, $exit);
            $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";
            if ($exit !== 0) {
                http_response_code(500);
                $output = "=== ERROR: Pull failed ===\n" . $output;
            }
            fputs($file, $output);
            echo $output;

            // Checkout specific SHA if provided
            if (!empty($sha)) {
                fputs($file, "*** RESET TO HASH INITIATED ***" . "\n");
                exec(GIT . " reset --hard {$sha} 2>&1", $output, $exit);
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: Reset failed ===\n" . $output;
                }
                fputs($file, $output);
                echo $output;
            }

            // After pull command
            if (!empty(AFTER_PULL)) {
                fputs($file, "*** AFTER_PULL INITIATED ***" . "\n");
                exec(AFTER_PULL . " 2>&1", $output, $exit);
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: AFTER_PULL failed ===\n" . $output;
                }
                fputs($file, $output);
                echo $output;
            }

            fputs($file, "*** AUTO PULL COMPLETE ***" . "\n");
        } else {
            $error = "=== ERROR: DIR `" . DIR . "` is not a repository ===\n";
            if (!file_exists(DIR)) {
                $error = "=== ERROR: DIR `" . DIR . "` does not exist ===\n";
            } elseif (!is_dir(DIR)) {
                $error = "=== ERROR: DIR `" . DIR . "` is not a directory ===\n";
            }
            http_response_code(400);
            fputs($file, $error);
            echo $error;
        }
    } else {
        $error = "=== ERROR: Pushed branch `" . $json["ref"] . "` does not match BRANCH `" . BRANCH . "` ===\n";
        http_response_code(400);
        fputs($file, $error);
        echo $error;
    }
}

fputs($file, "\n\n" . PHP_EOL);
fclose($file);
