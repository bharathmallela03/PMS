<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Registration
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Password Setup for Invited Users
// Password setup routes
Route::get('/setup-password/{token}', [AuthController::class, 'showPasswordSetup'])->name('password.setup');
Route::post('/setup-password', [AuthController::class, 'setupPassword'])->name('password.setup.store');

// Admin Routes
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Pharmacist Management
    Route::get('/pharmacists', [AdminController::class, 'pharmacists'])->name('admin.pharmacists');
    Route::get('/pharmacists/create', [AdminController::class, 'createPharmacist'])->name('admin.pharmacists.create');
    Route::post('/pharmacists', [AdminController::class, 'storePharmacist'])->name('admin.pharmacists.store');
    Route::get('/pharmacists/{id}/edit', [AdminController::class, 'editPharmacist'])->name('admin.pharmacists.edit');
    Route::put('/pharmacists/{id}', [AdminController::class, 'updatePharmacist'])->name('admin.pharmacists.update');
    Route::delete('/pharmacists/{id}', [AdminController::class, 'deletePharmacist'])->name('admin.pharmacists.delete');
    Route::post('/pharmacists/{id}/resend-setup-mail', [AdminController::class, 'resendPasswordSetupMail'])->name('admin.pharmacists.resend-setup-mail');

    // Supplier Management
    Route::get('/suppliers', [AdminController::class, 'suppliers'])->name('admin.suppliers');
    Route::get('/suppliers/create', [AdminController::class, 'createSupplier'])->name('admin.suppliers.create');
    Route::post('/suppliers', [AdminController::class, 'storeSupplier'])->name('admin.suppliers.store');
    Route::get('/suppliers/{id}/edit', [AdminController::class, 'editSupplier'])->name('admin.suppliers.edit');
    Route::put('/suppliers/{id}', [AdminController::class, 'updateSupplier'])->name('admin.suppliers.update');
    Route::delete('/suppliers/{id}', [AdminController::class, 'deleteSupplier'])->name('admin.suppliers.delete');

    // Customer Management
    Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::delete('/customers/{id}', [AdminController::class, 'deleteCustomer'])->name('admin.customers.delete');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('admin.reports.export');
});

// Pharmacist Routes
Route::prefix('pharmacist')->middleware(['auth:pharmacist'])->group(function () {
    Route::get('/dashboard', [PharmacistController::class, 'dashboard'])->name('pharmacist.dashboard');

    // Medicine Management
    Route::get('/medicines', [PharmacistController::class, 'medicines'])->name('pharmacist.medicines');
    Route::get('/medicines/create', [PharmacistController::class, 'createMedicine'])->name('pharmacist.medicines.create');
    Route::post('/medicines', [PharmacistController::class, 'storeMedicine'])->name('pharmacist.medicines.store');
    Route::get('/medicines/{id}/edit', [PharmacistController::class, 'editMedicine'])->name('pharmacist.medicines.edit');
    Route::put('/medicines/{id}', [PharmacistController::class, 'updateMedicine'])->name('pharmacist.medicines.update');
    Route::delete('/medicines/{id}', [PharmacistController::class, 'deleteMedicine'])->name('pharmacist.medicines.delete');
    Route::post('/medicines/{id}/update-stock', [PharmacistController::class, 'updateStock'])->name('pharmacist.medicines.update-stock');
    Route::get('/medicines/export', [PharmacistController::class, 'exportMedicines'])->name('pharmacist.medicines.export');

    // Company Management
    Route::get('/companies', [PharmacistController::class, 'companies'])->name('pharmacist.companies');
    Route::post('/companies', [PharmacistController::class, 'storeCompany'])->name('pharmacist.companies.store');

    // Billing
    Route::get('/billing', [PharmacistController::class, 'billingIndex'])->name('pharmacist.billing');
    Route::post('/billing/store', [PharmacistController::class, 'storeBilling'])->name('pharmacist.billing.store');
    Route::get('/billing/{id}', [PharmacistController::class, 'showBill'])->name('pharmacist.billing.show');
    Route::get('/billing/{id}/print', [PharmacistController::class, 'printBill'])->name('pharmacist.billing.print');
    Route::put('/billing/{id}/status', [PharmacistController::class, 'updateBillStatus'])->name('pharmacist.billing.status');
    Route::get('/billing/search', [PharmacistController::class, 'searchBills'])->name('pharmacist.billing.search');
    Route::get('/search-medicines', [PharmacistController::class, 'searchMedicines'])->name('search.medicines');
    Route::get('/billing/{bill}', [PharmacistController::class, 'showBill'])->name('pharmacist.billing.show');
    Route::get('/billing/{bill}/print', [PharmacistController::class, 'printBill'])->name('pharmacist.billing.print');
    Route::delete('/billing/{bill}', [PharmacistController::class, 'deleteBill'])->name('pharmacist.billing.delete');

    // Orders
    Route::get('/orders', [PharmacistController::class, 'orders'])->name('pharmacist.orders');
    Route::put('/orders/{id}/status', [PharmacistController::class, 'updateOrderStatus'])->name('pharmacist.orders.status');

    Route::get('/orders/{id}', [PharmacistController::class, 'showOrder'])->name('pharmacist.orders.show');
    Route::get('/orders/{id}/invoice/download', [PharmacistController::class, 'downloadInvoice'])->name('pharmacist.billing.invoice.download');
    Route::put('/orders/{id}/status', [PharmacistController::class, 'updateOrderStatus'])->name('pharmacist.orders.status');

    // Reports
    Route::get('/reports/sales', [PharmacistController::class, 'salesReport'])->name('pharmacist.reports.sales');
    Route::get('/reports/sales/export', [PharmacistController::class, 'exportSalesReport'])->name('pharmacist.reports.sales.export');
    Route::get('/reports/inventory', [PharmacistController::class, 'inventoryReport'])->name('pharmacist.reports.inventory');

    // Stock Alerts
    Route::get('/stock-alerts', [PharmacistController::class, 'stockAlerts'])->name('pharmacist.stock-alerts');
    Route::post('/stock-alerts/request-restock', [PharmacistController::class, 'requestRestock'])->name('pharmacist.restock.request');

});

// Supplier Routes
// Route::prefix('supplier')->middleware(['auth:supplier'])->group(function () {
//     Route::get('/dashboard', [SupplierController::class, 'dashboard'])->name('supplier.dashboard');

//     // Medicine Management
//     Route::get('/medicines', [SupplierController::class, 'medicines'])->name('supplier.medicines');
//     Route::get('/medicines/create', [SupplierController::class, 'createMedicine'])->name('supplier.medicines.create');
//     Route::post('/medicines', [SupplierController::class, 'storeMedicine'])->name('supplier.medicines.store');
//     Route::get('/medicines/{id}/edit', [SupplierController::class, 'editMedicine'])->name('supplier.medicines.edit');
//     Route::put('/medicines/{id}', [SupplierController::class, 'updateMedicine'])->name('supplier.medicines.update');
//     Route::delete('/medicines/{id}', [SupplierController::class, 'deleteMedicine'])->name('supplier.medicines.delete');

//     // Bulk Upload
//     Route::get('/medicines/bulk-upload', [SupplierController::class, 'bulkUpload'])->name('supplier.medicines.bulk-upload');
//     Route::post('/medicines/bulk-upload', [SupplierController::class, 'processBulkUpload'])->name('supplier.medicines.process-bulk-upload');
//     Route::get('/medicines/download-template', [SupplierController::class, 'downloadTemplate'])->name('supplier.medicines.download-template');

//     // Company Management
//     Route::get('/companies', [SupplierController::class, 'companies'])->name('supplier.companies');
//     Route::post('/companies', [SupplierController::class, 'storeCompany'])->name('supplier.companies.store');

//     // Reports
//     Route::get('/reports/inventory', [SupplierController::class, 'inventoryReport'])->name('supplier.reports.inventory');
//     Route::get('/reports/inventory/export', [SupplierController::class, 'exportInventoryReport'])->name('supplier.reports.inventory.export');

//     // Stock Requests
//     Route::get('/stock-requests', [SupplierController::class, 'stockRequests'])->name('supplier.stock-requests');
//     Route::post('/stock-requests/{id}/fulfill', [SupplierController::class, 'fulfillStockRequest'])->name('supplier.stock-requests.fulfill');
// });

// Supplier Routes
Route::prefix('supplier')->middleware(['auth:supplier'])->name('supplier.')->group(function () {
    Route::get('/dashboard', [SupplierController::class, 'dashboard'])->name('dashboard');
    
    // Medicine Management
    Route::get('/medicines', [SupplierController::class, 'medicines'])->name('medicines');
    Route::get('/medicines/create', [SupplierController::class, 'createMedicine'])->name('medicines.create');
    Route::post('/medicines', [SupplierController::class, 'storeMedicine'])->name('medicines.store');
    Route::get('/medicines/{id}/edit', [SupplierController::class, 'editMedicine'])->name('medicines.edit');
    Route::put('/medicines/{id}', [SupplierController::class, 'updateMedicine'])->name('medicines.update');
    Route::delete('/medicines/{id}', [SupplierController::class, 'deleteMedicine'])->name('medicines.delete');
    
    // Bulk Upload
    Route::get('/medicines/bulk-upload', [SupplierController::class, 'bulkUpload'])->name('medicines.bulk-upload');
    Route::post('/medicines/bulk-upload', [SupplierController::class, 'processBulkUpload'])->name('medicines.process-bulk-upload');
    Route::get('/medicines/download-template', [SupplierController::class, 'downloadTemplate'])->name('medicines.download-template');

    
    // DELETE THE TWO LINES BELOW
    Route::get('/companies', [SupplierController::class, 'companies'])->name('supplier.companies');
    Route::post('/companies', [SupplierController::class, 'storeCompany'])->name('supplier.companies.store');
    
    // Reports
    Route::get('/reports/inventory', [SupplierController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/inventory/export', [SupplierController::class, 'exportInventoryReport'])->name('reports.inventory.export');
    
    // Stock Requests
    Route::get('/stock-requests', [SupplierController::class, 'stockRequests'])->name('stock-requests');
    Route::post('/stock-requests/{id}/fulfill', [SupplierController::class, 'fulfillStockRequest'])->name('stock-requests.fulfill');
});

// Customer Routes
Route::prefix('customer')->middleware(['auth:customer'])->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');

    // Medicine Browse
    Route::get('/medicines', [CustomerController::class, 'medicines'])->name('customer.medicines');
    Route::get('/medicines/{id}', [CustomerController::class, 'showMedicine'])->name('customer.medicines.show');

    // Cart
    Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
    Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
    Route::put('/cart/{id}', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
    Route::delete('/cart/{id}', [CustomerController::class, 'removeFromCart'])->name('customer.cart.remove');
    Route::delete('/cart', [CustomerController::class, 'clearCart'])->name('customer.cart.clear');

    // Checkout
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/checkout', [CustomerController::class, 'placeOrder'])->name('customer.checkout.place-order');


    Route::post('/place-order', [CustomerController::class, 'placeOrder'])->name('customer.order.place');
    Route::get('/orders/{id}/invoice/download', [CustomerController::class, 'downloadInvoice'])->name('customer.orders.invoice.download');
    Route::post('/profile/update', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/password/update', [CustomerController::class, 'updatePassword'])->name('customer.password.update');

    // Orders
    Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/orders/{id}', [CustomerController::class, 'showOrder'])->name('customer.orders.show');
    Route::put('/orders/{id}/cancel', [CustomerController::class, 'cancelOrder'])->name('customer.orders.cancel');
    Route::get('/orders/{id}/download', [CustomerController::class, 'downloadInvoice'])->name('customer.orders.download');

    // Profile
    Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    Route::put('/profile/password', [CustomerController::class, 'updatePassword'])->name('customer.profile.password');

    // Addresses
    Route::get('/addresses', [CustomerController::class, 'addresses'])->name('customer.addresses');
    Route::post('/addresses', [CustomerController::class, 'storeAddress'])->name('customer.addresses.store');
    Route::put('/addresses/{id}', [CustomerController::class, 'updateAddress'])->name('customer.addresses.update');
    Route::delete('/addresses/{id}', [CustomerController::class, 'deleteAddress'])->name('customer.addresses.delete');
});

// API Routes for AJAX
Route::prefix('api')->group(function () {
    Route::get('/medicines/search', [CustomerController::class, 'searchMedicines'])->name('api.medicines.search');
    Route::get('/medicines/{id}/stock', [PharmacistController::class, 'getMedicineStock'])->name('api.medicines.stock');
});