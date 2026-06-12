<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Wishlist.php';

class ProductController {
    private Product $productModel;
    private Category $categoryModel;
    private Review $reviewModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->reviewModel = new Review();
    }

    public function index(): void {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        $categorySlug = $_GET['category'] ?? null;
        $filters = [
            'category_id' => null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? 'newest',
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'on_sale' => $_GET['on_sale'] ?? null,
            'in_stock' => $_GET['in_stock'] ?? null,
        ];

        if ($categorySlug) {
            $category = $this->categoryModel->getBySlug($categorySlug);
            if ($category) {
                $filters['category_id'] = $category['id'];
            }
        }

        $products = $this->productModel->filter($filters, $perPage, $offset);
        $total = $this->productModel->filterCount($filters);
        $categories = $this->categoryModel->getActive();
        $currentCategory = $categorySlug ? $this->categoryModel->getBySlug($categorySlug) : null;

        require __DIR__ . '/../views/frontend/products/index.php';
    }

    public function show(string $slug): void {
        $product = $this->productModel->getBySlug($slug);
        if (!$product) {
            flash('error', 'Product not found');
            redirect('');
        }
        $related = $this->productModel->getRelated($product['id'], $product['category_id']);
        $reviews = $this->reviewModel->getByProduct($product['id']);
        $reviewStats = $this->reviewModel->getStats($product['id']);

        $inWishlist = false;
        if (isLoggedIn()) {
            $wishlist = new Wishlist();
            $inWishlist = $wishlist->isInWishlist(getCurrentUserId(), $product['id']);
        }

        logActivity(getCurrentUserId(), 'view', $product['id'], $product['category_id']);

        require __DIR__ . '/../views/frontend/products/show.php';
    }

    public function search(): void {
        $query = sanitize($_GET['q'] ?? '');
        if (empty($query)) redirect('products');
        $products = $this->productModel->search($query);
        logActivity(getCurrentUserId(), 'search', null, null, $query);
        require __DIR__ . '/../views/frontend/products/search.php';
    }

    public function suggestions(): void {
        $query = sanitize($_GET['q'] ?? '');
        if (empty($query) || strlen($query) < 2) {
            echo json_encode([]);
            return;
        }
        $products = $this->productModel->searchSuggestions($query);
        header('Content-Type: application/json');
        echo json_encode($products);
    }

    public function byCategory(string $slug): void {
        $category = $this->categoryModel->getBySlug($slug);
        if (!$category) {
            flash('error', 'Category not found');
            redirect('');
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = ITEMS_PER_PAGE;
        $products = $this->productModel->getByCategory($category['id'], $perPage, ($page - 1) * $perPage);
        $total = $this->productModel->count(['category_id' => $category['id'], 'status' => 'active']);
        require __DIR__ . '/../views/frontend/products/category.php';
    }

    public function addReview(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isLoggedIn()) {
            redirect('login');
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $comment = sanitize($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            flash('error', 'Please select a valid rating');
            redirect('product/' . ($_POST['slug'] ?? ''));
        }
        if ($this->reviewModel->hasReviewed($productId, getCurrentUserId())) {
            flash('error', 'You have already reviewed this product');
            redirect('product/' . ($_POST['slug'] ?? ''));
        }

        $this->reviewModel->addReview($productId, getCurrentUserId(), $rating, $title, $comment);
        logActivity(getCurrentUserId(), 'review', $productId);
        flash('success', 'Review submitted and pending approval');
        redirect('product/' . ($_POST['slug'] ?? ''));
    }
}
