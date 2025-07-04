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

echo "4. Checking what's actually registered...\n";

// Check if services are bound, not just providers loaded
$services = ['files', 'filesystem', 'cache', 'cache.store', 'view', 'blade.compiler'];

foreach ($services as $service) {
    try {
        $resolved = $app->make($service);
        echo "   ✓ Service '$service' resolved successfully\n";
    } catch (Exception $e) {
        echo "   ✗ Service '$service' failed: " . $e->getMessage() . "\n";
    }
}

echo "5. Checking all loaded providers:\n";
$providers = $app->getLoadedProviders();
foreach ($providers as $provider => $loaded) {
    if (strpos($provider, 'Filesystem') !== false || 
        strpos($provider, 'View') !== false || 
        strpos($provider, 'Cache') !== false) {
        echo "   - $provider\n";
    }
}

echo "6. Total providers loaded: " . count($providers) . "\n";
