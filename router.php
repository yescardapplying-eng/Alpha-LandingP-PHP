<?php
/**
 * ============================================================================
 * PHP BUILT-IN SERVER ROUTER
 * Routes API endpoints cleanly and serves static frontend assets
 * Run: php -S localhost:8000 router.php
 * ============================================================================
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

// Route API endpoints
$apiRoutes = [
    '/api/track-click'   => __DIR__ . '/api/track-click.php',
    '/api/track-click.php' => __DIR__ . '/api/track-click.php',
    '/api/track-step'    => __DIR__ . '/api/track-step.php',
    '/api/track-step.php'  => __DIR__ . '/api/track-step.php',
    '/api/submit-lead'   => __DIR__ . '/api/submit-lead.php',
    '/api/submit-lead.php' => __DIR__ . '/api/submit-lead.php',
    '/api/get-lead'      => __DIR__ . '/api/get-lead.php',
    '/api/get-lead.php'    => __DIR__ . '/api/get-lead.php',
];

if (isset($apiRoutes[$uri])) {
    require $apiRoutes[$uri];
    exit;
}

// Block direct access to hidden files and internal scripts
$blockedPatterns = [
    '/^\/\./',                 // Hidden files (.env, .git)
    '/^\/config(\/|$)/i',       // Internal configs
    '/^\/database(\/|$)/i',     // Internal migrations & SQL
    '/^\/helpers(\/|$)/i',      // Helper scripts
    '/^\/test.*\.php$/i',       // Test scripts
    '/^\/composer.*$/i',
    '/^\/cacert\.pem$/i'
];

foreach ($blockedPatterns as $pattern) {
    if (preg_match($pattern, $uri)) {
        http_response_code(403);
        echo "403 Forbidden";
        exit;
    }
}

// Default root to index.html
if ($uri === '/' || $uri === '') {
    $uri = '/index.html';
}

$filePath = __DIR__ . $uri;

// If file exists, serve it with proper MIME type
if (file_exists($filePath) && !is_dir($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimes = [
        'html' => 'text/html; charset=UTF-8',
        'css'  => 'text/css; charset=UTF-8',
        'js'   => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'webp' => 'image/webp'
    ];

    if (isset($mimes[$ext])) {
        header("Content-Type: {$mimes[$ext]}");
    }
    readfile($filePath);
    exit;
}

// 404 for unmatched routes
http_response_code(404);
echo "404 Not Found";
exit;
