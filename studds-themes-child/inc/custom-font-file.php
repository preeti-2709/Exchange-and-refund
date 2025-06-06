<?php

function allow_custom_mime_types($mimes) {
    $mimes['ttf'] = 'font/ttf'; // Allow .ttf font files
    $mimes['TTF'] = 'font/ttf'; // Allow .ttf font files
    return $mimes;
}
add_filter('upload_mimes', 'allow_custom_mime_types');
