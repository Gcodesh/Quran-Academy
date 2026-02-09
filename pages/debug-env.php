<?php
// Debug script to check environment variables in Vercel
echo "<h1>Environment Variables Check</h1>";
echo "<pre>";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NOT SET') . "\n";
echo "DB_PORT: " . (getenv('DB_PORT') ?: 'NOT SET') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: 'NOT SET') . "\n";
echo "DB_PASS: " . (getenv('DB_PASS') ? '***SET***' : 'NOT SET') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'NOT SET') . "\n";
echo "</pre>";

echo "<h2>All Environment Variables:</h2>";
echo "<pre>";
print_r(getenv());
echo "</pre>";
