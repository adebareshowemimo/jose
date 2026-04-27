<?php
/**
 * Temporary cache-clear script.
 * DELETE THIS FILE after use.
 */

// Simple secret token to prevent public abuse
$secret = 'jcl-clear-2026';
if (($_GET['token'] ?? '') !== $secret) {
    http_response_code(403);
    die('Forbidden. Use ?token=jcl-clear-2026');
}

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$results = [];

$commands = [
    'view:clear',
    'cache:clear',
    'config:clear',
    'route:clear',
];

foreach ($commands as $cmd) {
    $exitCode = $kernel->call($cmd);
    $results[$cmd] = $exitCode === 0 ? 'OK' : 'FAILED (code ' . $exitCode . ')';
}

echo '<pre style="font-family:monospace;font-size:14px;padding:20px;">';
echo "=== Cache Clear Results ===\n\n";
foreach ($results as $cmd => $result) {
    echo str_pad("php artisan $cmd", 40) . " -> $result\n";
}
echo "\n✅ Done. DELETE this file from your server now!\n";
echo '</pre>';
