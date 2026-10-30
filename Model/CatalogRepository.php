<?php
declare(strict_types=1);

require_once __DIR__ . '/../Config/DBconnection.php';

class CatalogRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }


    public function findActiveProducts(): array
    {
        $sql = "SELECT
                id,
                name,
                price,
                stock_quantity
            FROM products
            WHERE is_active = 1
            ORDER BY id DESC";

        $stmt = $this->db->run_query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function searchProducts(string $search): array
    {
        $search = trim($search);

        if ($search === '') {
            return $this->findActiveProducts();
        }

        $sql = "SELECT
                id,
                name,
                price,
                stock_quantity
            FROM products
            WHERE is_active = 1
            AND name LIKE :search
            ORDER BY id DESC";

        $stmt = $this->db->run_query($sql, [
            'search' => '%' . $search . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function findByCategory(int $categoryId): array
    {
        $sql = "SELECT
                p.id,
                p.name,
                p.price,
                p.stock_quantity,
                c.name AS category_name
            FROM products AS p
            INNER JOIN categories AS c
                ON p.category_id = c.id
            WHERE p.is_active = 1
              AND p.category_id = :category_id
            ORDER BY p.id DESC";

        $stmt = $this->db->run_query($sql, [
            'category_id' => $categoryId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function findByPriceRange(
        float $minPrice,
        float $maxPrice
    ): array {

        if ($minPrice < 0 || $maxPrice < $minPrice) {
            throw new InvalidArgumentException(
                'Invalid price range.'
            );
        }

        $sql = "SELECT
                id,
                name,
                price,
                stock_quantity
            FROM products
            WHERE is_active = 1
              AND price BETWEEN :min_price AND :max_price
            ORDER BY price ASC, id DESC";

        $stmt = $this->db->run_query($sql, [
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function findFilteredProducts(
        string $search = '',
        ?int $categoryId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        string $sort = 'newest',
        int $limit = 12,
        int $offset = 0
    ): array {

        // 1. Build filters using private method
        [$where, $params] = $this->buildFilters(
            $search,
            $categoryId,
            $minPrice,
            $maxPrice
        );

        // 2. Pagination validation
        $limit = max(1, min(50, $limit));
        $offset = max(0, $offset);

        // 3. Sorting
        $orderBy = match ($sort) {
            'price_asc' => 'p.price ASC, p.id DESC',
            'price_desc' => 'p.price DESC, p.id DESC',
            'name_asc' => 'p.name ASC, p.id DESC',
            'name_desc' => 'p.name DESC, p.id DESC',
            default => 'p.id DESC'
        };

        // 4. SQL Query
        
$sql = "SELECT
            p.id,
            p.name,
            p.description,
            p.price,
            p.stock_quantity,
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
        WHERE {$where}
        ORDER BY {$orderBy}
        LIMIT {$limit} OFFSET {$offset}";

        // 5. Execute Query
        $stmt = $this->db->run_query($sql, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function countFilteredProducts(
        string $search = '',
        ?int $categoryId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): int {

        [$where, $params] = $this->buildFilters(
            $search,
            $categoryId,
            $minPrice,
            $maxPrice
        );

        $sql = "SELECT COUNT(*)
            FROM products AS p
            WHERE {$where}";

        $stmt = $this->db->run_query($sql, $params);

        return (int) $stmt->fetchColumn();
    }


    private function buildFilters(
        string $search,
        ?int $categoryId,
        ?float $minPrice,
        ?float $maxPrice
    ): array {

        // Validate price range
        if (
            ($minPrice !== null &&
                (!is_finite($minPrice) || $minPrice < 0)) ||
            ($maxPrice !== null &&
                (!is_finite($maxPrice) || $maxPrice < 0)) ||
            ($minPrice !== null &&
                $maxPrice !== null &&
                $minPrice > $maxPrice)
        ) {
            throw new InvalidArgumentException(
                'Invalid price range.'
            );
        }

        $conditions = ['p.is_active = 1'];
        $params = [];

        // Search
        $search = trim($search);

        if ($search !== '') {
            $conditions[] = 'p.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        // Category
        if ($categoryId !== null) {

            if ($categoryId < 1) {
                throw new InvalidArgumentException(
                    'Invalid category.'
                );
            }

            $conditions[] = 'p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        // Minimum price
        if ($minPrice !== null) {
            $conditions[] = 'p.price >= :min_price';
            $params['min_price'] = $minPrice;
        }

        // Maximum price
        if ($maxPrice !== null) {
            $conditions[] = 'p.price <= :max_price';
            $params['max_price'] = $maxPrice;
        }

        $where = implode(' AND ', $conditions);

        return [$where, $params];
    }


    
public function getCategoriesWithCounts(): array
{
    $sql = "SELECT
                c.id,
                c.name,
                COUNT(p.id) AS product_count
            FROM categories AS c
            LEFT JOIN products AS p
                ON p.category_id = c.id
                AND p.is_active = 1
            GROUP BY c.id, c.name
            ORDER BY c.name ASC";

    $stmt = $this->db->run_query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
