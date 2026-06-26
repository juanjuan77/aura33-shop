<?php
header('Content-Type: text/plain');
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'yes' : 'no') . "\n";
echo "curl extension: " . (extension_loaded('curl') ? 'yes' : 'no') . "\n";
echo "ZipArchive: " . (class_exists('ZipArchive') ? 'yes' : 'no') . "\n";
echo "disk_free_space /home: " . @disk_free_space('/home') . "\n";
