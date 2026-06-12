<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?> - <?= APP_NAME ?></title>
    <meta name="description" content="<?= $metaDescription ?? 'Your premium online shopping destination' ?>">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛍️</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <script>var APP_BASE_URL = '<?= APP_URL ?>';</script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= url() ?>">
                <i class="bi bi-shop"></i> <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= url() ?>">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Products
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            $navCatModel = new Category();
                            $navCategories = $navCatModel->getActive();
                            foreach ($navCategories as $navCat):
                            ?>
                            <li><a class="dropdown-item" href="<?= url('products?category=' . $navCat['slug']) ?>"><?= htmlspecialchars($navCat['name']) ?></a></li>
                            <?php endforeach; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('products') ?>">All Products</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= url('products?on_sale=1') ?>">Sale</a>
                    </li>
                </ul>
                <form class="d-flex me-3 position-relative search-form" action="<?= url('search') ?>" method="GET">
                    <div class="input-group">
                        <input class="form-control search-input border-0" type="search" name="q" placeholder="Search products..." aria-label="Search" autocomplete="off" data-autocomplete-url="<?= url('api/search-suggestions') ?>">
                        <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    <div class="search-suggestions w-100"></div>
                </form>
                <button class="btn btn-link nav-link dark-mode-toggle" type="button" title="Toggle dark mode">
                    <i class="bi bi-moon-fill dark-mode-icon"></i>
                </button>
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link position-relative px-2" href="<?= url('cart') ?>">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" id="cartCount">
                                <?php
                                $navCart = new Cart();
                                echo $navCart->getCartCount(isLoggedIn() ? getCurrentUserId() : null, isLoggedIn() ? null : session_id());
                                ?>
                            </span>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-md-inline ms-1"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Account') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('profile') ?>"><i class="bi bi-person me-2"></i>My Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?= url('orders') ?>"><i class="bi bi-box me-2"></i>My Orders</a></li>
                            <li><a class="dropdown-item" href="<?= url('wishlist') ?>"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                            <li><a class="dropdown-item" href="<?= url('profile/edit') ?>"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="<?= url('login') ?>"><i class="bi bi-person fs-5"></i></a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main>
