<?php
class NotificationController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // Get all unread notifications
    public function getUnread(): array
    {
        return $this->db->run_query(
            'SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC'
        )->fetchAll();
    }
}