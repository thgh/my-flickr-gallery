<?php

// Load config file, see config.example.php
if (!file_exists(__DIR__ . '/config.php')) {
  exit('Create config.php with credentials' . __DIR__ . '/config.php');
}

require_once(__DIR__ . '/helpers.php');

return require(__DIR__ . '/config.php');
