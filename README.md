# Laravel Product Inventory — Skills Test

A Laravel web application for managing product inventory. Submit products through a Bootstrap form, persist data to a JSON file, and view, edit, or delete entries via Ajax without page reloads.

## Features

- **Product form** with Product name, Quantity in stock, and Price per item
- **JSON file storage** — data saved to `storage/app/products.json` with valid JSON syntax
- **Product table** sorted by datetime submitted, showing:
  - Product name
  - Quantity in stock
  - Price per item
  - Datetime submitted
  - Total value number (`quantity × price`)
- **Sum total row** at the bottom of the table
- **Edit** and **Delete** actions per row (Ajax + confirmation modal)
- **Bootstrap 5** UI with responsive layout and smooth animations
- **No database required** — works out of the box with file-based storage

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.2 or higher |
| Composer | 2.x |
| PHP extensions | `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo` |

Node.js and npm are **not** required. Bootstrap and icons are loaded from CDN.

## Quick Start

### 1. Extract and enter the project directory

```bash
cd Laravel_Test
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Create environment file and generate app key

**Linux / macOS:**

```bash
cp .env.example .env
php artisan key:generate
```

**Windows (PowerShell):**

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Or use the built-in setup script:

```bash
composer run setup
```

### 4. Start the development server

```bash
php artisan serve
```

### 5. Open the application

Visit [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser.

## Usage

### Add a product

1. Fill in **Product name**, **Quantity in stock**, and **Price per item**
2. Click **Submit Product**
3. The table updates instantly via Ajax

### Edit a product

1. Click the pencil icon on any row
2. Update the fields in the modal
3. Click **Save Changes**

### Delete a product

1. Click the trash icon on any row
2. Confirm deletion in the modal

## API Endpoints

| Method | URL | Description |
|---|---|---|
| `GET` | `/` | Product inventory page |
| `GET` | `/products/list` | List all products + sum total (JSON) |
| `POST` | `/products` | Create a new product (JSON) |
| `PUT` | `/products/{id}` | Update a product (JSON) |
| `DELETE` | `/products/{id}` | Delete a product (JSON) |

### Example: Create product

```bash
curl -X POST http://127.0.0.1:8000/products \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{"product_name":"Widget","quantity_in_stock":10,"price_per_item":9.99}'
```

> CSRF token is required for `POST`, `PUT`, and `DELETE` requests. The web UI handles this automatically.

## Data Storage

Products are stored in:

```
storage/app/products.json
```

The file is created automatically on first use. Example structure:

```json
[
    {
        "id": 1,
        "product_name": "Widget",
        "quantity_in_stock": 10,
        "price_per_item": 9.99,
        "datetime_submitted": "2026-07-09 12:00:00"
    }
]
```

`total_value` is calculated at runtime and is not persisted.

## Project Structure

```
app/
├── Http/Controllers/
│   └── ProductController.php    # Web + API endpoints
└── Services/
    └── ProductStorageService.php # JSON read/write logic

public/
└── js/
    └── products.js              # Ajax form, table, edit, delete

resources/views/products/
└── index.blade.php              # Bootstrap UI

routes/
└── web.php                      # Application routes

storage/app/
└── products.json                # Product data (auto-created)
```

## Deployment Notes

1. Point your web server document root to the `public/` directory
2. Ensure `storage/` and `bootstrap/cache/` are writable by the web server
3. Run `composer install --optimize-autoloader --no-dev` in production
4. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
5. Run `php artisan config:cache` after configuring `.env`

### Apache

The included `public/.htaccess` handles URL rewriting. Enable `mod_rewrite`.

### Nginx

```nginx
root /path/to/Laravel_Test/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## Running Tests

```bash
php artisan test
```

## Troubleshooting

| Issue | Solution |
|---|---|
| `500` error on first run | Run `php artisan key:generate` and ensure `storage/` is writable |
| Form submit returns `419` | Clear browser cache; CSRF token is set in the page `<meta>` tag |
| Products not saving | Check write permissions on `storage/app/` |
| Blank page | Enable `APP_DEBUG=true` in `.env` and check `storage/logs/laravel.log` |

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
