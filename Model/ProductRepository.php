<?php

class ProductRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }


    public function findAll(): array
    {

        $sql = "SELECT
            p.*,
            c.name AS category_name,
            (
                SELECT pi.image
                FROM product_images AS pi
                WHERE pi.product_id = p.id
                ORDER BY pi.id ASC
                LIMIT 1
            ) AS image_path
        FROM products AS p
        INNER JOIN categories AS c
            ON p.category_id = c.id
        ORDER BY p.id DESC";


        $stmt = $this->db->run_query($sql);

        return $stmt->fetchAll();

    }


    public function create(array $data): int
    {
        $sql = "INSERT INTO products
                (category_id, name, description, price, stock_quantity)
            VALUES
                (:category_id, :name, :description, :price, :stock_quantity)";

        $this->db->run_query($sql, [
            'category_id' => (int) $data['category_id'],
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock_quantity' => (int) $data['stock_quantity']
        ]);

        return (int) $this->db
            ->run_query("SELECT LAST_INSERT_ID()")
            ->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT p.*, c.name AS category_name
            FROM products AS p
            INNER JOIN categories AS c
                ON p.category_id = c.id
            WHERE p.id = :id
            LIMIT 1";

        $stmt = $this->db->run_query($sql, [
            'id' => $id
        ]);

        $product = $stmt->fetch();

        return $product === false ? null : $product;
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE products
            SET
                category_id = :category_id,
                name = :name,
                description = :description,
                price = :price,
                stock_quantity = :stock_quantity
            WHERE id = :id";

        $this->db->run_query($sql, [
            'id' => $id,
            'category_id' => (int) $data['category_id'],
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock_quantity' => (int) $data['stock_quantity']
        ]);
    }


    public function deactivate(int $id): bool
    {
        $sql = "UPDATE products
            SET is_active = 0
            WHERE id = :id
              AND is_active = 1";

        $stmt = $this->db->run_query($sql, [
            'id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }


    public function activate(int $id): bool
    {
        $sql = "UPDATE products
            SET is_active = 1
            WHERE id = :id
              AND is_active = 0";

        $stmt = $this->db->run_query($sql, [
            'id' => $id
        ]);

        return $stmt->rowCount() === 1;
    }
    public function findPage(
    int $limit,
    int $offset,
    string $search = '',
    ?int $categoryId = null,
    string $status = 'all'
): array {

    $limit = max(1, min(50, $limit));
    $offset = max(0, $offset);

    [$where, $params] = $this->buildAdminFilters(
        $search,
        $categoryId,
        $status
    );

    $sql = "SELECT
                p.*,
                c.name AS category_name,
                (
                    SELECT pi.image
                    FROM product_images AS pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.id ASC
                    LIMIT 1
                ) AS image_path
            FROM products AS p
            INNER JOIN categories AS c
                ON c.id = p.category_id
            {$where}
            ORDER BY p.id DESC
            LIMIT {$limit} OFFSET {$offset}";

    return $this->db
        ->run_query($sql, $params)
        ->fetchAll(PDO::FETCH_ASSOC);
}


public function getAdminStats(): array
{
    $sql = "SELECT
                COUNT(*) AS total,
                COALESCE(SUM(is_active = 1), 0) AS active,
                COALESCE(SUM(
                    is_active = 1
                    AND stock_quantity BETWEEN 1 AND 5
                ), 0) AS low_stock,
                COALESCE(SUM(
                    is_active = 1
                    AND stock_quantity = 0
                ), 0) AS out_of_stock
            FROM products";

    return $this->db
        ->run_query($sql)
        ->fetch(PDO::FETCH_ASSOC);
}


private function buildAdminFilters(
    string $search,
    ?int $categoryId,
    string $status
): array {

    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = "p.name LIKE :search";
        $params['search'] = '%' . $search . '%';
    }

    if ($categoryId !== null) {
        $conditions[] = "p.category_id = :category_id";
        $params['category_id'] = $categoryId;
    }

    if ($status === 'active') {
        $conditions[] = "p.is_active = 1";
    } elseif ($status === 'inactive') {
        $conditions[] = "p.is_active = 0";
    }

    $where = $conditions === []
        ? ''
        : 'WHERE ' . implode(' AND ', $conditions);

    return [$where, $params];
}


public function countFilteredProducts(
    string $search = '',
    ?int $categoryId = null,
    string $status = 'all'
): int {

    [$where, $params] = $this->buildAdminFilters(
        $search,
        $categoryId,
        $status
    );

    $sql = "SELECT COUNT(*)
            FROM products AS p
            {$where}";

    return (int) $this->db
        ->run_query($sql, $params)
        ->fetchColumn();
}


}