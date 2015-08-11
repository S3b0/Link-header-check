<!DOCTYPE>
<html class="no-js" lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>.htaccess redirect link checker</title>
	<link rel="stylesheet" href="../foundation/css/foundation.css" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="styles/main.css" />
	<script src="foundation/js/vendor/modernizr.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.3.min.js"></script>

	<script type="text/javascript">
		$(window).load(function() {
			$("#spinner").fadeOut("slow");
		})
	</script>
</head>
<body>
	<div id="spinner" class="loader"></div>
	<div class="row">
		<div class="large-12 columns">
			<h1>.htaccess redirect link checker v1.0.0a</h1>
		</div>
	</div>
	<div class="row">
		<form method="post">
			<div class="large-12 columns">
				<label>URL list
					<textarea placeholder="Enter URLs" name="uriList" rows="20"><?php if ( is_array($_POST) && array_key_exists('uriList', $_POST) ) echo $_POST['uriList']; ?></textarea>
				</label>
			</div>
			<div class="large-12 columns">
				<button type="submit" class="button expand">Submit</button>
			</div>
		</form>
	</div>

	<div class="row">
		<div class="large-12 columns">
<?php
	require_once('includes.php');

	if ( is_array($_POST) && array_key_exists('uriList', $_POST) && $list = explode(PHP_EOL, $_POST['uriList']) ) {
		$proxy = explode(':', $proxy);
		array_walk($list, create_function('&$val', '$val = trim($val);'));
		echo "<table><thead><tr><th scope=\"column\">Origin</th><th scope=\"column\">Status</th><th scope=\"column\"><abbr title=\"ReDirect Count\">RDC</abbr></th><th scope=\"column\">Target</th><th scope=\"column\">Status</th></tr></thead><tbody>";

		foreach ( $list as $entry ) {
			$url = $entry;
			// prepend protocol (http & https allowed)
			if ( !preg_match('/^https?:\/\//i', $entry) ) {
				$url = 'http://' . $entry;
			}
			$result = [];
			if ( filter_var($url, FILTER_VALIDATE_URL) ) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
				curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]);
				curl_setopt($ch, CURLOPT_HEADER, TRUE);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLINFO_HTTP_CODE, TRUE);
				curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);

				$exec = curl_exec($ch);
				$info = curl_getinfo($ch);
				$info['http_code_highlight'] = $classes[substr($info['http_code'], 0, 1)];

				if ( $info['http_code'] >= 300 && $info['http_code'] < 400 ) {
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
					$exec = curl_exec($ch);
					$info2 = curl_getinfo($ch);
					$info2['http_code_highlight'] = $classes[substr($info2['http_code'], 0, 1)];
				} else {
					$info2 = $info;
				}

				curl_close($ch);

				$result[] = [
					'orig' => $entry,
					'dataOrigin' => $info2,
					'dataTarget' => $info,
					'errorMsg' => ''
				];

				echo ("
					<tr>
						<td>$entry</td>
						<td class=\"$info[http_code_highlight]\" style=\"text-align:center\">$info[http_code] {$codes[$info['http_code']]}</td>
						<td>$info2[redirect_count] <a href=\"detail.php?url=" . rawurlencode($entry) . "\" target=\"_blank\"><i class=\"fa fa-info-circle fa-lg\"></i></a></td>
						<td><a href=\"$info2[url]\" target=\"_blank\">$info2[url]</a></td>
						<td class=\"$info2[http_code_highlight]\" style=\"text-align:center\">$info2[http_code] {$codes[$info2['http_code']]}</td>
					</tr>
				");
			} elseif ( empty($entry) ) {
				continue;
			} else {
				$result[] = [
					'orig' => $entry,
					'data' => FALSE,
					'errorMsg' => 'URL validation failed!'
				];

				echo ("
					<tr>
						<td>$entry</td>
						<td colspan=\"4\" class=\"alert\">URL validation failed!</td>
					</tr>
				");
			}
		}

		echo "</tbody></table>";

		$requestTime = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2);
		$seconds = $requestTime / 1000;
		$rSeconds = $seconds % 60;
		$minutes = floor($seconds / 60);
		echo "<hr /><p>Request time: {$minutes}m {$rSeconds}s [ {$seconds}s ]</p>";
	}
?>
		</div>
	</div>
</body>
</html>