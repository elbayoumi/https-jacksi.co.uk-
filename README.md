# 🧾 Multi-Guard Invoicing Dashboard (Laravel 12+)

A **robust business dashboard** built with Laravel 12+ supporting **multi-guard authentication** for Admins and Sellers. Features RESTful APIs, PDF exports, service-layered architecture, and role-based access control. Ideal as a starter template for a full-scale SaaS invoicing platform.

---

## 📚 Table of Contents

- [Overview](#-overview)
- [Tech Stack](#️-tech-stack)
- [Architecture](#️-architecture)
- [Authentication Guards](#-authentication-guards)
- [Database Schema](#-database-schema)
- [Functional Features](#-functional-features)
- [Events & Notifications](#-events--notifications)
- [Business Logic Layer](#-business-logic-layer)
- [Routes Organization](#-routes-organization)
- [REST API Endpoints](#-rest-api-endpoints)
- [Testing](#-testing)
- [Seeders & Dummy Data](#-seeders--dummy-data)
- [PDF Export](#-pdf-export)
- [Quality Standards](#-quality-standards)
- [Quick Setup Guide](#-quick-setup-guide)
- [API Tokens](#-api-tokens)
- [Developer Notes](#-developer-notes)
- [Credits](#-credits)

---

## 📋 Overview

A Laravel-based invoicing system with two distinct user roles:

- **Admin**: Manages all sellers, clients, and invoices.
- **Seller**: Manages their own clients and invoices independently.

Built with scalability and maintainability in mind, the project includes:

- 🧩 RESTful API
- 🖥️ Blade-based dashboard
- 🧠 Service & Repository pattern
- 🔒 Multi-guard session & token auth
- 📤 DomPDF invoice export
- 📬 Notifications, Events, Listeners
- ✅ PHPUnit / Pest testing

---

## ⚙️ Tech Stack

| Component       | Tool / Version               |
|----------------|------------------------------|
| Framework       | Laravel 12.x                 |
| Language        | PHP 8.2+                     |
| Database        | MySQL 8+                     |
| Auth            | Multi-guard (Admin & Seller) |
| API Auth (Opt.) | Laravel Sanctum              |
| PDF Export      | barryvdh/laravel-dompdf      |
| UI              | Blade + Bootstrap            |
| Testing         | PHPUnit / Pest               |
| Seeding         | Factories + Faker            |

---

## 🏗️ Architecture

app/
├── Contracts/
├── Services/
├── Events/
├── Listeners/
├── Notifications/
├── Http/
│ ├── Controllers/
│ │ ├── Admin/
│ │ ├── Seller/
│ │ └── API/
│ ├── Requests/
│ └── Middleware/
├── Models/
├── Providers/
resources/
├── views/admin/
└── views/seller/
routes/
├── web/
│ ├── admin.php
│ ├── seller.php
│ └── shared.php
└── api.php

pgsql
Copy code

---

## 🔐 Authentication Guards

| Guard    | Provider | Model                  | Role               | Type           |
|----------|----------|------------------------|--------------------|----------------|
| `admin`  | `admins` | `App\Models\Admin`     | System Admin       | Session / API  |
| `seller` | `sellers`| `App\Models\Seller`    | Business Owner     | Session / API  |

### Configuration (`config/auth.php`):

```php
'guards' => [
    'admin' => ['driver' => 'session', 'provider' => 'admins'],
    'seller' => ['driver' => 'session', 'provider' => 'sellers'],
],
'providers' => [
    'admins' => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
    'sellers' => ['driver' => 'eloquent', 'model' => App\Models\Seller::class],
],
🗃️ Database Schema
Entity Relationship:

scss
Copy code
Admin (1) ── (∞) Seller ── (∞) Client ── (∞) Invoice ── (∞) InvoiceItem
Table	Key Fields	Relationships
admins	id, name, email, is_active	Oversees sellers
sellers	id, name, email, password	Has many clients & invoices
clients	id, seller_id, name, email, address	Belongs to seller
invoices	id, seller_id, client_id, total, number	Belongs to seller & client
invoice_items	id, invoice_id, name, qty, price	Belongs to invoice

🧩 Functional Features
👩‍💼 Seller Role
Register/login via Laravel Breeze

CRUD operations for clients

Create multi-line item invoices

Automatic total calculation

Export invoices to PDF

Dashboard with total revenue & sales

🧑‍💻 Admin Role
View/manage all sellers, clients, and invoices

Enable/disable sellers

System-wide stats: revenue, invoices, top sellers

Receive notifications on new invoices

🔄 Events & Notifications
Event	Trigger	Listener/Notification
InvoiceCreated	After creation	Logs invoice + triggers notification
NewInvoiceNotification	Listener	Sends mail & DB notification to admins

🧠 Business Logic Layer
All domain rules are encapsulated in InvoiceService:

Transactional operations

Auto-calculate totals

Auto-generate invoice numbers

Fires InvoiceCreated on success

Interface Binding
php
Copy code
use App\Contracts\InvoiceServiceInterface;
use App\Services\InvoiceService;

public function register(): void
{
    $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
}
🌐 Routes Organization
File	Purpose
web/admin.php	Admin dashboard, auth, seller mgmt
web/seller.php	Seller dashboard, clients, invoices
web/shared.php	Common landing / auth redirects
api.php	Token-authenticated REST endpoints

php
Copy code
Route::prefix('admin')->middleware('web')->group(base_path('routes/web/admin.php'));
Route::prefix('seller')->middleware('web')->group(base_path('routes/web/seller.php'));
📤 REST API Endpoints
Method	Endpoint	Role	Description
GET	/api/invoices	Seller/Admin	List all invoices
GET	/api/invoices/{id}	Seller/Admin	View invoice detail
POST	/api/invoices	Seller	Create new invoice
PUT	/api/invoices/{id}	Seller	Update existing invoice
DELETE	/api/invoices/{id}	Seller	Delete invoice
GET	/api/admin/stats	Admin	View system stats

🧪 Testing
Example
php
Copy code
Sanctum::actingAs($seller, ['seller']);

$this->postJson('/api/invoices', [
  'client_id' => $client->id,
  'items' => [
    ['product_name' => 'Service A', 'quantity' => 2, 'price' => 50],
    ['product_name' => 'Service B', 'quantity' => 1, 'price' => 30],
  ]
])->assertCreated()
  ->assertJsonPath('data.total', 130);
Run Tests
bash
Copy code
php artisan test
🧩 Seeders & Dummy Data
Seeders automatically generate:

1 Admin (admin@example.com)

3 Sellers, each with:

5 Clients

3 Invoices per client

2 Items per invoice

Run Seeder
bash
Copy code
php artisan migrate:fresh --seed
🧾 PDF Export
Export invoices via DomPDF:

Template: /resources/views/pdf/invoice.blade.php

Controller Usage:

php
Copy code
$pdf = PDF::loadView('pdf.invoice', ['invoice' => $invoice->load('items', 'client', 'seller')]);
return $pdf->download($invoice->number . '.pdf');
🧱 Quality Standards
✅ SOLID Principles
✅ Service/Repository abstraction
✅ Role-based access (guards)
✅ FormRequest validation
✅ Modular route files
✅ Event-driven logging & notifications
✅ Factories + seeders for demo data
✅ Controllers < 100 lines

🚀 Quick Setup Guide
bash
Copy code
# 1️⃣ Clone the repo
git clone https://github.com/<your-username>/<repo-name>.git
cd <repo-name>

# 2️⃣ Install dependencies
composer install
npm install && npm run build

# 3️⃣ Configure environment
cp .env.example .env
php artisan key:generate

# 4️⃣ Migrate & seed
php artisan migrate:fresh --seed

# 5️⃣ Start the server
php artisan serve
Default Credentials
Role	Email	Password
Admin	admin@example.com	password
Seller	seller1@example.com	password

🧭 API Tokens (Optional Sanctum Setup)
php
Copy code
// Admin
$adminToken = $admin->createToken('admin-token', ['admin'])->plainTextToken;

// Seller
$sellerToken = $seller->createToken('seller-token', ['seller'])->plainTextToken;
Use in Headers:

http
Copy code
Authorization: Bearer <token>
Accept: application/json
🧰 Developer Notes
All route files auto-loaded from /routes/web

Use middleware: auth:admin or auth:seller

Prefer service injection over direct model calls

Extend to multi-tenancy via tenant_id pattern

Blade components easily swappable with Vue or Livewire

🏁 Credits
Crafted with ❤️ by Mohamed Ashraf
CTO / Full-Stack Laravel Developer

Designed as a production-ready or extensible base for multi-tenant SaaS platforms.

