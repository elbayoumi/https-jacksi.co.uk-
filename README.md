# Hiring Task — Laravel Admin/Seller Dashboard (Laravel 11+) — **Full Multi‑Guard (Admin & Seller as Separate Models)**

> Completely rewritten per your notes: **latest Laravel‑ready**, and **Seller has its own guard & provider** separate from users. Clean layering (FormRequests, Services/Repositories), Events, DomPDF, REST API, Sanctum options, Seeders/Factories, and a green Unit Test.

---

## ⚙️ Tech Stack

* **Laravel 11+** (works on latest)
* **PHP 8.2+**, **MySQL 8**
* **Auth (Web)**: Two session guards → `seller`, `admin`
* **Auth (API, optional)**: Sanctum abilities (`seller` / `admin`)
* **PDF**: `barryvdh/laravel-dompdf`
* **UI**: Blade minimal dashboards (admin & seller)
* **Architecture**: FormRequests + Services/Repositories + Events/Listeners + Tests

---

## 🧩 Auth: Guards & Providers (`config/auth.php`)

```php
return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'seller'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'sellers'),
    ],

    'guards' => [
        'seller' => [ // Seller guard (session)
            'driver' => 'session',
            'provider' => 'sellers',
        ],
        'admin' => [ // Admin guard (session)
            'driver' => 'session',
            'provider' => 'admins',
        ],
        // For Sanctum API: set sanctum.php => 'guard' => ['seller','admin'] if needed
    ],

    'providers' => [
        'sellers' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Seller::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'sellers' => [
            'provider' => 'sellers',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
        ],
        'admins' => [
            'provider' => 'admins',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
```

> **Why this design?** Full isolation (tables/models/guards) → cleaner policies, clearer middleware, and zero role‑mixing.

---

## 🧱 Models & Migrations

### `App/Models/Admin.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'admins';

    protected $fillable = ['name','email','password','is_active'];
    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
```

### `App/Models/Seller.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Seller extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'sellers';

    protected $fillable = ['name','email','password','is_active'];
    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function clients() { return $this->hasMany(Client::class, 'seller_id'); }
    public function invoices() { return $this->hasMany(Invoice::class, 'seller_id'); }
}
```

### Migrations

```php
// create_admins_table
Schema::create('admins', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();
});

// create_sellers_table
Schema::create('sellers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();
});

// clients (FK → sellers)
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
    $table->string('name');
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('address')->nullable();
    $table->timestamps();
});

// invoices (FK → sellers, clients)
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
    $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
    $table->string('number')->unique();
    $table->decimal('subtotal', 12, 2)->default(0);
    $table->decimal('tax', 12, 2)->default(0);
    $table->decimal('total', 12, 2)->default(0);
    $table->timestamps();
});

// invoice_items
Schema::create('invoice_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
    $table->string('product_name');
    $table->unsignedInteger('quantity');
    $table->decimal('price', 12, 2);
    $table->decimal('total', 12, 2); // denormalized
    $table->timestamps();
});
```

---

## 🔐 Auth Controllers

```php
class AdminAuthController extends Controller
{
    public function showLogin() { return view('auth.admin-login'); }
    public function login(Request $r) {
        $cred = $r->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::guard('admin')->attempt($cred, $r->boolean('remember'))) {
            $admin = Auth::guard('admin')->user();
            if (!$admin->is_active) { Auth::guard('admin')->logout(); return back()->withErrors(['email'=>'Account disabled']); }
            $r->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }
        return back()->withErrors(['email'=>'Invalid credentials']);
    }
    public function logout(Request $r) { Auth::guard('admin')->logout(); $r->session()->invalidate(); $r->session()->regenerateToken(); return redirect()->route('admin.login'); }
}
```

```php
class SellerAuthController extends Controller
{
    public function showLogin() { return view('auth.seller-login'); }
    public function login(Request $r) {
        $cred = $r->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::guard('seller')->attempt($cred, $r->boolean('remember'))) {
            $seller = Auth::guard('seller')->user();
            if (!$seller->is_active) { Auth::guard('seller')->logout(); return back()->withErrors(['email'=>'Account disabled']); }
            $r->session()->regenerate();
            return redirect()->intended('/seller/dashboard');
        }
        return back()->withErrors(['email'=>'Invalid credentials']);
    }
    public function logout(Request $r) { Auth::guard('seller')->logout(); $r->session()->invalidate(); $r->session()->regenerateToken(); return redirect()->route('seller.login'); }
}
```

---

## 🌐 Routes (Web)

```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class,'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class,'login'])->name('login.post');
    });
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', fn() => view('admin.dashboard'))->name('dashboard');
        Route::resource('sellers', Admin\SellerController::class)->only(['index','update']);
        Route::post('logout', [AdminAuthController::class,'logout'])->name('logout');
    });
});

Route::prefix('seller')->name('seller.').group(function () {
    Route::middleware('guest:seller')->group(function () {
        Route::get('login', [SellerAuthController::class,'showLogin'])->name('login');
        Route::post('login', [SellerAuthController::class,'login'])->name('login.post');
    });
    Route::middleware('auth:seller')->group(function () {
        Route::get('dashboard', fn() => view('seller.dashboard'))->name('dashboard');
        Route::resource('clients', Seller\ClientController::class);
        Route::resource('invoices', Seller\InvoiceController::class);
        Route::get('invoices/{invoice}/pdf', [Seller\InvoicePdfController::class,'show'])->name('invoices.pdf');
    });
});
```

---

## 🧠 Business Layer

* **Repositories:** `ClientRepository`, `InvoiceRepository`
* **Services:** `InvoiceService` (totals + auth per guard)
* **Event:** `InvoiceCreated` → **Listener:** `LogInvoiceCreated` (and optional Notification to Admins)

```php
$user = Auth::guard('admin')->user() ?? Auth::guard('seller')->user();
$isAdmin = Auth::guard('admin')->check();
$isSeller = Auth::guard('seller')->check();
```

---

## 🧾 PDF Export

```php
$pdf = PDF::loadView('pdf.invoice', ['invoice' => $invoice->load('items','client','seller')]);
return $pdf->download($invoice->number.'.pdf');
```

> Guard check: seller can only download own invoices.

---

## 🔐 API (Optional with Sanctum)

* Add `HasApiTokens` to both models.
* `config/sanctum.php`: set `guard => ['seller','admin']`
* Create tokens & abilities:

```php
$adminToken  = $admin->createToken('admin-token',  ['admin'])->plainTextToken;
$sellerToken = $seller->createToken('seller-token', ['seller'])->plainTextToken;
```

* Routes:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('abilities:seller,admin')->group(function () {
        Route::get('invoices', [InvoiceController::class,'index']);
        Route::post('clients', [ClientController::class,'store']);
        Route::post('invoices', [InvoiceController::class,'store']);
    });
    Route::middleware('abilities:admin')->group(function () {
        Route::get('admin/stats', [StatsApiController::class,'index']);
        Route::patch('admin/sellers/{seller}/toggle', [Admin\SellerController::class,'update']);
    });
});
```

---

## 🧪 Tests

```php
it('seller can create invoice with items & totals', function () {
    $seller = Seller::factory()->create();
    $client = Client::factory()->create(['seller_id' => $seller->id]);

    $payload = [
        'client_id' => $client->id,
        'items' => [
            ['product_name' => 'P1', 'quantity' => 2, 'price' => 50],
            ['product_name' => 'P2', 'quantity' => 1, 'price' => 30],
        ],
        'tax' => 18,
    ];

    $this->actingAs($seller, 'seller')
         ->postJson('/api/invoices', $payload)
         ->assertCreated()
         ->assertJsonPath('data.total', 148.0);
});
```

---

## 📊 Admin KPIs

```php
return [
  'total_invoices' => Invoice::count(),
  'total_revenue'  => Invoice::sum('total'),
  'top_sellers'    => Invoice::select('seller_id', DB::raw('SUM(total) as revenue'))
                         ->groupBy('seller_id')->orderByDesc('revenue')
                         ->with('seller:id,name')->take(5)->get(),
];
```

---

## 🧾 README (Essentials)

* **Guards**: `seller` (session), `admin` (session), optional Sanctum
* **Login**: `/seller/login` & `/admin/login`
* **Seeders**: 1 Admin, 3 Sellers, 15 Clients, 40 Invoices
* **Features**: CRUD Clients/Invoices, PDF, Events, Stats, REST API
* **Run**: `php artisan migrate:fresh --seed`
* **Tests**: `php artisan test`

---

## ✅ Deliverables Checklist

* [x] Separate guards & providers (Admin/Seller)
* [x] Seller‑scoped data model (FK → sellers.id)
* [x] FormRequests + Services/Repositories
* [x] Event + Listener (logging/notification)
* [x] DomPDF export
* [x] Seeders/Factories (admin + sellers + data)
* [x] Unit test (invoice creation)
* [x] Clean routes & minimal Blade
