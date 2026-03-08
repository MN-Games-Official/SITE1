<?php
/**
 * EduWrite AI - Notifications API Endpoint
 *
 * Notification listing, marking as read, and deletion.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/NotificationService.php';

requireAuth();

$notificationService = new NotificationService();

routeAction([
    'list'          => 'handleList',
    'unread-count'  => 'handleUnreadCount',
    'mark-read'     => 'handleMarkRead',
    'mark-all-read' => 'handleMarkAllRead',
    'delete'        => 'handleDelete',
    'recent'        => 'handleRecent',
]);

/** GET - Get paginated notifications */
function handleList(): void {
    requireMethod('GET');

    global $notificationService;
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();

    $result = $notificationService->getForUser($user['id'], $page, $perPage);
    jsonSuccess($result);
}

/** GET - Get unread notification count */
function handleUnreadCount(): void {
    requireMethod('GET');

    global $notificationService;
    $user = getCurrentUser();

    $count = $notificationService->getUnreadCount($user['id']);
    jsonSuccess(['unread_count' => $count]);
}

/** POST - Mark a single notification as read */
function handleMarkRead(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $notificationService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Notification ID is required', 400);
    }

    $notificationService->markAsRead($id, $user['id']);
    jsonSuccess(null, 'Notification marked as read');
}

/** POST - Mark all notifications as read */
function handleMarkAllRead(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $notificationService;
    $user = getCurrentUser();

    $count = $notificationService->markAllAsRead($user['id']);
    jsonSuccess(['marked_count' => $count], 'All notifications marked as read');
}

/** DELETE - Delete a notification */
function handleDelete(): void {
    requireMethod('DELETE');
    validateWriteCSRF();

    global $notificationService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Notification ID is required', 400);
    }

    $notificationService->deleteNotification($id, $user['id']);
    jsonSuccess(null, 'Notification deleted');
}

/** GET - Get recent notifications for header dropdown */
function handleRecent(): void {
    requireMethod('GET');

    global $notificationService;
    $user = getCurrentUser();

    $limit = min(20, max(1, (int)($_GET['limit'] ?? 5)));
    $notifications = $notificationService->getRecentNotifications($user['id'], $limit);
    jsonSuccess(['notifications' => $notifications]);
}
