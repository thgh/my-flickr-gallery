<?php

$files = glob('*.cache');
foreach($files as $file){
  if(is_file($file)) {
    unlink($file); // delete file
  }
}

$latest = require('latest-collection.php');

assert($latest['year'] === 2018);

assert(file_exists('z-collections.cache'));


// Get default page content
ob_start();
require('cached-photos.php');
$html = ob_get_contents();
ob_end_clean();

assert(strpos($html, '<img class="photo lazyload" data-src="https://farm') > 1);

echo 'Success!';
