<?php

require_once 'jsmin-1.1.1.php';

// Enable advanced caching control using ETag headers
define ('_STRATEGY_ETAG_', true);

// Enable advanced caching control using Modified headers
define ('_STRATEGY_MODIFIED_', false);

// Enable caching the combined files on the server
define ('_STRATEGY_CACHE_', false);

// Enable javascript optimisation
define ('_ENABLE_JSMINIFY_', false);

// Determine the type
$type = $_GET['type'];

if ($type != 'js' && $type != 'css') {
	header ("HTTP/1.0 403 Forbidden");
	exit;
}

//disable zlib compression for it collides with combine logic
$zlibCompression = ini_get('zlib.output_compression');
if ($zlibCompression) {
    ini_set("zlib.output_compression", 0);
}

// Build our paths
$base = realpath(dirname(dirname(__FILE__)));
$cacheFolder = realpath(dirname(dirname(dirname($base))) . '/var/cache');

// Check if files exist
$elements = explode(',', $_GET['files']);
$files = array();

while (list(,$element) = each($elements)) {
	$path = realpath($base . '/' . $element);

	$dirSep = preg_quote(DIRECTORY_SEPARATOR, '#');
	if (preg_match("#^{$dirSep}(js|css){$dirSep}#", substr($path, strlen($base)))) {
		$files[] = $path;
	}
	else {
	   header ("HTTP/1.0 403 Forbidden");
	   exit;
	}
}

// Determine supported compression methods
if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
	$encoding = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ? 'gzip' :
			(strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') ? 'deflate' : 'none');
} else {
	$encoding = 'none';
}

if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
	preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
	$version = floatval($matches[1]);

	if ($version < 6)
		$encoding = 'none';

	if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1'))
		$encoding = 'none';
}

// Determine the date of the last modification
$modified = 0;
while (list(,$file) = each($files)) {
	$modified = max($modified, filemtime($file));
}

// Determine the unique hash
$hash = $modified . '_' . md5(implode('', $files)) . '_' . $encoding . '.' . $type;

// Check if the cache version of the browser is still valid
if (_STRATEGY_MODIFIED_) {
	header ('Last-Modified: ' . gmdate("D, d M Y H:i:s",  $modified) . ' GMT');

	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) <= $modified) {
		header ("HTTP/1.0 304 Not Modified");
		header ('Content-Length: 0');
		exit;
	}
}

if (_STRATEGY_ETAG_) {
	header ("ETag: \"" . $hash . "\"");

	if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && ($_SERVER['HTTP_IF_NONE_MATCH'] == $hash || $_SERVER['HTTP_IF_NONE_MATCH'] == '"' . $hash . '"')) {
		header ("HTTP/1.0 304 Not Modified");
		header ('Content-Length: 0');
		exit;
	}
}

$cacheFile = $cacheFolder . '/combine_' . $hash;
if (_STRATEGY_CACHE_ && file_exists($cacheFile)) {
	$contents = file_get_contents($cacheFile);
}
else {
	$contents = '';
	reset ($files);
	while (list(,$file) = each($files)) {
      if (_ENABLE_JSMINIFY_ && preg_match("/\.js$/", $file)) {
         // Minify JS
		   $contents .= "\n\n" . JSMin::minify(file_get_contents($file), basename($file));
      } else {
		   $contents .= "\n\n" . file_get_contents($file);
      }
	}

	if (_STRATEGY_CACHE_ && is_writable($cacheFolder)) {
		if ($fp = fopen($cacheFile, 'w+b')) {
			fwrite($fp, $contents);
			fclose($fp);
		}
	}
}

if ($encoding != 'none' && !ob_list_handlers()) {
    if ($encoding == 'gzip') {
        ob_start("ob_gzhandler");
        header ("Content-Encoding: " . $encoding);
    }
}

header ("Content-Type: text/" . ($type == 'js' ? 'javascript' : $type));
echo $contents;

?>
