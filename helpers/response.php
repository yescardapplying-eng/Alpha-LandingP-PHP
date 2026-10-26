<?php
/**
 * ============================================================================
 * HTTP RESPONSE & UTILITY HELPERS
 * ============================================================================
 */

// Enable CORS and Security Headers
function setStandardHeaders() {
    // If headers already sent, do nothing
    if (headers_sent()) {
        return;
    }

    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Send JSON response and terminate script
 *
 * @param int $statusCode
 * @param array $data
 */
function sendJsonResponse($statusCode, array $data) {
    setStandardHeaders();
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Parse and validate incoming JSON request body
 *
 * @return array
 */
function getJsonInput() {
    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        return [];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return [];
    }

    return $data;
}

/**
 * Safely clean and convert any input to a string
 *
 * @param mixed $input
 * @return string
 */
function cleanString($input) {
    if (is_null($input)) {
        return '';
    }
    if (is_array($input) || is_object($input)) {
        return json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    return trim(strip_tags((string)$input));
}

/**
 * Retrieve client IP address
 *
 * @return string
 */
function getClientIp() {
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '127.0.0.1';
}

/**
 * Retrieve User Agent string safely
 *
 * @return string
 */
function getUserAgent() {
    return cleanString($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
}

/**
 * Generate unique Lead Reference (e.g. SM-LEAD-8A3F2C1B)
 *
 * @return string
 */
function generateLeadReference() {
    $bytes = random_bytes(4);
    $hex = strtoupper(bin2hex($bytes));
    return "SM-LEAD-{$hex}";
}
