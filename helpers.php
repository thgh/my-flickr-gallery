<?php

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

// Flickr API helpers
function getCollections () {
  global $config;
  $json = cacheGet('collections');
  if (!$json) {
    $json = file_get_contents('https://api.flickr.com/services/rest/?method=flickr.collections.getTree&user_id=' . $config['user_id'] . '&format=json&api_key=' . $config['api_key']);
    cacheSet('collections', $json);
  }
  return json_decode(substr($json, 14, -1), true);
}
function getPhotoset ($set_id) {
  global $config;
  $json = cacheGet('photoset-' . $set_id);
  if (!$json) {
    $json = file_get_contents('https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&user_id=' . $config['user_id'] . '&photoset_id=' . $set_id . '&format=json&api_key=' . $config['api_key']);
    cacheSet('photoset-' . $set_id, $json);
  }
  return json_decode(substr($json, 14, -1), true);
}
// only for the date
function getPhotosetInfo ($set_id) {
  global $config;
  $json = cacheGet('photosetinfo-' . $set_id);
  if (!$json) {
    $json = file_get_contents('https://api.flickr.com/services/rest/?method=flickr.photosets.getInfo&user_id=' . $config['user_id'] . '&photoset_id=' . $set_id . '&format=json&api_key=' . $config['api_key']);
    cacheSet('photosetinfo-' . $set_id, $json);
  }
  return json_decode(substr($json, 14, -1), true);
}

// Loop over all photosets in all collections
function flattenToPhotosets ($baseCollections) {
  $photosets = [];
  foreach ($baseCollections as $baseCollection) {
    // If the base collection has sub collections, join them together
    if (isset($baseCollection['collection'])) {
      foreach ($baseCollection['collection'] as $collection) {
        foreach ($collection['set'] as $photoset) {
          $photosets[] = $photoset;
        }
      }
    } else {
      foreach ($baseCollection['set'] as $photoset) {
        $photosets[] = $photoset;
      }
    }
  }

  return $photosets;
}
