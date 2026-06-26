<?php
header('Content-Type: text/plain');
$base = __DIR__;
$paths = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'bootstrap/cache',
    'public/storage',
];
foreach ($paths as $p) {
    $full = "$base/$p";
    if (!file_exists($full)) {
        echo "$p: MISSING\n";
        continue;
    }
    echo "$p: exists, " . (is_writable($full) ? 'writable' : 'NOT WRITABLE') . ", perms=" . substr(sprintf('%o', fileperms($full)), -4) . "\n";
}
echo "\nstorage/logs contents:\n";
foreach (glob("$base/storage/logs/*") as $f) {
    echo " - $f (" . filesize($f) . " bytes)\n";
}
