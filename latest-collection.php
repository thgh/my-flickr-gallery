<?php

$config = require(__DIR__ . '/bootstrap.php');

$collections = getCollections()['collections']['collection'];
$collectionTitles = array_column($collections, 'title');

$flattened = flattenToPhotosets($collections);
$years = [];
foreach ($flattened as $photoset) {
  $years[] = intval(substr($photoset['title'], 0, 4));
}
$years = array_unique($years);
rsort($years);

return [
  'year' => max($years),
  'years' => $years,
  'collectionId' => 0,
  'collectionTitles' => $collectionTitles
];
