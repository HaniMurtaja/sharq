<?php

require __DIR__.'/vendor/autoload.php';

echo "1. Autoloader loaded successfully\n";

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    echo "2. Bootstrap app loaded successfully\n";
    echo "3. Laravel version: " . $app->version() . "\n";
} catch (Exception $e) {
    echo "ERROR in bootstrap: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    echo "4. Checking if FilesystemServiceProvider is registered...\n";
    $providers = $app->getLoadedProviders();
    if (isset($providers['Illuminate\Filesystem\FilesystemServiceProvider'])) {
        echo "   ✓ FilesystemServiceProvider is loaded\n";
    } else {
        echo "   ✗ FilesystemServiceProvider is NOT loaded\n";
    }
    
    echo "5. Trying to resolve 'files' service...\n";
    $files = $app->make('files');
    echo "   ✓ Files service resolved successfully\n";
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
}
