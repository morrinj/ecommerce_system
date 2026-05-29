<?php
require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel {
    protected string $table = 'categories';

    public function __construct() {
        parent::__construct();
    }

    public function getActive(): array {
        $stmt = $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status = 'active') as product_count
                FROM categories c WHERE c.status = 'active' ORDER BY c.sort_order ASC, c.name ASC");
        return $stmt->fetchAll();
    }

    public function getFeatured(): array {
        $stmt = $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status = 'active') as product_count
                FROM categories c WHERE c.status = 'active' AND c.is_featured = 1
                ORDER BY c.sort_order ASC LIMIT 8");
        return $stmt->fetchAll();
    }

    public function getBySlug(string $slug): ?array {
        return $this->findBy('slug', $slug);
    }

    public function getWithProductCount(): array {
        $stmt = $this->db->query("SELECT c.*, p.name as parent_name,
                (SELECT COUNT(*) FROM products p2 WHERE p2.category_id = c.id AND p2.status = 'active') as product_count
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                ORDER BY c.sort_order ASC, c.name ASC");
        return $stmt->fetchAll();
    }

    public function getParentCategories(): array {
        $stmt = $this->db->query("SELECT * FROM categories WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    }

    public function getChildCategories(int $parentId): array {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id = ? AND status = 'active' ORDER BY sort_order ASC");
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    public function createCategory(array $data): int {
        if (empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $data['name']), '-'));
        }
        return $this->create($data);
    }

    public function updateCategory(int $id, array $data): bool {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $data['name']), '-'));
        }
        return $this->update($id, $data);
    }
}
