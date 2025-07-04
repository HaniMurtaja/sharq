<?php

require __DIR__.'/vendor/autoload.php';

echo "Loading Laravel app...\n";
$app = require_once __DIR__.'/bootstrap/app.php';

echo "Laravel version: " . $app->version() . "\n";
echo "All loaded providers:\n";

$providers = $app->getLoadedProviders();
foreach ($providers as $provider => $loaded) {
    echo "  - $provider\n";
}

echo "\nTotal: " . count($providers) . " providers\n";
