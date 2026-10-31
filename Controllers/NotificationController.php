<?php
class NotificationController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // Get ALL notifications (newest first)
    public function getAll(): array
    {
        return $this->db->run_query(
            'SELECT * FROM notifications ORDER BY created_at DESC'
        )->fetchAll();
    }

    // Get only unread (for the sidebar badge)
    public function getUnread(): array
    {
        return $this->db->run_query(
            'SELECT * FROM notifications WHERE is_read = 0'
        )->fetchAll();
    }

    // Mark one as read
    public function markAsRead(int $id): bool
    {
        return $this->db->run_query(
            'UPDATE notifications SET is_read = 1 WHERE id = ?',
            [$id]
        )->rowCount() > 0;
    }

    // Mark all as read (optional button)
    public function markAllAsRead(): bool
    {
        return $this->db->run_query('UPDATE notifications SET is_read = 1 WHERE is_read = 0')->rowCount() > 0;
    }

    // Get notifications with pagination
    public function getPaginated(int $limit = 10, int $offset = 0): array
    {
        // Force them to be integers to prevent SQL injection
        $limit = (int) max(1, min($limit, 100));
        $offset = (int) max(0, $offset);

        // Inject the integers directly into the string
        $sql = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";

        return $this->db->run_query($sql)->fetchAll();
    }

    // Count total notifications (ADDED THIS METHOD)
    public function countAll(): int
    {
        return (int) $this->db->run_query('SELECT COUNT(*) FROM notifications')->fetchColumn();
    }
}