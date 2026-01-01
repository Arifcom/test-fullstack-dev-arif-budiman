# 📦 Products CRUD - Laravel Livewire

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

Modern, secure, and responsive product inventory management system built with Laravel 12 and Livewire 3.

---

## 📋 Table of Contents

- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Application Flow](#-application-flow)
- [Security Features](#-security-features)
- [Installation Guide](#-installation-guide)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Contributing](#-contributing)

---

## ✨ Features

- ✅ **Full CRUD Operations** - Create, Read, Update, Delete products
- ✅ **Real-time Search** - Live product filtering without page reload
- ✅ **Modern UI** - Card-based grid layout with Slate & Indigo color scheme
- ✅ **Delete Confirmation Modal** - Custom modal replacing browser confirm()
- ✅ **Toast Notifications** - Slide-in notifications using Alpine.js
- ✅ **Responsive Design** - Mobile-first approach (1/2/3 column grid)
- ✅ **Form Validation** - Comprehensive validation with unique constraint
- ✅ **Security Hardened** - CSRF, XSS, SQL Injection, Mass Assignment protection
- ✅ **Clean Code** - Full PHPDoc documentation, descriptive naming

---

## 🛠 Technology Stack

### Core Framework
- **[Laravel 12.x](https://laravel.com/)** - PHP web application framework
- **[Livewire 3.x](https://livewire.laravel.com/)** - Full-stack framework for Laravel (includes Alpine.js)
- **PHP 8.2+** - Server-side scripting language

### Frontend
- **[Tailwind CSS 3.x](https://tailwindcss.com/)** - Utility-first CSS framework
- **[Alpine.js](https://alpinejs.dev/)** - Lightweight JavaScript framework (bundled with Livewire 3)
- **[Vite](https://vitejs.dev/)** - Modern frontend build tool

### Database
- **MySQL 8.0+** or **MariaDB** - Relational database

### Development Tools
- **Composer** - PHP dependency manager
- **NPM** - Node package manager
- **Laravel Artisan** - Command-line interface

---

## 🔄 Application Flow

### Detailed Flow by Operation

#### 1. **List Products** (`GET /products`)
1. User navigates to `/products`
2. `ProductIndex` component loads
3. Eloquent query fetches paginated products (10 per page)
4. Search filter applies if query exists
5. Products displayed in responsive grid (3/2/1 columns)

#### 2. **Create Product** (`GET /products/create`)
1. User clicks "Tambah Produk" button
2. Navigate to `/products/create` (using `wire:navigate` for SPA feel)
3. `ProductCreate` component renders empty form
4. User fills: `name`, `amount`, `qty`
5. On submit, Livewire validates:
   - `name`: required, string, max 255, **unique**
   - `amount`: required, numeric, min 0
   - `qty`: required, integer, min 0
6. If valid → Create product → Flash success → Redirect
7. If invalid → Display error messages inline

#### 3. **Edit Product** (`GET /products/{id}/edit`)
1. User clicks "Edit" on product card
2. Navigate to `/products/{id}/edit`
3. `ProductEdit` component loads via `mount($id)`
4. Fetch product from DB, populate form fields
5. User modifies fields
6. On submit, validate with **unique exception** for current product
7. Update database → Redirect with success toast

#### 4. **Delete Product** (Livewire Action)
1. User clicks "Hapus" button on product card
2. `confirmDelete($id)` sets modal state to visible
3. Custom modal appears (Alpine.js `x-show`)
4. User clicks "Ya, Hapus" → `delete()` method executes
5. Product deleted from database
6. Modal closes, **Toast notification** dispatched
7. Product list updates automatically (Livewire reactivity)

#### 5. **Search Products** (Real-time)
1. User types in search input
2. Livewire `wire:model.live="search"` triggers on each keystroke
3. Query re-executed with `WHERE name LIKE %search%`
4. Grid updates instantly without page reload

---

## 🔒 Security Features

### 1. **Form Request Validation**

**Why Form Requests?**

Form Requests provide a **centralized, reusable, and testable** way to validate incoming data. Advantages:

- ✅ **Separation of Concerns**: Validation logic separated from controller/component logic
- ✅ **DRY Principle**: Reusable across multiple controllers/components
- ✅ **Authorization**: Built-in `authorize()` method for permission checks
- ✅ **Custom Error Messages**: Centralized error message definitions
- ✅ **Type Safety**: Forces validation before data reaches application logic

**Implementation:**

```php
// app/Http/Requests/StoreProductRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255|unique:products,name',
        'amount' => 'required|numeric|min:0',
        'qty' => 'required|integer|min:0',
    ];
}
```

**Security Benefits:**
- Prevents **SQL Injection** via type validation
- Prevents **Mass Assignment** via explicit field definition
- Prevents **Buffer Overflow** via length limits
- Prevents **Type Confusion** via strict type checks

**Note**: In this project, validation is implemented directly in Livewire components for simplicity, but follows the same principles.

---

### 2. **SQL Injection Protection**

**How Laravel Prevents SQL Injection:**

Laravel's **Eloquent ORM** and **Query Builder** use **prepared statements** and **parameter binding** automatically:

**❌ Vulnerable (Raw SQL):**
```php
// DON'T DO THIS!
DB::select("SELECT * FROM products WHERE name = '" . $request->input('search') . "'");
// Attacker input: ' OR '1'='1
// Resulting query: SELECT * FROM products WHERE name = '' OR '1'='1' (returns all records!)
```

**✅ Safe (Laravel Query Builder):**
```php
// Laravel automatically binds parameters
Product::where('name', 'like', '%' . $search . '%')->get();
// Internally becomes: SELECT * FROM products WHERE name LIKE ?
// Parameter: %$search% (safely escaped)
```

**How It Works:**
1. Laravel sends query structure to database separately from data
2. Database treats user input as **literal values**, never as SQL code
3. Even if attacker sends `'; DROP TABLE products; --`, it's treated as a string search

**Additional Protections:**
- **Type Casting**: `$casts = ['amount' => 'decimal:2']` ensures numeric types
- **Validation**: `numeric` rule prevents non-numeric input from reaching DB
- **Whitelisting**: Only `$fillable` fields can be mass-assigned

**Example Attack Scenario Prevented:**
```
Attacker input: '; DELETE FROM products WHERE '1'='1
Without protection: Query becomes: ... WHERE name = ''; DELETE FROM products WHERE '1'='1'
With Laravel: Safely escaped as literal string, no command execution
```

---

### 3. **CSRF Protection (Cross-Site Request Forgery)**

**What is CSRF?**

CSRF tricks an authenticated user's browser into making unwanted requests to your application.

**Attack Example:**
```html
<!-- Attacker's malicious website -->
<img src="https://yourapp.com/products/delete/1" />
<!-- If user is logged in, this silently deletes product #1 -->
```

**How Laravel/Livewire Prevents CSRF:**

1. **Token Generation**: Laravel generates unique token per session
2. **Token Validation**: All POST/PUT/DELETE requests must include valid token
3. **Automatic Inclusion**: Livewire automatically includes CSRF token in headers

**Implementation:**
```blade
{{-- resources/views/layouts/app.blade.php --}}
@livewireScripts
{{-- Livewire automatically adds X-CSRF-TOKEN header to all AJAX requests --}}
```

**Verification:**
```javascript
// Check browser DevTools > Network > Request Headers:
X-CSRF-TOKEN: Ag7f3ks9d... (unique token)
X-Livewire: true
```

**Why It's Critical:**
- ✅ Prevents unauthorized actions from external sites
- ✅ Ensures requests originate from your application
- ✅ Protects Delete, Update, Create operations
- ✅ Required for OWASP Top 10 compliance

**What Happens Without CSRF Protection:**
```
1. User logs into your app (gets session cookie)
2. User visits attacker.com (without logging out)
3. attacker.com sends hidden request to yourapp.com/products/delete/5
4. Browser includes your app's cookies automatically
5. Product #5 deleted without user knowledge ❌
```

**With CSRF Protection:**
```
1-3. Same as above
4. Request arrives without valid CSRF token
5. Laravel returns 419 Token Mismatch Error ✅
6. Product safe, attack blocked
```

---

### 4. **XSS Protection (Cross-Site Scripting)**

**How Blade Templates Prevent XSS:**

```blade
{{-- ✅ SAFE: Auto-escaped --}}
<h3>{{ $product->name }}</h3>
{{-- If name = "<script>alert('XSS')</script>" --}}
{{-- Output: &lt;script&gt;alert('XSS')&lt;/script&gt; (displayed as text) --}}

{{-- ❌ DANGEROUS: Not escaped --}}
<h3>{!! $product->name !!}</h3>
{{-- Output: <script>alert('XSS')</script> (executed as code!) --}}
```

**Rule**: Always use `{{ }}` for user input, never `{!! !!}` unless sanitized.

---

### 5. **Mass Assignment Protection**

**The Vulnerability:**

```php
// ❌ Without $fillable protection
Product::create($request->all());
// Attacker sends: name=Laptop&amount=1000&qty=5&id=999
// Result: Could overwrite product ID or inject admin fields!
```

**The Solution:**

```php
// ✅ Protected Model
protected $fillable = ['name', 'amount', 'qty'];

Product::create($request->all());
// Only name, amount, qty accepted. 'id' ignored automatically.
```

---

### 6. **Unique Constraint**

Prevents duplicate product names in database:

```php
'name' => 'required|string|max:255|unique:products,name'
```

**For Updates:**
```php
// Exclude current product from unique check
'name' => 'unique:products,name,' . $this->productId
```

This allows editing product without triggering "name already exists" for its own name.

---

## 📥 Installation Guide

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0 or MariaDB
- (Optional) WAMP/XAMPP/Laravel Herd for local development

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/testing-antariks.git
cd testing-antariks
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Frontend Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testing_antariks
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 5: Create Database

Create a new database in MySQL:

```sql
CREATE DATABASE testing_antariks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or use phpMyAdmin/MySQL Workbench GUI.

### Step 6: Run Migrations

```bash
php artisan migrate
```

This creates the `products` table with columns:
- `id` (primary key)
- `name` (string, unique)
- `amount` (decimal 10,2)
- `qty` (integer)
- `created_at`, `updated_at` (timestamps)

### Step 7: (Optional) Seed Sample Data

```bash
php artisan db:seed
```

### Step 8: Build Frontend Assets

**For Development:**
```bash
npm run dev
```

**For Production:**
```bash
npm run build
```

### Step 9: Serve Application

**Option A: Laravel's built-in server**
```bash
php artisan serve
```
Access at: `http://localhost:8000`

**Option B: Using WAMP/XAMPP**
- Move project to `htdocs` or `www` folder
- Access via: `http://localhost/testing-antariks/public`

**Option C: Laravel Herd (Recommended)**
- Install [Laravel Herd](https://herd.laravel.com/)
- Herd auto-serves projects in parked directories
- Access via: `http://testing-antariks.test`

---

## 🎮 Usage

### Basic Operations

1. **View Products**: Navigate to `/products` or click "Dashboard" in navigation
2. **Add Product**: Click "Tambah Produk" → Fill form → Click "Simpan Produk"
3. **Edit Product**: Click "Edit" on any product card → Modify fields → "Simpan Perubahan"
4. **Delete Product**: Click "Hapus" → Confirm in modal → Product removed with toast
5. **Search**: Type product name in search bar → Results filter in real-time

### Validation Rules

| Field | Rules | Error Message Example |
|-------|-------|----------------------|
| **Name** | Required, Max 255, Unique | "Nama produk sudah digunakan." |
| **Amount** | Required, Numeric, Min 0 | "Harga produk tidak boleh kurang dari 0." |
| **Qty** | Required, Integer, Min 0 | "Jumlah stok harus berupa angka bulat." |

---

## 📁 Project Structure

```
testing-antariks/
├── app/
│   ├── Http/
│   │   └── Requests/
│   │       ├── StoreProductRequest.php    # Validation for create
│   │       └── UpdateProductRequest.php   # Validation for update
│   ├── Livewire/
│   │   ├── ProductIndex.php               # List & delete logic
│   │   ├── ProductCreate.php              # Create logic
│   │   └── ProductEdit.php                # Edit logic
│   └── Models/
│       └── Product.php                    # Eloquent model
├── database/
│   └── migrations/
│       └── XXXX_create_products_table.php # Database schema
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php              # Main layout with toast
│       └── livewire/
│           ├── product-index.blade.php    # Grid view
│           ├── product-create.blade.php   # Create form
│           └── product-edit.blade.php     # Edit form
├── routes/
│   └── web.php                            # Application routes
├── SECURITY.md                            # Detailed security documentation
└── README.md                              # This file
```

---

## 🧪 Testing

### Manual Testing Checklist

- [ ] Create product with valid data → Success
- [ ] Create product with duplicate name → Validation error
- [ ] Create product with negative price → Validation error
- [ ] Edit product keeping same name → Success (unique exception works)
- [ ] Edit product to existing name → Validation error
- [ ] Delete product → Modal appears → Confirm → Product deleted + Toast
- [ ] Delete product → Modal appears → Cancel → Modal closes, product safe
- [ ] Search for product → Results filter instantly
- [ ] Pagination works (if > 10 products)
- [ ] Mobile responsive (resize browser to 375px width)

### Security Testing

```bash
# Test XSS
Try creating product with name: <script>alert('XSS')</script>
Expected: Displays as text, not executed

# Test SQL Injection
Try searching: '; DROP TABLE products; --
Expected: Treated as literal search string, no DB damage

# Test CSRF (requires DevTools)
1. Open Network tab
2. Create/Delete product
3. Verify X-CSRF-TOKEN header present
```

---

## 🤝 Contributing

Contributions welcome! Please follow these guidelines:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes with descriptive messages
4. Follow PSR-12 coding standards
5. Add PHPDoc for new methods
6. Test thoroughly
7. Submit Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📧 Support

For questions or issues:
- Create an [Issue](https://github.com/yourusername/testing-antariks/issues)
- Check [SECURITY.md](SECURITY.md) for security details
- Review [Laravel Documentation](https://laravel.com/docs)

---

## 🙏 Acknowledgments

- **Laravel Team** - For the amazing framework
- **Livewire Team** - For reactive components
- **Tailwind Labs** - For utility-first CSS
- **Alpine.js** - For lightweight interactivity

---

**Built with ❤️ by Arif Budiman**

Last Updated: 2026-01-01
