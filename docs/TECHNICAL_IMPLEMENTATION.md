# Yousaha ERP - Technical Implementation Guide

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Database Design](#database-design)
3. [Business Logic Implementation](#business-logic-implementation)
4. [Transaction Management](#transaction-management)
5. [Security Implementation](#security-implementation)
6. [API Design Patterns](#api-design-patterns)
7. [Integration Points](#integration-points)
8. [Performance Considerations](#performance-considerations)
9. [Testing Strategy](#testing-strategy)
10. [Deployment Guide](#deployment-guide)

---

## Architecture Overview

### System Architecture
```┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (Blade/JS)    │◄──►│   (Laravel)     │◄──►│   (MySQL)       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │   External      │
                    │   Services      │
                    │   (SMTP/AI)     │
                    └─────────────────┘```
### Technology Stack

- **Backend Framework**: Laravel 10.x
- **Database**: MySQL 8.0+
- **Frontend**: Blade Templates + Bootstrap + jQuery
- **Email**: SMTP Integration
- **AI Integration**: LLM API for employee evaluations
- **File Storage**: Local/S3 compatible storage

### Directory Structure
```yousaha/
├── app/
│   ├── Http/Controllers/     # Request handling
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   ├── Jobs/                # Queue jobs
│   └── Mail/                # Email templates
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/            # Sample data
├── resources/
│   ├── views/              # Blade templates
│   ├── js/                 # Frontend JavaScript
│   └── css/                # Stylesheets
└── routes/
    ├── web.php             # Web routes
    └── api.php             # API routes```
---

## Database Design

### Multi-Tenancy Implementation
```sql
-- All business data tables include company_id for isolation
CREATE TABLE products (
    id BIGINT UNSIGNED PRIMARY KEY,
    company_id BIGINT UNSIGNED,
    name VARCHAR(255),
    -- ... other fields
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company_products (company_id, id)
);```
### Key Design Patterns

#### 1. Status Tracking Tables```sql
-- Status logs for audit trail
CREATE TABLE sales_order_status_logs (
    id BIGINT UNSIGNED PRIMARY KEY,
    sales_order_id BIGINT UNSIGNED,
    status VARCHAR(50),
    changed_at TIMESTAMP,
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id)
);```
#### 2. Detail/Line Item Pattern```sql
-- Master-detail relationship
CREATE TABLE sales_orders (
    id BIGINT UNSIGNED PRIMARY KEY,
    -- master fields
);

CREATE TABLE sales_order_product_lines (
    id BIGINT UNSIGNED PRIMARY KEY,
    sales_order_id BIGINT UNSIGNED,
    product_id BIGINT UNSIGNED,
    quantity INTEGER,
    -- detail fields
);```
#### 3. Financial Double-Entry```sql
CREATE TABLE general_ledgers (
    id BIGINT UNSIGNED PRIMARY KEY,
    total DECIMAL(18,2)  -- Control total
);

CREATE TABLE general_ledger_details (
    id BIGINT UNSIGNED PRIMARY KEY,
    general_ledger_id BIGINT UNSIGNED,
    account_id BIGINT UNSIGNED,
    type ENUM('debit', 'credit'),
    value DECIMAL(18,2)
);```
### Database Relationships

#### Company-Centric Design
All business entities belong to a company:
```php
// Base model with company scope
abstract class CompanyModel extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->currentCompany) {
                $builder->where('company_id', auth()->user()->currentCompany->id);
            }
        });
    }
}```
---

## Business Logic Implementation

### Service Layer Pattern

#### 1. Order Processing Service
```php
<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\Delivery;
use App\Models\Income;
use App\Models\GeneralLedger;
use Illuminate\Support\Facades\DB;

class SalesOrderService
{
    public function updateOrderStatus(SalesOrder $order, string $newStatus): bool
    {
        return DB::transaction(function () use ($order, $newStatus) {
            // 1. Update order status
            $order->update(['status' => $newStatus]);
            
            // 2. Log status change
            $order->statusLogs()->create([
                'status' => $newStatus,
                'changed_at' => now()
            ]);
            
            // 3. Handle status-specific logic
            switch ($newStatus) {
                case 'accepted':
                    $this->reserveStock($order);
                    break;
                case 'sent':
                    $this->createDelivery($order);
                    break;
                case 'done':
                    $this->recordIncome($order);
                    $this->createGeneralLedgerEntry($order);
                    break;
                case 'cancel':
                    $this->releaseStock($order);
                    break;
            }
            
            return true;
        });
    }
    
    private function reserveStock(SalesOrder $order): void
    {
        foreach ($order->productLines as $line) {
            $stock = Stock::where('product_id', $line->product_id)->first();
            if ($stock) {
                $stock->increment('quantity_reserve', $line->quantity);
                $stock->decrement('quantity_saleable', $line->quantity);
            }
        }
    }
    
    private function createDelivery(SalesOrder $order): void
    {
        $delivery = Delivery::create([
            'company_id' => $order->company_id,
            'delivery_address' => $order->customer->address,
            'scheduled_at' => now()->addDay(),
            'reference' => $order->number,
            'status' => 'draft'
        ]);
        
        foreach ($order->productLines as $line) {
            $delivery->productLines()->create([
                'product_id' => $line->product_id,
                'quantity' => $line->quantity
            ]);
        }
    }
}```
#### 2. Stock Management Service
```php
<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\StockDetail;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function updateStock(array $stockData): bool
    {
        return DB::transaction(function () use ($stockData) {
            $stock = Stock::findOrFail($stockData['stock_id']);
            
            // Record before state
            $beforeState = $stock->only([
                'quantity_total', 'quantity_reserve', 
                'quantity_saleable', 'quantity_incoming'
            ]);
            
            // Update stock
            $stock->update($stockData);
            
            // Create history record
            $this->createStockHistory($stock, $beforeState, $stockData);
            
            // Update stock details
            if (isset($stockData['stock_details'])) {
                $this->updateStockDetails($stock, $stockData['stock_details']);
            }
            
            return true;
        });
    }
    
    private function createStockHistory(Stock $stock, array $before, array $after): void
    {
        StockHistory::create([
            'stock_id' => $stock->id,
            'quantity_total_before' => $before['quantity_total'],
            'quantity_total_after' => $after['quantity_total'],
            'quantity_reserve_before' => $before['quantity_reserve'],
            'quantity_reserve_after' => $after['quantity_reserve'],
            'quantity_saleable_before' => $before['quantity_saleable'],
            'quantity_saleable_after' => $after['quantity_saleable'],
            'quantity_incoming_before' => $before['quantity_incoming'],
            'quantity_incoming_after' => $after['quantity_incoming'],
            'type' => $after['type'] ?? 'adjustment',
            'reference' => $after['reference'] ?? 'manual',
            'date' => now()
        ]);
    }
}```
### Business Rules Implementation

#### 1. Order Status Validation
```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidOrderStatusTransition implements Rule
{
    private $currentStatus;
    private $allowedTransitions = [
        'draft' => ['waiting', 'cancel'],
        'waiting' => ['accepted', 'cancel'],
        'accepted' => ['sent', 'cancel'],
        'sent' => ['done', 'cancel'],
        'done' => [],
        'cancel' => []
    ];
    
    public function __construct($currentStatus)
    {
        $this->currentStatus = $currentStatus;
    }
    
    public function passes($attribute, $value)
    {
        return in_array($value, $this->allowedTransitions[$this->currentStatus] ?? []);
    }
    
    public function message()
    {
        return 'Invalid status transition from ' . $this->currentStatus;
    }
}```
#### 2. Financial Balance Validation
```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BalancedLedgerEntry implements Rule
{
    public function passes($attribute, $value)
    {
        $debits = collect($value)->where('type', 'debit')->sum('value');
        $credits = collect($value)->where('type', 'credit')->sum('value');
        
        return abs($debits - $credits) < 0.01; // Allow for floating point precision
    }
    
    public function message()
    {
        return 'Debit and credit amounts must be equal';
    }
}```
---

## Transaction Management

### Database Transactions

#### 1. Complex Business Operations
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class ReceiptService
{
    public function processGoodsReceiving(Receipt $receipt, array $receivedItems): bool
    {
        try {
            DB::beginTransaction();
            
            // 1. Update receipt status
            $receipt->update(['status' => 'done']);
            
            // 2. Log status change
            $receipt->statusLogs()->create([
                'status' => 'done',
                'changed_at' => now()
            ]);
            
            // 3. Update stock levels
            foreach ($receivedItems as $item) {
                $this->updateStockFromReceipt($item);
            }
            
            // 4. Create stock details
            foreach ($receivedItems as $item) {
                $this->createStockDetails($item);
            }
            
            // 5. Update stock history
            $this->createStockHistoryFromReceipt($receipt, $receivedItems);
            
            DB::commit();
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function updateStockFromReceipt(array $item): void
    {
        $stock = Stock::where('product_id', $item['product_id'])->first();
        
        if (!$stock) {
            throw new Exception('Stock record not found for product');
        }
        
        $stock->increment('quantity_total', $item['quantity_received']);
        $stock->increment('quantity_saleable', $item['quantity_received']);
        $stock->decrement('quantity_incoming', $item['quantity_expected']);
    }
}```
#### 2. Transaction Middleware
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class DatabaseTransaction
{
    public function handle($request, Closure $next)
    {
        DB::beginTransaction();
        
        try {
            $response = $next($request);
            
            if ($response->getStatusCode() >= 400) {
                DB::rollBack();
            } else {
                DB::commit();
            }
            
            return $response;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}```
---

## Security Implementation

### Authentication & Authorization

#### 1. Email Verification
```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\EmailVerification;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    public function registerUser(array $userData): array
    {
        $user = User::create([
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'name' => $userData['name']
        ]);
        
        $token = $this->createEmailVerification($user->email);
        
        Mail::to($user->email)->send(new EmailVerificationMail($token));
        
        return [
            'user' => $user,
            'verification_token' => $token
        ];
    }
    
    private function createEmailVerification(string $email): string
    {
        $token = Str::random(64);
        
        EmailVerification::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now()
        ]);
        
        return $token;
    }
    
    public function verifyEmail(string $token): bool
    {
        $verification = EmailVerification::where('token', $token)
            ->where('created_at', '>', now()->subHours(24))
            ->first();
            
        if (!$verification) {
            return false;
        }
        
        User::where('email', $verification->email)
            ->update(['verify_at' => now()]);
            
        $verification->delete();
        
        return true;
    }
}```
#### 2. Company Data Isolation
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has company access
        $hasCompany = $user->companies()->exists() || $user->employee()->exists();
        
        if (!$hasCompany) {
            return redirect()->route('company.create');
        }
        
        return $next($request);
    }
}```
### Data Validation

#### 1. Request Validation
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\BalancedLedgerEntry;

class GeneralLedgerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'number' => 'required|string|unique:general_ledgers,number',
            'date' => 'required|date',
            'total' => 'required|numeric|min:0',
            'details' => ['required', 'array', 'min:2', new BalancedLedgerEntry],
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.type' => 'required|in:debit,credit',
            'details.*.value' => 'required|numeric|min:0.01'
        ];
    }
}```
---

## API Design Patterns

### RESTful Controllers

#### 1. Resource Controller Pattern
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('company')
            ->paginate(request('per_page', 15));
            
        return ProductResource::collection($products);
    }
    
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        
        return new ProductResource($product);
    }
    
    public function show(Product $product)
    {
        return new ProductResource($product->load('company', 'stocks'));
    }
    
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        
        return new ProductResource($product);
    }
    
    public function destroy(Product $product)
    {
        // Check for related records
        if ($product->salesOrderLines()->exists() || $product->stocks()->exists()) {
            return response()->json([
                'message' => 'Cannot delete product with existing orders or stock'
            ], 422);
        }
        
        $product->delete();
        
        return response()->json(['message' => 'Product deleted successfully']);
    }
}```
#### 2. API Resource Pattern
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'salesperson' => $this->salesperson,
            'total' => $this->total,
            'status' => $this->status,
            'deadline' => $this->deadline->format('Y-m-d'),
            'product_lines' => SalesOrderProductLineResource::collection(
                $this->whenLoaded('productLines')
            ),
            'status_logs' => SalesOrderStatusLogResource::collection(
                $this->whenLoaded('statusLogs')
            ),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString()
        ];
    }
}```
### Error Handling

#### 1. Global Exception Handler
```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($exception);
        }
        
        return parent::render($request, $exception);
    }
    
    private function handleApiException(Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors()
            ], 422);
        }
        
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found'
            ], 404);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Internal server error'
        ], 500);
    }
}```
---

## Integration Points

### Email Integration

#### 1. SMTP Configuration
```php
// config/mail.php
return [
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
        ],
    ],
];```
#### 2. Email Templates
```php
<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailVerificationMail extends Mailable
{
    public $token;
    
    public function __construct($token)
    {
        $this->token = $token;
    }
    
    public function build()
    {
        return $this->subject('Verify Your Email Address')
                   ->view('emails.verify-email')
                   ->with(['token' => $this->token]);
    }
}```
### AI Integration

#### 1. Employee Evaluation Service
```php
<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\Http;

class AIEvaluationService
{
    public function generateAnnualEvaluation(Employee $employee, int $year): array
    {
        // Gather employee data
        $performanceData = $this->gatherPerformanceData($employee, $year);
        
        // Send to AI service
        $response = Http::post(config('ai.evaluation_endpoint'), [
            'employee_data' => $performanceData,
            'evaluation_year' => $year
        ]);
        
        if ($response->failed()) {
            throw new Exception('AI evaluation service unavailable');
        }
        
        return $response->json();
    }
    
    private function gatherPerformanceData(Employee $employee, int $year): array
    {
        return [
            'attendance_record' => $employee->attendances()
                ->whereYear('date', $year)
                ->selectRaw('
                    COUNT(*) as total_days,
                    AVG(TIMESTAMPDIFF(HOUR, clock_in, clock_out)) as avg_hours,
                    COUNT(CASE WHEN TIME(clock_in) > "09:00:00" THEN 1 END) as late_days
                ')
                ->first(),
            'time_off_record' => $employee->timeOffs()
                ->whereYear('date', $year)
                ->count(),
            'department_info' => $employee->department->name,
            'position' => $employee->position,
            'years_of_service' => $employee->years_of_service
        ];
    }
}```
---

## Performance Considerations

### Database Optimization

#### 1. Indexing Strategy
```sql
-- Compound indexes for common queries
CREATE INDEX idx_company_date ON sales_orders (company_id, created_at);
CREATE INDEX idx_status_deadline ON sales_orders (status, deadline);
CREATE INDEX idx_product_warehouse ON stocks (product_id, warehouse_id);

-- Full-text search indexes
CREATE FULLTEXT INDEX idx_product_search ON products (name, sku);```
#### 2. Query Optimization
```php
// Eager loading to prevent N+1 queries
$orders = SalesOrder::with([
    'customer:id,name,email',
    'productLines.product:id,name,price',
    'statusLogs' => function($query) {
        $query->latest()->limit(5);
    }
])->paginate();

// Use select to limit columns
$products = Product::select('id', 'name', 'sku', 'price')
    ->where('company_id', auth()->user()->company_id)
    ->get();```
### Caching Strategy

#### 1. Model Caching
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    public function getPriceAttribute($value)
    {
        return Cache::remember(
            "product_price_{$this->id}",
            3600,
            fn() => $value
        );
    }
    
    protected static function booted()
    {
        static::updated(function ($product) {
            Cache::forget("product_price_{$product->id}");
        });
    }
}```
---

## Testing Strategy

### Unit Tests

#### 1. Service Testing
```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SalesOrderService;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesOrderServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_update_order_status()
    {
        $order = SalesOrder::factory()->create(['status' => 'draft']);
        $service = new SalesOrderService();
        
        $result = $service->updateOrderStatus($order, 'waiting');
        
        $this->assertTrue($result);
        $this->assertEquals('waiting', $order->fresh()->status);
        $this->assertDatabaseHas('sales_order_status_logs', [
            'sales_order_id' => $order->id,
            'status' => 'waiting'
        ]);
    }
}```
### Feature Tests

#### 1. API Testing
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_product()
    {
        $user = User::factory()->create();
        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST001',
            'type' => 'goods',
            'price' => 100.00
        ];
        
        $response = $this->actingAs($user)
            ->postJson('/api/products', $productData);
            
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'sku', 'type', 'price']
            ]);
    }
}```
---

## Deployment Guide

### Environment Setup

#### 1. Production Configuration
```bash
# .env.production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=yousaha_prod
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password```
#### 2. Server Requirements
```bash
# PHP Extensions
php8.1-fpm
php8.1-mysql
php8.1-redis
php8.1-mbstring
php8.1-xml
php8.1-curl
php8.1-zip
php8.1-gd

# Web Server (Nginx)
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/yousaha/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}```
### Deployment Process

#### 1. Automated Deployment Script
```bash
#!/bin/bash

# deployment.sh
set -e

echo "Starting deployment..."

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo service php8.1-fpm restart
sudo service nginx restart

echo "Deployment completed successfully!"```
### Monitoring & Maintenance

#### 1. Log Monitoring
```php
// config/logging.php
'channels' => [
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
    
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
    ],
]```
#### 2. Health Checks
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    public function check()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage()
        ];
        
        $allHealthy = collect($checks)->every(fn($check) => $check === 'ok');
        
        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks
        ], $allHealthy ? 200 : 503);
    }
    
    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
            return 'ok';
        } catch (\Exception $e) {
            return 'failed';
        }
    }
    
    private function checkCache(): string
    {
        try {
            Cache::put('health_check', 'ok', 10);
            return Cache::get('health_check') === 'ok' ? 'ok' : 'failed';
        } catch (\Exception $e) {
            return 'failed';
        }
    }
}```
This technical implementation guide provides comprehensive coverage of the system architecture, patterns, and deployment strategies for the Yousaha ERP system based on the sequence diagrams and business requirements.
