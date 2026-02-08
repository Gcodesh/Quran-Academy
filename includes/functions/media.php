<?php

if (!class_exists('App\Services\MediaService')) {
    require_once __DIR__ . '/../../src/bootstrap.php';
}

class MediaHandler extends App\Services\MediaService {
    // Proxy for backward compatibility
}
