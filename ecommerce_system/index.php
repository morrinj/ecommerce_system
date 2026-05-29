<?php
/**
 * SmartShopping - Main Router
 * 
 * Lightweight MVC router for the e-commerce platform.
 * Handles all frontend and admin routes.
 */

require_once __DIR__ . '/config/app.php';

$requestUri = $_SERVER['REQUEST_URI'];
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$path = substr($requestUri, strlen($basePath));
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');
$path = explode('/', $path);

// Require model files
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Admin.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Cart.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Review.php';
require_once __DIR__ . '/models/Wishlist.php';
require_once __DIR__ . '/models/Coupon.php';
require_once __DIR__ . '/models/Payment.php';

$controller = $path[0] ?? '';
$action = $path[1] ?? '';
$param = $path[2] ?? '';
$param2 = $path[3] ?? '';

try {
    switch ($controller) {
        // ---- Admin Routes ----
        case 'admin':
            if (!isset($_SESSION['admin_id']) && $action !== 'login') {
                redirect('admin/login');
            }

            require __DIR__ . '/controllers/AdminController.php';
            $adminCtrl = new AdminController();

            switch ($action) {
                case '':
                case 'dashboard':
                    $adminCtrl->dashboard();
                    break;
                case 'login':
                    $adminCtrl->login();
                    break;
                case 'logout':
                    $adminCtrl->logout();
                    break;
                case 'products':
                    if ($param === 'create') $adminCtrl->productCreate();
                    elseif ($param === 'edit' && $param2) $adminCtrl->productEdit((int)$param2);
                    elseif ($param === 'delete' && $param2) $adminCtrl->productDelete((int)$param2);
                    else $adminCtrl->products();
                    break;
                case 'categories':
                    if ($param === 'create') $adminCtrl->categoryCreate();
                    elseif ($param === 'edit' && $param2) $adminCtrl->categoryEdit((int)$param2);
                    elseif ($param === 'delete' && $param2) $adminCtrl->categoryDelete((int)$param2);
                    else $adminCtrl->categories();
                    break;
                case 'orders':
                    if ($param === 'view' && $param2) $adminCtrl->orderShow((int)$param2);
                    elseif ($param === 'invoice' && $param2) $adminCtrl->orderInvoice((int)$param2);
                    else $adminCtrl->orders();
                    break;
                case 'users':
                    if ($param === 'toggle' && $param2) $adminCtrl->userToggle((int)$param2);
                    else $adminCtrl->users();
                    break;
                case 'reviews':
                    if ($param === 'approve' && $param2) $adminCtrl->reviewApprove((int)$param2);
                    elseif ($param === 'delete' && $param2) $adminCtrl->reviewDelete((int)$param2);
                    else $adminCtrl->reviews();
                    break;
                case 'coupons':
                    if ($param === 'create') $adminCtrl->couponCreate();
                    elseif ($param === 'edit' && $param2) $adminCtrl->couponEdit((int)$param2);
                    elseif ($param === 'delete' && $param2) $adminCtrl->couponDelete((int)$param2);
                    else $adminCtrl->coupons();
                    break;
                case 'reports':
                    $adminCtrl->reports();
                    break;
                case 'settings':
                    $adminCtrl->settings();
                    break;
                case 'ai':
                    $adminCtrl->ai();
                    break;
                case 'admins':
                    if ($param === 'create') $adminCtrl->adminCreate();
                    elseif ($param === 'edit' && $param2) $adminCtrl->adminEdit((int)$param2);
                    elseif ($param === 'delete' && $param2) $adminCtrl->adminDelete((int)$param2);
                    else $adminCtrl->admins();
                    break;
                default:
                    $adminCtrl->dashboard();
            }
            break;

        // ---- Auth Routes ----
        case 'login':
            require __DIR__ . '/controllers/AuthController.php';
            $auth = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') $auth->login();
            else $auth->showLogin();
            break;

        case 'register':
            require __DIR__ . '/controllers/AuthController.php';
            $auth = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') $auth->register();
            else $auth->showRegister();
            break;

        case 'logout':
            require __DIR__ . '/controllers/AuthController.php';
            $auth = new AuthController();
            $auth->logout();
            break;

        case 'forgot-password':
            require __DIR__ . '/controllers/AuthController.php';
            $auth = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') $auth->forgotPassword();
            else $auth->showForgotPassword();
            break;

        case 'reset-password':
            require __DIR__ . '/controllers/AuthController.php';
            $auth = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') $auth->resetPassword();
            elseif ($action) $auth->showResetPassword($action);
            else redirect('login');
            break;

        // ---- Product Routes ----
        case 'products':
            require __DIR__ . '/controllers/ProductController.php';
            $prodCtrl = new ProductController();
            $prodCtrl->index();
            break;

        case 'product':
            require __DIR__ . '/controllers/ProductController.php';
            $prodCtrl = new ProductController();
            if ($action) $prodCtrl->show($action);
            else redirect('products');
            break;

        case 'search':
            require __DIR__ . '/controllers/ProductController.php';
            $prodCtrl = new ProductController();
            $prodCtrl->search();
            break;

        case 'review':
            require __DIR__ . '/controllers/ProductController.php';
            $prodCtrl = new ProductController();
            $prodCtrl->addReview();
            break;

        // ---- Cart Routes ----
        case 'cart':
            require __DIR__ . '/controllers/CartController.php';
            $cartCtrl = new CartController();
            if ($action === 'add') $cartCtrl->add();
            elseif ($action === 'update') $cartCtrl->update();
            elseif ($action === 'remove') $cartCtrl->remove();
            elseif ($action === 'count') $cartCtrl->count();
            else $cartCtrl->index();
            break;

        // ---- Checkout Routes ----
        case 'checkout':
            require __DIR__ . '/controllers/OrderController.php';
            if ($action === 'shipping') {
                require __DIR__ . '/controllers/ShippingController.php';
                $shipCtrl = new ShippingController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') $shipCtrl->store();
                else $shipCtrl->index();
            } else {
                $orderCtrl = new OrderController();
                if ($action === 'coupon') $orderCtrl->applyCoupon();
                else $orderCtrl->checkout();
            }
            break;

        case 'order-confirmation':
            require __DIR__ . '/controllers/OrderController.php';
            $orderCtrl = new OrderController();
            if ($action) $orderCtrl->confirmation($action);
            else redirect('');
            break;

        // ---- Order Routes ----
        case 'orders':
            require __DIR__ . '/controllers/OrderController.php';
            $orderCtrl = new OrderController();
            $orderCtrl->track();
            break;

        case 'order':
            require __DIR__ . '/controllers/OrderController.php';
            $orderCtrl = new OrderController();
            if ($action) $orderCtrl->show($action);
            else redirect('orders');
            break;

        // ---- Profile Routes ----
        case 'profile':
            require __DIR__ . '/controllers/ProfileController.php';
            $profileCtrl = new ProfileController();
            if ($action === 'edit') $profileCtrl->edit();
            elseif ($action === 'password') $profileCtrl->password();
            else $profileCtrl->dashboard();
            break;

        case 'wishlist':
            require __DIR__ . '/controllers/ProfileController.php';
            $profileCtrl = new ProfileController();
            if ($action === 'toggle') {
                $profileCtrl->toggleWishlist();
            } else {
                $profileCtrl->wishlist();
            }
            break;

        case 'wishlist/toggle':
            require __DIR__ . '/controllers/ProfileController.php';
            $profileCtrl = new ProfileController();
            $profileCtrl->toggleWishlist();
            break;

        // ---- API Routes ----
        case 'api':
            if ($action === 'shipping' && $param === 'calculate') {
                require __DIR__ . '/controllers/ShippingController.php';
                $shipCtrl = new ShippingController();
                $shipCtrl->calculateAjax();
            } elseif ($action === 'search-suggestions') {
                require __DIR__ . '/controllers/ProductController.php';
                $prodCtrl = new ProductController();
                $prodCtrl->suggestions();
            } elseif ($action === 'cart-count') {
                require __DIR__ . '/controllers/CartController.php';
                $cartCtrl = new CartController();
                $cartCtrl->count();
            } else {
                header('HTTP/1.0 404 Not Found');
                echo json_encode(['error' => 'API endpoint not found']);
            }
            break;

        // ---- Static Pages ----
        case 'help':
            require __DIR__ . '/controllers/PageController.php';
            (new PageController())->help();
            break;

        case 'shipping':
            require __DIR__ . '/controllers/PageController.php';
            (new PageController())->shipping();
            break;

        case 'returns':
            require __DIR__ . '/controllers/PageController.php';
            (new PageController())->returns();
            break;

        case 'contact':
            require __DIR__ . '/controllers/PageController.php';
            (new PageController())->contact();
            break;

        case 'about':
            require __DIR__ . '/controllers/PageController.php';
            (new PageController())->about();
            break;

        // ---- Home ----
        case '':
            require __DIR__ . '/controllers/HomeController.php';
            $homeCtrl = new HomeController();
            $homeCtrl->index();
            break;

        // ---- 404 ----
        default:
            header('HTTP/1.0 404 Not Found');
            require __DIR__ . '/views/frontend/home/index.php';
    }
} catch (Exception $e) {
    if (APP_ENV === 'development') {
        echo '<h2>Error</h2><pre>' . $e->getMessage() . '</pre>';
    } else {
        flash('error', 'Something went wrong. Please try again.');
        redirect('');
    }
}
