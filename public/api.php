<?php
/**
 * EduWrite AI - Main API Entry Point
 * 
 * Routes requests to the correct API version endpoint.
 * URL format: /public/api.php?endpoint=documents&action=list&version=v1
 */

$version = $_GET['version'] ?? 'v1';
$endpoint = $_GET['endpoint'] ?? '';

// Validate version
$allowedVersions = ['v1'];
if (!in_array($version, $allowedVersions)) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid API version']);
    exit;
}

// Validate endpoint
$allowedEndpoints = [
    'auth', 'documents', 'classes', 'assignments', 'ai',
    'notifications', 'analytics', 'admin', 'teacher', 'import-export'
];

if (empty($endpoint)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'data' => [
            'name' => 'EduWrite AI API',
            'version' => $version,
            'endpoints' => $allowedEndpoints
        ]
    ]);
    exit;
}

if (!in_array($endpoint, $allowedEndpoints)) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Unknown endpoint: ' . $endpoint]);
    exit;
}

$endpointFile = dirname(__DIR__) . "/api/{$version}/{$endpoint}.php";

if (!file_exists($endpointFile)) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Endpoint not implemented']);
    exit;
}

require_once $endpointFile;
