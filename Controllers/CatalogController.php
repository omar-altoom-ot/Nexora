<?php
declare(strict_types=1);

require_once __DIR__ . '/../Model/CatalogRepository.php';

class CatalogController
{
    private CatalogRepository $repository;

    public function __construct(CatalogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(array $query): array
    {
        // 1. Read page number
        $page = filter_var(
            $query['p'] ?? 1,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $page = is_int($page) ? $page : 1;


        // Search Validation
        $rawSearch = $query['q'] ?? '';

        $search = is_string($rawSearch)
            ? trim($rawSearch)
            : '';

        if (strlen($search) > 150) {
            $search = '';
        }


        // Category Validation
        $rawCategory = $query['category'] ?? null;

        $categoryId = (
            is_string($rawCategory) ||
            is_int($rawCategory)
        )
            ? filter_var(
                $rawCategory,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            )
            : false;

        $categoryId = is_int($categoryId)
            ? $categoryId
            : null;



        // Price Range Validation
        $rawMinPrice = $query['min_price'] ?? null;
        $rawMaxPrice = $query['max_price'] ?? null;

        $minPrice = $this->parsePrice($rawMinPrice);
        $maxPrice = $this->parsePrice($rawMaxPrice);

        $priceError = '';

        if (
            ($rawMinPrice !== null && $rawMinPrice !== '' && $minPrice === null) ||
            ($rawMaxPrice !== null && $rawMaxPrice !== '' && $maxPrice === null)
        ) {
            $priceError = 'Please enter a valid price.';
            $minPrice = null;
            $maxPrice = null;
        } elseif (
            $minPrice !== null &&
            $maxPrice !== null &&
            $minPrice > $maxPrice
        ) {
            $priceError = 'Minimum price cannot be greater than maximum price.';

            $minPrice = null;
            $maxPrice = null;
        }


        // Sorting Validation
        $rawSort = $query['sort'] ?? 'newest';

        $allowedSorts = [
            'newest',
            'price_asc',
            'price_desc',
            'name_asc',
            'name_desc'
        ];

        $sort = is_string($rawSort)
            && in_array($rawSort, $allowedSorts, true)
            ? $rawSort
            : 'newest';


        // 2. Products per page
        $perPage = 12;

        // 3. Count active products


        $totalProducts = $this->repository
            ->countFilteredProducts(
                $search,
                $categoryId,
                $minPrice,
                $maxPrice
            );



        // 4. Calculate total pages
        $totalPages = max(
            1,
            (int) ceil($totalProducts / $perPage)
        );

        // 5. Prevent page number exceeding total
        $page = min($page, $totalPages);

        // 6. Calculate offset
        $offset = ($page - 1) * $perPage;

        // 7. Fetch products

        
$products = $this->repository
    ->findFilteredProducts(
        $search,
        $categoryId,
        $minPrice,
        $maxPrice,
        $sort,
        $perPage,
        $offset
    );



        // 8. Fetch categories
        $categories = $this->repository
            ->getCategoriesWithCounts();

        // 9. Return data to View

        
return [
    'products' => $products,
    'categories' => $categories,
    'totalProducts' => $totalProducts,
    'search' => $search,
    'categoryId' => $categoryId,

    // New values
    'minPrice' => $minPrice,
    'maxPrice' => $maxPrice,
    'priceError' => $priceError,
    'sort' => $sort,

    'productPage' => $page,
    'perPage' => $perPage,
    'totalPages' => $totalPages,
    'offset' => $offset
];



    }


    private function parsePrice(mixed $value): ?float
    {
        if (
            !is_string($value) &&
            !is_int($value) &&
            !is_float($value)
        ) {
            return null;
        }

        $price = trim((string) $value);

        if (
            $price === '' ||
            !preg_match('/\A\d{1,8}(?:\.\d{1,2})?\z/', $price)
        ) {
            return null;
        }

        return (float) $price;
    }


}
