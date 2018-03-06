<?php

// Expects $config, $collectionId, $year and cache helper functions
if (!isset($config) || !isset($collectionId) || !isset($year)) {
  exit('Cannot load fresh photos');
}

// Collection selection
$baseCollection = getCollections()['collections']['collection'][$collectionId];

// If the base collection has sub collections, join them together
if (isset($baseCollection['collection'])) {
  $photosets = [];
  foreach ($baseCollection['collection'] as $collection) {
    foreach ($collection['set'] as $photoset) {
      $photosets[] = $photoset;
    }
  }
} else {
  $photosets = $baseCollection['set'];
}

foreach ($photosets as $photosetNumber => $photoset) {
  if (strpos($photoset['title'], $year) === false) {
    continue;
  }
  $photos = getPhotoset($photoset['id'])['photoset']['photo'];
  $timestamp = getPhotosetInfo($photoset['id'])['photoset']['date_create'];
  ?>
  <div class="photoitem">
    <div class="row">
      <div class="col-12">
        <h2><?php echo $photoset['title']; ?></h2>
        <h3><?php echo getPhotoset($photoset['id'])['photoset']['total']; ?> photos by Johan Vanbrabant / <?php echo gmdate("F Y", $timestamp); ?></h3>
        <a href="#btnalbum-<?php echo $photosetNumber; ?>" class="viewallphotosbtn">View all photos</a>
      </div>
    </div>

    <div class="row">
      <?php
      foreach ($photos as $number => $photo) {
        // This will break when there is less than 3 photos
        if($number == 3 ){
          echo "</div><div class='closedalbum closedalbumnr".$photosetNumber."'><div class='row'>";
        }
        ?>
        <div class="col-xs-12 col-sm-4">
          <?php echo '<a href="https://farm'.$photo['farm'].'.staticflickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'_b.jpg" class="fresco ratio zoomeffect" data-fresco-group="album-'.$photosetNumber.'" data-fresco-group-options="loop:true" data-fresco-caption=""><img class="photo lazyload" data-src="https://farm'.$photo['farm'].'.staticflickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'_n.jpg"></a>'; ?>
        </div>
      <?php } ?>

      </div>
    </div>
  </div>
<?php
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
