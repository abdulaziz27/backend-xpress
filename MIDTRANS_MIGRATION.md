# Migrasi dari Stripe ke Midtrans

## Ringkasan Perubahan

Sistem pembayaran telah berhasil diubah dari Stripe ke Midtrans untuk mendukung pasar Indonesia dengan mata uang Rupiah.

## Perubahan yang Dilakukan

### 1. Dependencies
- **Dihapus**: `stripe/stripe-php`
- **Ditambah**: `midtrans/midtrans-php`

### 2. Konfigurasi
- **File**: `config/services.php`
- **Perubahan**: Mengganti konfigurasi Stripe dengan Midtrans
```php
'midtrans' => [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
],
```

### 3. Environment Variables
- **File**: `.env.example`
- **Ditambah**:
```env
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

### 4. Database Migration
- **File**: `database/migrations/2025_09_29_125721_rename_stripe_customer_id_to_midtrans_customer_id.php`
- **Perubahan**: Mengganti kolom `stripe_customer_id` menjadi `midtrans_customer_id` di tabel `users`

### 5. Model User
- **File**: `app/Models/User.php`
- **Perubahan**: Update fillable field dari `stripe_customer_id` ke `midtrans_customer_id`

### 6. PaymentService
- **File**: `app/Services/PaymentService.php`
- **Perubahan Besar**:
  - Mengganti semua integrasi Stripe dengan Midtrans
  - Method baru: `getOrCreateMidtransCustomer()`, `createSnapToken()`, `createPaymentToken()`
  - Webhook handling untuk notifikasi Midtrans
  - Status mapping untuk Midtrans transaction status

### 7. Webhook Controller
- **File**: `app/Http/Controllers/Api/V1/MidtransWebhookController.php`
- **Baru**: Controller untuk menangani webhook notifikasi dari Midtrans

### 8. Routes
- **File**: `routes/api.php`
- **Ditambah**: Route untuk webhook Midtrans
```php
Route::post('/webhooks/midtrans', [App\Http\Controllers\Api\V1\MidtransWebhookController::class, 'handle']);
```

### 9. Tests
- **File**: `tests/Unit/PaymentServiceTest.php` - Updated untuk Midtrans
- **File**: `tests/Feature/MidtransWebhookTest.php` - Test baru untuk webhook

## Fitur Midtrans yang Didukung

1. **Snap Payment**: Untuk pembayaran dengan UI Midtrans
2. **Core API**: Untuk charge langsung dengan saved token
3. **Payment Methods**:
   - Credit Card
   - Bank Transfer (VA)
   - E-Wallet (GoPay, ShopeePay)
   - QRIS
4. **Webhook Notifications**: Untuk update status pembayaran real-time

## Cara Penggunaan

### 1. Setup Environment
```bash
# Sandbox
MIDTRANS_SERVER_KEY=SB-Mid-server-xxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxx
MIDTRANS_IS_PRODUCTION=false

# Production
MIDTRANS_SERVER_KEY=Mid-server-xxx
MIDTRANS_CLIENT_KEY=Mid-client-xxx
MIDTRANS_IS_PRODUCTION=true
```

### 2. Webhook URL
Daftarkan URL webhook di Midtrans Dashboard:
```
https://yourdomain.com/api/v1/webhooks/midtrans
```

### 3. Testing
```bash
# Run payment service tests
php artisan test tests/Unit/PaymentServiceTest.php

# Run webhook tests
php artisan test tests/Feature/MidtransWebhookTest.php
```

## Status Mapping

| Midtrans Status | Local Status |
|----------------|--------------|
| capture, settlement | completed |
| pending | pending |
| deny, cancel, expire | failed |
| refund, partial_refund | refunded |

## Keamanan

1. **Signature Verification**: Semua webhook notification diverifikasi menggunakan signature key
2. **Server Key Protection**: Server key disimpan di environment variables
3. **3DS Security**: Diaktifkan untuk transaksi kartu kredit

## Migration Checklist

- [x] Remove Stripe dependencies
- [x] Add Midtrans dependencies  
- [x] Update configuration
- [x] Migrate database schema
- [x] Update PaymentService
- [x] Create webhook controller
- [x] Add routes
- [x] Update tests
- [x] Add documentation

## Next Steps

1. Setup Midtrans account (Sandbox & Production)
2. Configure webhook URL di Midtrans Dashboard
3. Test payment flow end-to-end
4. Deploy ke production dengan environment variables yang benar