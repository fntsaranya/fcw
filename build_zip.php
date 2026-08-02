<?php
$zip = new ZipArchive();
if ($zip->open('fcw_php_hostinger_release_v27.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Cannot create zip file");
}

$dirs = ['config', 'src', 'templates', 'static', 'storage', 'vendor'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
        $path = $file->getPathname();
        $zipPath = str_replace('\\', '/', $path);
        
        if ($file->isDir()) {
            $zip->addEmptyDir($zipPath);
        } else {
            $zip->addFile($path, $zipPath);
        }
    }
}

$files = ['.env', '.env.example', '.gitignore', '.htaccess', 'composer.json', 'composer.lock', 'HOSTINGER_SHARED_PLAN_DEPLOYMENT.md', 'index.php', 'LICENSE', 'README.md', 'update_db.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        $zip->addFile($file, $file);
    }
}

$zip->close();
echo "Zip built successfully.\n";
