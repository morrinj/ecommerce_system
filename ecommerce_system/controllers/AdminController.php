<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Coupon.php';
require_once __DIR__ . '/../models/Setting.php';

class AdminController {
    private Admin $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
    }

    public function checkAuth(): void {
        if (!isAdmin()) {
            redirect('admin/login');
        }
    }

    public function login(): void {
        if (isAdmin()) redirect('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $result = $this->adminModel->login($email, $password);
            if ($result['success']) {
                flash('success', 'Welcome back, ' . $result['admin']['full_name']);
                redirect('admin');
            }
            flash('error', $result['message']);
            redirect('admin/login');
        }
        require __DIR__ . '/../views/admin/login.php';
    }

    public function logout(): void {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_role']);
        session_destroy();
        redirect('admin/login');
    }

    public function dashboard(): void {
        $this->checkAuth();
        $orderModel = new Order();
        $userModel = new User();
        $productModel = new Product();
        $reviewModel = new Review();

        $totalRevenue = $orderModel->getRevenueTotal();
        $totalOrders = $orderModel->getOrderCount();
        $pendingOrders = $orderModel->getPendingCount();
        $totalUsers = $userModel->getCount();
        $totalProducts = $productModel->count(['status' => 'active']);
        $recentOrders = $orderModel->getRecent(10);
        $lowStock = $productModel->getLowStock(5);
        $pendingReviews = $reviewModel->getPending();
        $rawSales = $orderModel->getSalesStats('month');
        $salesData = ['labels' => [], 'values' => []];
        foreach ($rawSales as $row) {
            $salesData['labels'][] = date('M d', strtotime($row['date']));
            $salesData['values'][] = (float)$row['total'];
        }
        $topSelling = $productModel->getTopSelling(5);
        $productModelAll = new Product();
        $allProducts = $productModelAll->getActive(null, null, 'created_at', 'DESC');

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function products(int $page = 1): void {
        $this->checkAuth();
        $productModel = new Product();
        $products = $productModel->getAllAdmin($page, 500);
        $total = $productModel->count();
        $totalPages = ceil($total / 500);
        $perPage = 500;
        require __DIR__ . '/../views/admin/products/index.php';
    }

    public function productCreate(): void {
        $this->checkAuth();
        $categoryModel = new Category();
        $categories = $categoryModel->getActive();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $productModel = new Product();
            $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $data['name']), '-'));
            $slug = $baseSlug;
            $counter = 1;
            while ($productModel->findBy('slug', $slug)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
            $data['price'] = (float)$data['price'];
            $data['compare_price'] = !empty($data['compare_price']) ? (float)$data['compare_price'] : null;
            $data['stock_quantity'] = (int)$data['stock_quantity'];
            $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
            $data['is_new'] = isset($data['is_new']) ? 1 : 0;
            $data['is_on_sale'] = isset($data['is_on_sale']) ? 1 : 0;
            $data['is_track_stock'] = isset($data['is_track_stock']) ? 1 : 0;

            if (isset($_FILES['image_primary']) && $_FILES['image_primary']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadImage($_FILES['image_primary']);
                if ($uploadResult) $data['image_primary'] = $uploadResult;
            }

            $productId = $productModel->create($data);
            flash('success', 'Product created successfully');
            redirect('admin/products/edit/' . $productId);
        }
        require __DIR__ . '/../views/admin/products/form.php';
    }

    public function productEdit(int $id): void {
        $this->checkAuth();
        $productModel = new Product();
        $product = $productModel->find($id);
        if (!$product) {
            flash('error', 'Product not found');
            redirect('admin/products');
        }
        $categoryModel = new Category();
        $categories = $categoryModel->getActive();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $data['price'] = (float)$data['price'];
            $data['compare_price'] = !empty($data['compare_price']) ? (float)$data['compare_price'] : null;
            $data['stock_quantity'] = (int)$data['stock_quantity'];
            $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
            $data['is_new'] = isset($data['is_new']) ? 1 : 0;
            $data['is_on_sale'] = isset($data['is_on_sale']) ? 1 : 0;
            $data['is_track_stock'] = isset($data['is_track_stock']) ? 1 : 0;

            if (isset($_FILES['image_primary']) && $_FILES['image_primary']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadImage($_FILES['image_primary']);
                if ($uploadResult) $data['image_primary'] = $uploadResult;
            }

            $productModel->update($id, $data);
            flash('success', 'Product updated successfully');
            redirect('admin/products/edit/' . $id);
        }
        require __DIR__ . '/../views/admin/products/form.php';
    }

    public function productDelete(int $id): void {
        $this->checkAuth();
        $productModel = new Product();
        $productModel->update($id, ['status' => 'inactive']);
        flash('success', 'Product deleted successfully');
        redirect('admin/products');
    }

    public function categories(): void {
        $this->checkAuth();
        $categoryModel = new Category();
        $categories = $categoryModel->getWithProductCount();
        require __DIR__ . '/../views/admin/categories/index.php';
    }

    public function categoryCreate(): void {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $categoryModel = new Category();
            $categoryModel->createCategory($data);
            flash('success', 'Category created successfully');
            redirect('admin/categories');
        }
        $categoryModel = new Category();
        $parentCategories = $categoryModel->getParentCategories();
        require __DIR__ . '/../views/admin/categories/form.php';
    }

    public function categoryEdit(int $id): void {
        $this->checkAuth();
        $categoryModel = new Category();
        $category = $categoryModel->find($id);
        if (!$category) {
            flash('error', 'Category not found');
            redirect('admin/categories');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $categoryModel->updateCategory($id, $data);
            flash('success', 'Category updated successfully');
            redirect('admin/categories');
        }
        $parentCategories = $categoryModel->getParentCategories();
        require __DIR__ . '/../views/admin/categories/form.php';
    }

    public function categoryDelete(int $id): void {
        $this->checkAuth();
        $categoryModel = new Category();
        $categoryModel->update($id, ['status' => 'inactive']);
        flash('success', 'Category deleted successfully');
        redirect('admin/categories');
    }

    public function orders(int $page = 1): void {
        $this->checkAuth();
        $orderModel = new Order();
        $status = $_GET['status'] ?? null;
        if ($status) {
            $orders = $orderModel->getOrdersByStatus($status);
        } else {
            $orders = $orderModel->getWithUser($page, 20);
        }
        require __DIR__ . '/../views/admin/orders/index.php';
    }

    public function orderShow(int $id): void {
        $this->checkAuth();
        $orderModel = new Order();
        $order = $orderModel->findOrderById($id);
        if (!$order) {
            flash('error', 'Order not found');
            redirect('admin/orders');
        }
        $items = $orderModel->getItems($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = sanitize($_POST['status'] ?? '');
            $paymentStatus = sanitize($_POST['payment_status'] ?? '');
            $tracking = sanitize($_POST['tracking_number'] ?? '');
            if ($status) $orderModel->update($id, ['status' => $status]);
            if ($paymentStatus) $orderModel->update($id, ['payment_status' => $paymentStatus]);
            if ($tracking) $orderModel->update($id, ['tracking_number' => $tracking]);
            flash('success', 'Order updated successfully');
            redirect('admin/orders/view/' . $id);
        }
        require __DIR__ . '/../views/admin/orders/show.php';
    }

    public function users(int $page = 1): void {
        $this->checkAuth();
        $userModel = new User();
        $users = $userModel->getAll($page, 20);
        $total = $userModel->count();
        $totalPages = ceil($total / 20);
        require __DIR__ . '/../views/admin/users/index.php';
    }

    public function userToggle(int $id): void {
        $this->checkAuth();
        $userModel = new User();
        $user = $userModel->find($id);
        if ($user) {
            $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
            $userModel->updateStatus($id, $newStatus);
            flash('success', 'User status updated');
        }
        redirect('admin/users');
    }

    public function reviews(): void {
        $this->checkAuth();
        $reviewModel = new Review();
        $pending = $reviewModel->getAll();
        require __DIR__ . '/../views/admin/reviews/index.php';
    }

    public function reviewApprove(int $id): void {
        $this->checkAuth();
        $reviewModel = new Review();
        $reviewModel->approve($id);
        flash('success', 'Review approved');
        redirect('admin/reviews');
    }

    public function reviewDelete(int $id): void {
        $this->checkAuth();
        $reviewModel = new Review();
        $reviewModel->delete($id);
        flash('success', 'Review deleted');
        redirect('admin/reviews');
    }

    public function coupons(): void {
        $this->checkAuth();
        $couponModel = new Coupon();
        $coupons = $couponModel->getAll();
        require __DIR__ . '/../views/admin/coupons/index.php';
    }

    public function couponCreate(): void {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $data['code'] = strtoupper($data['code']);
            $data['value'] = (float)$data['value'];
            $data['min_order_amount'] = (float)($data['min_order_amount'] ?? 0);
            $data['max_uses'] = !empty($data['max_uses']) ? (int)$data['max_uses'] : null;
            $data['max_uses_per_user'] = (int)($data['max_uses_per_user'] ?? 1);
            $couponModel = new Coupon();
            $couponModel->create($data);
            flash('success', 'Coupon created successfully');
            redirect('admin/coupons');
        }
        require __DIR__ . '/../views/admin/coupons/form.php';
    }

    public function couponEdit(int $id): void {
        $this->checkAuth();
        $couponModel = new Coupon();
        $coupon = $couponModel->find($id);
        if (!$coupon) {
            flash('error', 'Coupon not found');
            redirect('admin/coupons');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $data['code'] = strtoupper($data['code']);
            $data['value'] = (float)$data['value'];
            $data['min_order_amount'] = (float)($data['min_order_amount'] ?? 0);
            $data['max_uses'] = !empty($data['max_uses']) ? (int)$data['max_uses'] : null;
            $data['max_uses_per_user'] = (int)($data['max_uses_per_user'] ?? 1);
            $couponModel->update($id, $data);
            flash('success', 'Coupon updated successfully');
            redirect('admin/coupons');
        }
        require __DIR__ . '/../views/admin/coupons/form.php';
    }

    public function couponDelete(int $id): void {
        $this->checkAuth();
        $couponModel = new Coupon();
        $couponModel->delete($id);
        flash('success', 'Coupon deleted');
        redirect('admin/coupons');
    }

    public function reports(): void {
        $this->checkAuth();
        $orderModel = new Order();
        $userModel = new User();

        $totalRevenue = $orderModel->getRevenueTotal();
        $totalOrders = $orderModel->getOrderCount();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalUsers = $userModel->getCount();
        $conversionRate = $totalUsers > 0 ? round(($totalOrders / $totalUsers) * 100, 1) : 0;

        $topProducts = $orderModel->getTopProducts(10);
        $monthlyData = $orderModel->getMonthlyStats(12);
        $statusData = $orderModel->getStatusBreakdown();
        $revenueTrend = $orderModel->getRevenueTrend(12);

        require __DIR__ . '/../views/admin/reports.php';
    }

    public function settings(): void {
        $this->checkAuth();
        $settingModel = new Setting();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            foreach ($data as $key => $value) {
                $settingModel->set($key, $value);
            }
            flash('success', 'Settings saved successfully');
            redirect('admin/settings');
        }
        $allSettings = $settingModel->getAll();
        $settings = [];
        foreach ($allSettings as $s) {
            $settings[$s['setting_key']] = $s['setting_value'];
        }
        require __DIR__ . '/../views/admin/settings.php';
    }

    public function ai(): void {
        $this->checkAuth();
        $productModel = new Product();
        $orderModel = new Order();
        $userModel = new User();
        $reviewModel = new Review();
        $stats = [
            'products' => $productModel->count(['status' => 'active']),
            'orders' => $orderModel->getOrderCount(),
            'users' => $userModel->getCount(),
            'reviews' => $reviewModel->getCount()
        ];
        require __DIR__ . '/../views/admin/ai.php';
    }

    public function admins(): void {
        $this->checkAuth();
        $admins = $this->adminModel->getAll();
        require __DIR__ . '/../views/admin/admins/index.php';
    }

    public function adminCreate(): void {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['confirm_password']);
            $this->adminModel->create($data);
            flash('success', 'Admin created successfully');
            redirect('admin/admins');
        }
        require __DIR__ . '/../views/admin/admins/form.php';
    }

    public function adminEdit(int $id): void {
        $this->checkAuth();
        $admin = $this->adminModel->find($id);
        if (!$admin) {
            flash('error', 'Admin not found');
            redirect('admin/admins');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeArray($_POST);
            if (!empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            } else {
                unset($data['password']);
            }
            $this->adminModel->update($id, $data);
            flash('success', 'Admin updated successfully');
            redirect('admin/admins');
        }
        require __DIR__ . '/../views/admin/admins/form.php';
    }

    public function adminDelete(int $id): void {
        $this->checkAuth();
        $this->adminModel->delete($id);
        flash('success', 'Admin deleted');
        redirect('admin/admins');
    }

    public function orderInvoice(int $id): void {
        $this->checkAuth();
        $orderModel = new Order();
        $order = $orderModel->findOrderById($id);
        if (!$order) {
            flash('error', 'Order not found');
            redirect('admin/orders');
        }
        $items = $orderModel->getItems($id);
        require __DIR__ . '/../views/admin/orders/invoice.php';
    }

    private function uploadImage(array $file): ?string {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) return null;
        if ($file['size'] > UPLOAD_MAX_SIZE) return null;
        $dir = __DIR__ . '/../uploads/products/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_') . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return 'uploads/products/' . $filename;
        }
        return null;
    }
}
