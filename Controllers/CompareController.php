<?php
require_once __DIR__ . '/../Config/DBconnection.php';

class CompareController
{
    private Db $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    public function categories(): array
    {
        return $this->db->run_query(
            'SELECT id, name FROM categories ORDER BY name ASC'
        )->fetchAll();
    }

    public function products(int $categoryId): array
    {
        if ($categoryId < 1) return [];

        return $this->db->run_query(
            'SELECT p.id, p.category_id, p.name, p.description, p.price, p.stock_quantity,
                    c.name AS category_name,
                    (SELECT pi.image FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.id ASC LIMIT 1) AS image,
                    (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.product_id = p.id) AS average_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id) AS review_count
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.category_id = :category_id
             ORDER BY p.name ASC',
            ['category_id' => $categoryId]
        )->fetchAll();
    }

    public function productPair(int $categoryId, int $firstId, int $secondId): array
    {
        if ($firstId === $secondId) {
            throw new InvalidArgumentException('Choose two different products.');
        }

        $products = $this->products($categoryId);
        $byId = [];
        foreach ($products as $product) $byId[(int) $product['id']] = $product;

        if (!isset($byId[$firstId], $byId[$secondId])) {
            throw new InvalidArgumentException('Choose products from the selected category.');
        }

        return [$byId[$firstId], $byId[$secondId]];
    }

    public function aiComparison(array $first, array $second, array $needs): array
    {
        $apiKey = getenv('GCP_API_KEY') ?: '';
        if ($apiKey === '') {
            throw new RuntimeException('AI is not configured yet. Set GCP_API_KEY in the server environment, then restart Apache.');
        }
        if (!function_exists('curl_init')) {
            throw new RuntimeException('PHP cURL is required to contact the AI service.');
        }

        $system = 'You are a careful PC hardware shopping advisor. Compare only the supplied database facts. Never infer or invent technical specifications that are missing. The database may contain only name, category, description, price, stock and customer review data; say when performance, memory, power draw, or other specs are unavailable. Recommend the better fit for the user\'s stated use, budget, and priorities, not simply the more expensive or higher-spec item. Return valid JSON only with keys: summary (string), winner_id (integer or null), winner_reason (string), tradeoffs (array of strings), and comparison_rows (array of objects with label, first, second).';
        $input = [
            'user_needs' => $needs,
            'products' => [
                ['id' => (int) $first['id'], 'name' => $first['name'], 'category' => $first['category_name'], 'price_jd' => (float) $first['price'], 'stock' => (int) $first['stock_quantity'], 'description' => $first['description'], 'average_rating' => $first['average_rating'], 'review_count' => (int) $first['review_count']],
                ['id' => (int) $second['id'], 'name' => $second['name'], 'category' => $second['category_name'], 'price_jd' => (float) $second['price'], 'stock' => (int) $second['stock_quantity'], 'description' => $second['description'], 'average_rating' => $second['average_rating'], 'review_count' => (int) $second['review_count']],
            ],
        ];
        $payload = json_encode([
            'systemInstruction' => ['parts' => [['text' => $system]]],
            'contents' => [[
                'role' => 'user',
                'parts' => [['text' => json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]],
            ]],
            'generationConfig' => ['responseMimeType' => 'application/json'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $response = null;
        $status = 0;
        foreach (['gemini-3.8-flash', 'gemini-3.5-flash-lite'] as $model) {
            $curl = curl_init('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent');
            curl_setopt_array($curl, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $apiKey],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 30,
            ]);
            $raw = curl_exec($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlErrorNumber = curl_errno($curl);
            $curlError = curl_error($curl);
            curl_close($curl);

            $hasFallback = $model !== 'gemini-3.5-flash-lite';
            if ($raw === false) {
                if ($hasFallback && $curlErrorNumber === CURLE_OPERATION_TIMEDOUT) continue;
                throw new RuntimeException('Could not reach the AI service: ' . $curlError);
            }
            $response = json_decode($raw, true);
            if ($status >= 200 && $status < 300) break;

            $message = $response['error']['message'] ?? 'The AI service returned an error.';
            if ($hasFallback && in_array($status, [429, 503], true)) continue;
            throw new RuntimeException($message);
        }

        $text = '';
        foreach (($response['candidates'][0]['content']['parts'] ?? []) as $part) {
            $text .= $part['text'] ?? '';
        }
        $analysis = json_decode(trim($text), true);
        if (!is_array($analysis)) throw new RuntimeException('The AI returned an unreadable comparison. Please try again.');

        return $analysis;
    }
}
