<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController {
    private Product $productModel;
    private Category $categoryModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index(): void {
        $featuredProducts = $this->productModel->getFeatured(8);
        $newArrivals = $this->productModel->getNewArrivals(8);
        $onSaleProducts = $this->productModel->getOnSale(4);
        $topRated = $this->productModel->getTopRated(4);
        $categories = $this->categoryModel->getActive();
        require __DIR__ . '/../views/frontend/home/index.php';
    }
}
