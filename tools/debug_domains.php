<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Host: " . request()->getHost() . "\n";
echo "Central Domains: " . implode(', ', config('tenancy.central_domains')) . "\n";
echo "Is Central: " . (in_array(request()->getHost(), config('tenancy.central_domains')) ? 'YES' : 'NO') . "\n";
