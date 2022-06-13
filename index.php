<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

require_once 'vendor/autoload.php';
require_once 'includes.php';
?>
<!DOCTYPE>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>.htaccess redirect link checker</title>
    <link rel="stylesheet" href="css/app.css" />

    <script type="text/javascript">
        window.addEventListener('load', () => document.getElementById('spinner').classList.remove('show'))
        window.setTimeout(() => document.getElementById('spinner').classList.add('hide'), 1000)
    </script>
</head>
<body>
<div id="spinner" class="loader show"></div>
<div class="grid-container">
    <div class="grid-x">
        <div class="cell">
            <h1>.htaccess redirect link checker v1.0.0a</h1>
        </div>
    </div>
    <div class="grid-x">
        <form method="post" class="cell">
            <label>URL list
                <textarea placeholder="Enter URLs" name="uriList" rows="20"><?php

                    if (is_array($_POST) && array_key_exists('uriList', $_POST)) {
                        echo $_POST['uriList'];
                    } ?></textarea>
            </label>
            <button type="submit" class="expanded button">Submit</button>
        </form>
    </div>

    <div class="grid-x">
        <div class="cell">
            <?php

            if (is_array($_POST) && array_key_exists('uriList', $_POST) && $list = explode(PHP_EOL, $_POST['uriList'])) {
                if (isset($proxy) && null !== ($proxy ?? null)) {
                    [$proxyHost, $proxyPort] = explode(':', $proxy, 2);
                }
                array_walk($list, 'trim');
                $list = array_filter($list);
                $content = '<table><thead><tr><th>Origin</th><th>Status</th><th><abbr title="ReDirect Count">RDC</abbr></th><th>Target</th><th>Status</th></tr></thead><tbody>';

                // Set counters
                $validLinks = $invalidLinks = $errors = 0;

                foreach ($list as $index => $entry) {
                    $url = trim($entry);
                    $result = [];
                    $url = idn_to_ascii($url) ?: $url;
                    // prepend protocol (http & https allowed)
                    if (!preg_match('/^https?:\/\//i', $url)) {
                        $url = 'http://' . $url;
                    }
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        if (isset($proxyHost, $proxyPort)) {
                            curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
                            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
                        }
                        curl_setopt($ch, CURLOPT_HEADER, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        //curl_setopt($ch, CURLINFO_HTTP_CODE, true);
                        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

                        $exec = curl_exec($ch);
                        $info = curl_getinfo($ch);
                        $info['http_code_highlight'] = $classes[((string)$info['http_code'])[0]] ?? 'alert';

                        if ($info['http_code'] >= 300 && $info['http_code'] < 400) {
                            $followUp = true;
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            $exec = curl_exec($ch);
                            $info2 = curl_getinfo($ch);
                            $info2['http_code_highlight'] = $classes[((string)$info2['http_code'])[0]];
                        } else {
                            $followUp = false;
                            $info2 = $info;
                        }

                        curl_close($ch);

                        $result[] = [
                            'orig' => $entry,
                            'dataOrigin' => $info2,
                            'dataTarget' => $info,
                            'errorMsg' => ''
                        ];

                        $content .= ("
					<tr>
						<td>$entry</td>
						<td class=\"{$info['http_code_highlight']}\" style=\"text-align:center\" colspan=\"" . ($followUp ? '1' : '4') . "\">{$info['http_code']} " . (Response::$statusTexts[$info['http_code']] ?? '') . "</td>
				");

                        if ($followUp) {
                            $content .= ("
							<td>$info2[redirect_count] <a href=\"detail.php?url=" . rawurlencode($url) . "\" target=\"_blank\"><i class=\"fa fa-info-circle fa-lg\"></i></a></td>
							<td><a href=\"$info2[url]\" target=\"_blank\">$info2[url]</a></td>
							<td class=\"$info2[http_code_highlight]\" style=\"text-align:center\">$info2[http_code] " . Response::$statusTexts[$info2['http_code']] . "</td>
					");
                        }

                        $content .= ("
					</tr>
				");

                        $validLinks++;
                        if ($info2['http_code'] < 200 || $info2['http_code'] >= 300) {
                            $errors++;
                        }
                    } else {
                        $result[] = [
                            'orig' => $entry,
                            'data' => false,
                            'errorMsg' => 'URL validation failed!'
                        ];

                        $content .= ('
					<tr>
						<td>' . $entry . '</td>
						<td colspan="4" class="alert">URL validation failed for "' . $url . '"!</td>
					</tr>
				');
                        $invalidLinks++;
                    }
                }

                $content .= "</tbody></table>";

                $requestTime = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2);
                $seconds = $requestTime / 1000;
                $minutes = floor($seconds / 60);
                if ($minutes > 0) {
                    $rSeconds = $seconds % 60;
                } else {
                    $rSeconds = round($seconds);
                }
                $callouts = ("
			<div class='success callout' data-closable>
				<i class='fa fa-fw fa-lg fa-check-circle'></i> {$validLinks} links checked in {$minutes}m {$rSeconds}s [ {$seconds}s ]
				<button class='close-button' aria-label='Dismiss alert' type='button' data-close><span aria-hidden='true'>&times;</span></button>
			</div>
		");
                if ($invalidLinks) {
                    $callouts .= ("
				<div class='warning callout' data-closable>
					<i class='fa fa-fw fa-lg fa-exclamation-triangle'></i> {$invalidLinks} invalid links found!
					<button class='close-button' aria-label='Dismiss alert' type='button' data-close><span aria-hidden='true'>&times;</span></button>
				</div>
			");
                }
                if ($errors) {
                    $callouts .= ("
				<div class='alert callout' data-closable>
					<i class='fa fa-fw fa-lg fa-times-circle'></i> {$errors} errors found!
					<button class='close-button' aria-label='Dismiss alert' type='button' data-close><span aria-hidden='true'>&times;</span></button>
				</div>
			");
                }
                echo $callouts . $content;
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
