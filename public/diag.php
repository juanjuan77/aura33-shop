<?php
$root = dirname(__DIR__);
echo '<pre>';
echo 'Document root: ' . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo 'Script: ' . __FILE__ . "\n";
echo 'App root: ' . $root . "\n\n";
echo '.env exists: ' . (file_exists($root . '/.env') ? 'YES' : 'NO') . "\n";
echo 'vendor exists: ' . (is_dir($root . '/vendor') ? 'YES' : 'NO') . "\n";
echo 'bootstrap/cache writable: ' . (is_writable($root . '/bootstrap/cache') ? 'YES' : 'NO') . "\n";
echo 'storage writable: ' . (is_writable($root . '/storage') ? 'YES' : 'NO') . "\n\n";
echo 'PHP binary: ' . PHP_BINARY . "\n";
echo 'PHP version: ' . PHP_VERSION . "\n";
echo '</pre>';
