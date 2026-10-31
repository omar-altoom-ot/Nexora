<?php
class Db
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO(
            "mysql:host=localhost;dbname=nexora;charset=utf8mb4",
            "root",
            "",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }

    public function run_query(string $query, array $param = [])
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute($param);
        return $stmt;
    }


    // Database Transaction MOTASEM
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->db->commit();
    }


    public function rollBack(): bool
    {
        if (!$this->db->inTransaction()) {
            return false;
        }

        return $this->db->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->db->inTransaction();
    }


}
?>