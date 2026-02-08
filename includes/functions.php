<?php
/**
 * Helper function to display images safely with a fallback
 */
function safeImage($url, $alt, $fallback = '../assets/images/placeholder.jpg', $class = '', $srcset = '', $width = '', $height = '') {
    // Sanitize output
    $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $alt = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
    $class = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
    $srcset = htmlspecialchars($srcset, ENT_QUOTES, 'UTF-8');

    // Add 'lazy' and 'skeleton' classes for JS observer and CSS shimmer
    $combinedClass = trim("lazy skeleton " . $class);

    $imgTag = '<img src="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 ' . ($width ?: 1) . ' ' . ($height ?: 1) . '\'%3E%3C/svg%3E"'
            . ' data-src="' . $url . '"'
            . ' alt="' . $alt . '"'
            . ($combinedClass ? ' class="' . $combinedClass . '"' : '')
            . ($srcset ? ' data-srcset="' . $srcset . '"' : '')
            . ($width ? ' width="' . $width . '"' : '')
            . ($height ? ' height="' . $height . '"' : '')
            . ' onerror="this.src=\'' . $fallback . '\'"'
            . ' loading="lazy">';
    return $imgTag;
}
?>
