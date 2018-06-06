<?php

$config = require(__DIR__ . '/bootstrap.php');

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
