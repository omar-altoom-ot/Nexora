<?php

declare(strict_types=1);

require_once __DIR__ . '/../Config/DBconnection.php';

class ProductImageController
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function validateImage(mixed $file): array
    {
        if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) {
            return ['product_image' => 'Invalid image upload.'];
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['product_image' => 'Please select a product image.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['product_image' => 'Image upload failed.'];
        }

        $maxSize = 2 * 1024 * 1024;

        if (
            !isset($file['size'], $file['tmp_name']) ||
            !is_int($file['size']) ||
            $file['size'] <= 0 ||
            $file['size'] > $maxSize ||
            !is_string($file['tmp_name'])
        ) {
            return ['product_image' => 'Invalid image size or file data.'];
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return ['product_image' => 'Invalid uploaded file.'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!in_array($mimeType, $allowedTypes, true)) {
            return ['product_image' => 'Only JPG, PNG and WEBP images are allowed.'];
        }

        if (@getimagesize($file['tmp_name']) === false) {
            return ['product_image' => 'Invalid image content.'];
        }

        return [];
    }


    public function storeImage(mixed $file): string
    {
        // 1. Validate image
        $errors = $this->validateImage($file);

        if ($errors !== []) {
            throw new InvalidArgumentException(
                $errors['product_image'] ?? 'Invalid image.'
            );
        }

        // 2. Determine safe extension from real MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($extensions[$mimeType])) {
            throw new InvalidArgumentException('Unsupported image type.');
        }

        // 3. Set upload directory
        $directory = dirname(__DIR__) . '/Assets/Images/products';

        if (!is_dir($directory) || !is_writable($directory)) {
            throw new RuntimeException('Upload directory is unavailable.');
        }

        // 4. Generate random filename
        $filename = bin2hex(random_bytes(16))
            . '.' . $extensions[$mimeType];

        // 5. Move uploaded image
        $destination = $directory . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Unable to store image.');
        }

        // 6. Return relative image path
        return 'Assets/Images/products/' . $filename;
    }


    public function saveImageRecord(int $productId, string $imagePath): int
    {
        if ($productId < 1 || $imagePath === '') {
            throw new InvalidArgumentException('Invalid product image data.');
        }

        $sql = "INSERT INTO product_images (product_id, image)
            VALUES (:product_id, :image)";

        $this->db->run_query($sql, [
            'product_id' => $productId,
            'image' => $imagePath
        ]);

        return (int) $this->db
            ->run_query("SELECT LAST_INSERT_ID()")
            ->fetchColumn();
    }


    public function findPrimaryImageByProductId(int $productId): ?array
    {
        if ($productId < 1) {
            throw new InvalidArgumentException('Invalid product ID.');
        }

        $sql = "SELECT id, image
            FROM product_images
            WHERE product_id = :product_id
            ORDER BY id ASC
            LIMIT 1";

        $stmt = $this->db->run_query($sql, [
            'product_id' => $productId
        ]);

        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        return $image === false ? null : $image;
    }



    public function replacePrimaryImage(
        int $productId,
        string $newImagePath
    ): ?string {

        if ($productId < 1 || $newImagePath === '') {
            throw new InvalidArgumentException('Invalid image data.');
        }

        $currentImage = $this->findPrimaryImageByProductId($productId);

        if ($currentImage === null) {
            $this->saveImageRecord($productId, $newImagePath);
            return null;
        }

        $sql = "UPDATE product_images
            SET image = :image
            WHERE id = :id
              AND product_id = :product_id";


        $stmt = $this->db->run_query($sql, [
            'image' => $newImagePath,
            'id' => (int) $currentImage['id'],
            'product_id' => $productId
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Unable to replace product image.');
        }

        return (string) $currentImage['image'];

    }


}
