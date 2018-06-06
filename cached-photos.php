<?php

// Load config file, see config.example.php
if (!file_exists(__DIR__ . '/config.php')) {
  exit('Create config.php with credentials' . __DIR__ . '/config.php');
}
$config = require(__DIR__ . '/config.php');


// Parse parameters
$collectionId = @$_POST['collectionId'];
$year = @$_POST['year'];

if (!$collectionId) {
  $latest = require(__DIR__ . '/latest-collection.php');
  $collectionId = $latest['collectionId'];
}
if (!$year) {
  $latest = $latest ?: require(__DIR__ . '/latest-collection.php');
  $year = $latest['year'];
}
//var_dump([$year, $collectionId]);
// Get cached version if available and otherwise generate fresh
$html = cacheGet('photos-' . $collectionId . '-' . $year);

if (!$html) {
  ob_start();
  require('fresh-photos.php');
  $html = ob_get_contents();
  ob_end_clean();
  cacheSet('photos-' . $collectionId . '-' . $year, $html);
}
echo $html;
// Caching helpers
function cacheHas ($path) {
  global $config;
  return $config['cache'] && file_exists(__DIR__ . '/z-' . $path . '.cache') && filemtime(__DIR__ . '/z-' . $path . '.cache') > time() - $config['cache'];
}
function cacheGet ($path) {
  return cacheHas($path) ? file_get_contents(__DIR__ . '/z-' . $path . '.cache') : '';
}
function cacheSet ($path, $data) {
  global $config;
  return $config['cache'] ? file_put_contents(__DIR__ . '/z-' . $path . '.cache', $data) : 0;
}


?>
