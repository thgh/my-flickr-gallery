<?php

include 'mykeys.php';

// Return current photo gallery
echo file_get_contents('cached-photos.html');

flush();

$timingcache = 10;
if( get_field('caching_on' , 10) ) {

 //echo 'ja, staat aan 2';
 $timingcache = 3600*24;

}
else {
 $timingcache = 10;
}


if (filemtime('cached-photos.html') < time() - $timingcache) {

ob_start();

  // generate
function getPhotoset ($set_id) {
  $json = file_get_contents('https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&user_id='.$myuserid.'&photoset_id=' . $set_id . '&format=json&api_key='.$myapikey);
  return json_decode(substr($json, 14, -1), true);
}

function getCollections ($set_id) {
  $json = file_get_contents('https://api.flickr.com/services/rest/?method=flickr.collections.getTree&user_id='.$myuserid.'&format=json&api_key='.$myapikey);
  return json_decode(substr($json, 14, -1), true);
}

// only for the date
function getPhotosetInfo ($set_id) {
  $json = file_get_contents('https://api.flickr.com/services/rest/?method=flickr.photosets.getInfo&user_id='.$myuserid.'&photoset_id=' . $set_id . '&format=json&api_key='.$myapikey);
  return json_decode(substr($json, 14, -1), true);
}

// Collection selection
$root = $_GET['root'] ?: 0;
$rootCollection = getCollections()['collections']['collection'][$root];

// Title selection
$title = $_GET['title'];
if ($title) {
  foreach ($rootCollection['collection'] as $collection) {
    if ($collection['title'] == $title) {
      $selected = $collection;
    }
  }
} else {
  $selected = $rootCollection['collection'][0];
}


?>

  <?php $setnumber = 0; foreach ($selected['set'] as $photoset) { ?>

      <?php $photos = getPhotoset($photoset['id'])['photoset']['photo'];
          $timestamp = getPhotosetInfo ($photoset['id'])['photoset']['date_create'];
      ?>
      <div class="photoitem">
          <div class="row">
            <div class="col-12">
              <h2><?php echo $photoset['title']; ?></h2>
              <h3><?php echo getPhotoset($photoset['id'])['photoset']['total']; ?> photos by Johan Vanbrabant / <?php echo gmdate("F Y", $timestamp); ?></h3>
              <a href="#btnalbum-<?php echo $setnumber; ?>" class="viewallphotosbtn">View all photos</a>
            </div>
          </div>


    <div class="row">

      <?php $howmuch = 0; foreach ($photos as $photo) {

if($howmuch == 3 ){ echo "</div><div class='closedalbum closedalbumnr".$setnumber."'><div class='row'>"; }
        ?>
        <div class="col-xs-12 col-sm-4">
<?php echo '<a href="https://farm'.$photo['farm'].'.staticflickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'_b.jpg" class="fresco ratio zoomeffect"
data-fresco-group="album-'.$setnumber.'"
data-fresco-group-options="loop:true"
data-fresco-caption=""><img class="photo lazyload" data-src="https://farm'.$photo['farm'].'.staticflickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'_n.jpg"></a>'; ?>
        </div>
      <?php $howmuch++; } ?>

      </div>
    </div>
        </div>
  <?php $setnumber++; } ?>


  <?php

$html = ob_get_contents();
ob_end_clean();


  file_put_contents('cached-photos.html', $html);
}
