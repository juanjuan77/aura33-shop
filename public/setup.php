<?php
// Borrar este archivo después de ejecutarlo
if ($_GET['token'] ?? '' !== 'aura33setup2026') {
    die('No autorizado');
}

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$commands = [
    'key:generate'    => [],
    'migrate'         => ['--force' => true],
    'storage:link'    => [],
    'config:cache'    => [],
    'route:cache'     => [],
    'view:cache'      => [],
];

echo '<pre style="font-family:monospace; padding:20px;">';
foreach ($commands as $cmd => $params) {
    echo ">>> php artisan $cmd\n";
    $status = $kernel->call($cmd, $params);
    echo $kernel->output();
    echo "Exit: $status\n\n";
}
echo '</pre>';
echo '<p style="color:red;font-weight:bold;">⚠️ BORRÁ este archivo: public/setup.php</p>';
