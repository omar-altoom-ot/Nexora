<?php 

class ProductRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }


    public function findAll():array
    {
        $sql = "SELECT p.*, c.name AS category_name
            FROM products AS p
            INNER JOIN categories AS c
                ON p.category_id = c.id
            ORDER BY p.id DESC";

        $stmt = $this->db->run_query($sql);
        
        return $stmt->fetchAll();

    }

}