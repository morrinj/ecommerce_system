# SmartShopping E-Commerce Platform - Setup Guide

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache (mod_rewrite enabled) or Nginx
- XAMPP / WAMP / LAMP stack recommended

## Quick Installation

### 1. Clone / Copy Files

Copy the `smartshopping` folder to your web server root:
- **XAMPP:** `C:\xampp\htdocs\smartshopping`
- **LAMP:** `/var/www/html/smartshopping`
- **Nginx:** Configure root to `/path/to/smartshopping`

### 2. Database Setup

1. Open phpMyAdmin (or MySQL CLI)
2. Run the database schema:
   ```
   Source: database.sql
   ```
   Or import `database.sql` via phpMyAdmin.

3. **Default Admin Credentials:**
   - Email: `admin@smartshop.com`
   - Password: `admin123`

### 3. Configure Database Connection

Edit `config/database.php` or set environment variables:

```php
// Default: MySQL root with no password
define('DB_HOST', 'localhost');
define('DB_NAME', 'smartshopping');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Or set environment variables (recommended for production):
```
DB_HOST=localhost
DB_NAME=smartshopping
DB_USER=youruser
DB_PASS=yourpassword
```

### 4. Configure App URL

Edit `config/app.php`:

```php
define('APP_URL', 'http://localhost/smartshopping');
```

Change this to match your deployment URL.

### 5. File Permissions

Ensure the following directories are writable:
```
uploads/products/
uploads/
```

On Linux:
```bash
chmod -R 755 uploads
```

### 6. Enable Apache mod_rewrite

#### XAMPP / WAMP:
- `mod_rewrite` is enabled by default
- Ensure `.htaccess` files are allowed in your Apache config:
  ```
  AllowOverride All
  ```

#### Nginx:
Add this to your server block:

```nginx
location /smartshopping/ {
    try_files $uri $uri/ /smartshopping/index.php?$query_string;
}
```

### 7. Access the Application

- **Frontend:** http://localhost/smartshopping
- **Admin Panel:** http://localhost/smartshopping/admin
- **Admin Login:** admin@smartshop.com / admin123

## Folder Structure

```
smartshopping/
├── .htaccess              # URL rewriting rules
├── index.php              # Main router (front controller)
├── database.sql           # Database schema + sample data
├── SETUP.md               # This file
├── config/
│   ├── app.php            # Application configuration
│   ├── database.php       # Database connection (PDO)
│   └── helpers.php        # Helper functions
├── models/                # Data models (OOP)
│   ├── BaseModel.php      # Abstract base model
│   ├── User.php
│   ├── Admin.php
│   ├── Product.php
│   ├── Category.php
│   ├── Cart.php
│   ├── Order.php
│   ├── Review.php
│   ├── Wishlist.php
│   ├── Coupon.php
│   └── Payment.php
├── controllers/           # Business logic
│   ├── HomeController.php
│   ├── AuthController.php
│   ├── ProductController.php
│   ├── CartController.php
│   ├── OrderController.php
│   ├── ProfileController.php
│   └── AdminController.php
├── views/                 # UI Templates
│   ├── partials/          # Header/footer partials
│   ├── frontend/          # Customer-facing pages
│   │   ├── home/
│   │   ├── products/
│   │   ├── cart/
│   │   ├── checkout/
│   │   ├── orders/
│   │   ├── profile/
│   │   └── auth/
│   └── admin/             # Admin panel pages
│       ├── partials/
│       ├── dashboard.php
│       ├── login.php
│       ├── products/
│       ├── categories/
│       ├── orders/
│       ├── users/
│       ├── reviews/
│       └── coupons/
├── assets/                # Static assets
│   ├── css/
│   ├── js/
│   └── images/
├── ai/                    # AI integration endpoints
│   ├── recommendations.php
│   ├── chatbot.php
│   └── analytics.php
├── api/                   # REST API endpoints (future)
└── uploads/               # User uploads
    └── products/
```

## AI Integration Points

The system includes placeholder endpoints that can be replaced with real AI:

| Endpoint | Purpose | AI Integration |
|----------|---------|---------------|
| `/ai/recommendations.php` | Product recommendations | Replace with ML model |
| `/ai/chatbot.php` | Customer support chatbot | Replace with LLM/OpenAI |
| `/ai/analytics.php` | Sales predictions | Replace with forecasting model |

## Security Notes

- Change the default admin password immediately
- Set strong environment variables in production
- Enable HTTPS in production
- Configure proper CORS for API endpoints
- Session security: use `session_regenerate_id()` after login
- CSRF tokens are available via `csrf_token()` function

## Testing

1. Browse to the homepage
2. Register a new user account
3. Browse products and add to cart
4. Complete checkout process
5. Log in to admin panel to manage orders/products

## Customization

- **Theme:** Edit `assets/css/style.css`
- **Layout:** Modify `views/partials/header.php` and `footer.php`
- **Products:** Add via admin panel or directly in database
- **Payment:** Implement payment gateway in `OrderController.php`

## License

MIT
