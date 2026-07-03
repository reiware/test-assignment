<?php

return [
    'disk' => env('FILES_DISK', 'local'),
    'ttl_hours' => (int) env('FILE_TTL_HOURS', 24),
    'max_upload_size_kb' => (int) env('FILE_MAX_UPLOAD_SIZE_KB', 10240),
    'deletion_email' => env('FILE_DELETION_EMAIL', 'admin@example.com'),
    'allowed_extensions' => ['pdf', 'docx'],
];
