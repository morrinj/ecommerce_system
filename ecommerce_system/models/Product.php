<?php
require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel {
    protected string $table = 'products';

    public function __construct() {
        parent::__construct();
    }

    public function getActive(int $limit = null, int $offset = null, string $orderBy = 'created_at', string $orderDir = 'DESC'): array {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'
                ORDER BY p.{$orderBy} {$orderDir}";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        if ($offset) $sql .= " OFFSET " . (int)$offset;
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getFeatured(int $limit = 8): array {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.is_featured = 1
                ORDER BY p.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getOnSale(int $limit = 8): array {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.is_on_sale = 1 AND p.compare_price IS NOT NULL
                ORDER BY p.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNewArrivals(int $limit = 8): array {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.is_new = 1
                ORDER BY p.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByCategory(int $categoryId, int $limit = null, int $offset = null): array {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.category_id = ?
                ORDER BY p.created_at DESC";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        if ($offset) $sql .= " OFFSET " . (int)$offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public function search(string $query, int $limit = 20): array {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'
                AND (p.name LIKE ? OR p.short_description LIKE ? OR p.sku LIKE ?)
                ORDER BY 
                    CASE WHEN p.name LIKE ? THEN 0
                         WHEN p.short_description LIKE ? THEN 1
                         ELSE 2
                    END,
                    p.created_at DESC
                LIMIT ?";
        $searchTerm = '%' . $query . '%';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }

    public function searchSuggestions(string $query, int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT id, name, slug, price, compare_price, image_primary
                FROM products WHERE status = 'active' AND name LIKE ?
                LIMIT ?");
        $stmt->execute(['%' . $query . '%', $limit]);
        return $stmt->fetchAll();
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getRelated(int $productId, int $categoryId, int $limit = 4): array {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.category_id = ? AND p.id != ?
                ORDER BY RAND() LIMIT ?");
        $stmt->execute([$categoryId, $productId, $limit]);
        return $stmt->fetchAll();
    }

    public function filter(array $filters, int $limit = 12, int $offset = 0): array {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.short_description LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['on_sale'])) {
            $where[] = "p.is_on_sale = 1 AND p.compare_price IS NOT NULL";
        }
        if (!empty($filters['featured'])) {
            $where[] = "p.is_featured = 1";
        }
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock_quantity > 0";
        }

        $orderBy = 'p.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc': $orderBy = 'p.price ASC'; break;
                case 'price_desc': $orderBy = 'p.price DESC'; break;
                case 'name_asc': $orderBy = 'p.name ASC'; break;
                case 'name_desc': $orderBy = 'p.name DESC'; break;
                case 'newest': $orderBy = 'p.created_at DESC'; break;
                case 'rating': $orderBy = 'p.average_rating DESC'; break;
            }
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function filterCount(array $filters): int {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.short_description LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['on_sale'])) {
            $where[] = "p.is_on_sale = 1 AND p.compare_price IS NOT NULL";
        }
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock_quantity > 0";
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) FROM products p WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getLowStock(int $threshold = 5): array {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE is_track_stock = 1 AND stock_quantity <= ? AND status = 'active' ORDER BY stock_quantity ASC LIMIT 20");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function updateStock(int $productId, int $quantity): bool {
        return $this->update($productId, ['stock_quantity' => $quantity]);
    }

    public function decrementStock(int $productId, int $quantity): bool {
        $stmt = $this->db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?");
        return $stmt->execute([$quantity, $productId, $quantity]);
    }

    public function getTopRated(int $limit = 8): array {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.review_count > 0
                ORDER BY p.average_rating DESC, p.review_count DESC
                LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllAdmin(int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name
                FROM products p LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTopSelling(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT p.*, SUM(oi.quantity) as total_sold
                FROM products p
                JOIN order_items oi ON p.id = oi.product_id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status NOT IN ('cancelled','refunded')
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
