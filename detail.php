<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

if (!is_array($_GET) || !array_key_exists('url', $_GET) || !$_GET['url']) {
    die ('No URL given!');
}

require_once "vendor/autoload.php";
require_once "includes.php";
?>

<!DOCTYPE>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>.htaccess redirect link checker</title>
    <link rel="stylesheet" href="vendor/components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/app.css" />

    <script type="text/javascript">
        window.addEventListener('load', () => document.getElementById('spinner').classList.remove('show'))
    </script>
</head>
<body>
<div id="spinner" class="loader show"></div>
<div class="row">
    <div class="large-12 columns">
        <h5>Tracking details for URL <?php
            echo $_GET['url']; ?></h5>
        <p>&nbsp;</p>
    </div>
</div>

<div class="row">
    <div class="large-12 columns">
        <?php
        require_once('includes.php');
        $pattern = '/(' . implode('|', array_keys(Response::$statusTexts)) . ')( (' . implode('|', Response::$statusTexts) . '))?$/i';

        exec('curl -I -L --no-sessionid --proxy1.0 ' . escapeshellarg($proxy ?? '') . ' ' . escapeshellarg(rawurldecode($_GET['url'])), $output);

        foreach ($output as $row) {
            if (preg_match($pattern, $row, $matches)) {
                echo sprintf('<span class="%s">%s</span><br>', $classes[(string)($matches[1])[0]], $row);
            } else {
                echo sprintf('%s<br>', $row);
            }
        }
        ?>
    </div>
</div>
</body>
</html>
