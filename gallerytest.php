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
      <?php $latest = require('latest-collection.php'); ?>
      <?php foreach ($latest['collectionTitles'] as $id => $title) {?>
        <option value="<?php echo $id ?>"><?php echo htmlentities($title) ?></option>
      <?php } ?>
    </select>
    <select id="photoyearevent">
      <?php foreach ($latest['years'] as $year) {?>
        <option><?php echo $year ?></option>
      <?php } ?>
    </select>
  </form>
  <div id="photo-html"><?php require('cached-photos.php') ?></div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
<script>
  $('#photodayevent, #photoyearevent').on('change', function () {
    var collectionId = $('#photodayevent').val()
    var year = $('#photoyearevent').val()

    $.post('/cached-photos.php', { collectionId: collectionId, year: year }, function (html) {
      $('#photo-html').html(html)
    })
  })
</script>
after