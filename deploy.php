<?php
// One-off deploy script: pulls latest commit from GitHub and deploys to this directory.
// DELETE THIS FILE FROM THE SERVER IMMEDIATELY AFTER USE.
header('Content-Type: text/plain');

$repoZipUrl = 'https://github.com/juanjuan77/aura33-shop/archive/refs/heads/main.zip';
$deployPath = __DIR__; // this script must live in public_html
$tmpZip = sys_get_temp_dir() . '/aura33-shop-deploy.zip';
$tmpExtract = sys_get_temp_dir() . '/aura33-shop-deploy-' . time();

function rcopy($src, $dst) {
    if (is_dir($src)) {
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            rcopy("$src/$item", "$dst/$item");
        }
    } else {
        copy($src, $dst);
    }
}

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = "$dir/$item";
        is_dir($path) ? rrmdir($path) : unlink($path);
    }
    rmdir($dir);
}

echo "1. Downloading $repoZipUrl ...\n";
$ch = curl_init($repoZipUrl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$zipData = curl_exec($ch);
if ($zipData === false) {
    die("curl error: " . curl_error($ch) . "\n");
}
curl_close($ch);
file_put_contents($tmpZip, $zipData);
echo "   downloaded " . filesize($tmpZip) . " bytes\n";

echo "2. Extracting...\n";
$zip = new ZipArchive();
if ($zip->open($tmpZip) !== true) {
    die("Failed to open zip\n");
}
mkdir($tmpExtract, 0755, true);
$zip->extractTo($tmpExtract);
$zip->close();

$extractedRoot = glob("$tmpExtract/*", GLOB_ONLYDIR)[0] ?? null;
if (!$extractedRoot) {
    die("Could not find extracted folder\n");
}
echo "   extracted to $extractedRoot\n";

echo "3. Copying files into $deployPath ...\n";
rcopy($extractedRoot, $deployPath);

echo "4. Flattening public/ into root...\n";
$publicDir = "$deployPath/public";
if (is_dir($publicDir)) {
    rcopy($publicDir, $deployPath);
    rrmdir($publicDir);
}

echo "5. Fixing index.php paths...\n";
$indexPath = "$deployPath/index.php";
$contents = file_get_contents($indexPath);
$contents = str_replace("__DIR__.'/../storage/framework/maintenance.php'", "__DIR__.'/storage/framework/maintenance.php'", $contents);
$contents = str_replace("__DIR__.'/../vendor/autoload.php'", "__DIR__.'/vendor/autoload.php'", $contents);
$contents = str_replace("__DIR__.'/../bootstrap/app.php'", "__DIR__.'/bootstrap/app.php'", $contents);
file_put_contents($indexPath, $contents);

echo "6. Cleaning up temp files...\n";
unlink($tmpZip);
rrmdir($tmpExtract);

echo "\nDONE. Now:\n";
echo "- Make sure .env exists in $deployPath (it is NOT in the git repo)\n";
echo "- DELETE this deploy.php file from the server now\n";
echo "- Run composer install / artisan commands if needed (ask Claude how, since shell is disabled here)\n";
