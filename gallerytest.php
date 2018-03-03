<!doctype html>
<html>
<head>
  <meta charset='utf8'>
  <title>NTAB Test</title>
  <style>
    .col-sm-4 {
      float: left;
      width: 15px;
      height: 10px;
      margin-right: 5px;
      background: #ccc;
    }
  </style>
</head>

<body>
  <form>
    <select id="photodayevent">
      <option value="0">Day events in Belgium</option>
      <option value="1">UK trips</option>
    </select>
    <select id="photoyearevent">
      <option value="2017">2017</option>
      <option value="2016">2016</option>
      <option value="2015">2015</option>
      <option value="2014">2014</option>
      <option value="2013">2013</option>
      <option value="2012">2012</option>
      <option value="2011">2011</option>
    </select>
  </form>
  <div id="photo-html"><?php require('wp-content/themes/ntab/flickr-gallery/cached-photos.php') ?></div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
<script>
  $('#photodayevent, #photoyearevent').on('change', function () {
    var collectionId = $('#photodayevent').val()
    var year = $('#photoyearevent').val()

    $.post('/wp-content/themes/ntab/flickr-gallery/cached-photos.php', { collectionId: collectionId, year: year }, function (html) {
      $('#photo-html').html(html)
    })
  })
</script>
after