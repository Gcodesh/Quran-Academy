<?php
namespace App\Services;

class MediaService {
    /**
     * Parse a media URL and return its provider and clean ID/Source
     */
    public static function parse($url) {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return [
                'provider' => 'youtube',
                'id' => self::getYoutubeId($url),
                'embed_url' => self::getYoutubeEmbed($url)
            ];
        }
        
        if (strpos($url, 'vimeo.com') !== false) {
            return [
                'provider' => 'vimeo',
                'id' => self::getVimeoId($url),
                'embed_url' => "https://player.vimeo.com/video/" . self::getVimeoId($url)
            ];
        }

        // Default to local/other
        return [
            'provider' => 'local',
            'id' => basename($url),
            'embed_url' => $url
        ];
    }

    private static function getYoutubeId($url) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $match[1] ?? null;
    }

    private static function getYoutubeEmbed($url) {
        $id = self::getYoutubeId($url);
        return $id ? "https://www.youtube.com/embed/" . $id : $url;
    }

    private static function getVimeoId($url) {
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Handle file uploads (Images, PDFs, etc.)
     */
    public static function upload($file, $folder = 'others') {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $base_dir = __DIR__ . '/../../uploads/' . $folder . '/';
        if (!is_dir($base_dir)) {
            mkdir($base_dir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('media_', true) . '.' . $ext;
        $target = $base_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return 'uploads/' . $folder . '/' . $filename;
        }

        return null;
    }

    /**
     * Returns the HTML player for the given media type and URL
     */
    public static function renderPlayer($provider, $url, $type = 'video') {
        $parsed = self::parse($url);
        
        if ($parsed['provider'] === 'youtube' || $parsed['provider'] === 'vimeo') {
            return '<iframe src="' . $parsed['embed_url'] . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%; aspect-ratio:16/9; border-radius:12px;"></iframe>';
        }

        if ($type === 'video') {
            return '<video controls style="width:100%; border-radius:12px;"><source src="' . $url . '" type="video/mp4">Your browser does not support the video tag.</video>';
        }

        if ($type === 'audio') {
            return '<audio controls style="width:100%;"><source src="' . $url . '" type="audio/mpeg">Your browser does not support the audio element.</audio>';
        }

        if ($type === 'pdf') {
            return '<iframe src="' . $url . '" style="width:100%; height:600px; border:none; border-radius:12px;"></iframe>';
        }

        return '<p>Media type not supported or invalid URL.</p>';
    }
}
