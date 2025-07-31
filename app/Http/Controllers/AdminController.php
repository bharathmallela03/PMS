<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Pharmacist;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Order;
use App\Mail\PasswordSetupMail;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashboard()
    {
        $data = [
            'pharmacists_count' => Pharmacist::count(),
            'suppliers_count' => Supplier::count(),
            'customers_count' => Customer::count(),
            'medicines_count' => Medicine::count(),
            'orders_count' => Order::count(),
            'total_sales' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'recent_orders' => Order::with(['customer', 'pharmacist'])->latest()->take(5)->get(),
            'low_stock_medicines' => Medicine::lowStock()->take(5)->get(),
            'monthly_sales' => $this->getMonthlySales(),
        ];

        return view('admin.dashboard', $data);
    }

    // Pharmacist Management
    public function pharmacists()
    {
        $pharmacists = Pharmacist::latest()->paginate(15);
        return view('admin.pharmacists.index', compact('pharmacists'));
    }
    public function resendPasswordSetupMail($id)
{
    $pharmacist = Pharmacist::findOrFail($id);

    // Only resend if the password has not been set
    if (is_null($pharmacist->password)) {
        $setupToken = Str::random(60);
        $pharmacist->setup_token = $setupToken;
        $pharmacist->save();

        // Send password setup email
        Mail::to($pharmacist->email)->send(new PasswordSetupMail($pharmacist, $setupToken, 'pharmacist'));

        return redirect()->back()->with('success', 'Password setup email has been resent successfully.');
    }

    return redirect()->back()->with('error', 'This pharmacist has already set up their password.');
}
    public function createPharmacist()
    {
        return view('admin.pharmacists.create');
    }

    public function storePharmacist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pharmacists',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
        ]);

        $setupToken = Str::random(60);

        $pharmacist = Pharmacist::create([
            'name' => $request->name,
            'shop_name' => $request->shop_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country,
            'setup_token' => $setupToken,
            'is_active' => true,
        ]);

        // Send password setup email
        Mail::to($pharmacist->email)->send(new PasswordSetupMail($pharmacist, $setupToken, 'pharmacist'));

        return redirect()->route('admin.pharmacists')->with('success', 'Pharmacist created successfully. Password setup email sent.');
    }

    public function editPharmacist($id)
    {
        $pharmacist = Pharmacist::findOrFail($id);
        return view('admin.pharmacists.edit', compact('pharmacist'));
    }

    public function updatePharmacist(Request $request, $id)
    {
        $pharmacist = Pharmacist::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pharmacists,email,' . $id,
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $pharmacist->update($request->all());

        return redirect()->route('admin.pharmacists')->with('success', 'Pharmacist updated successfully.');
    }

    public function deletePharmacist($id)
    {
        $pharmacist = Pharmacist::findOrFail($id);
        $pharmacist->delete();

        return redirect()->route('admin.pharmacists')->with('success', 'Pharmacist deleted successfully.');
    }

    // Supplier Management
    public function suppliers()
    {
        $suppliers = Supplier::latest()->paginate(15);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function createSupplier()
    {
        return view('admin.suppliers.create');
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:suppliers',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
        ]);

        $setupToken = Str::random(60);

        $supplier = Supplier::create([
            'name' => $request->name,
            'shop_name' => $request->shop_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country,
            'setup_token' => $setupToken,
            'is_active' => true,
        ]);

        // Send password setup email
        Mail::to($supplier->email)->send(new PasswordSetupMail($supplier, $setupToken, 'supplier'));

        return redirect()->route('admin.suppliers')->with('success', 'Supplier created successfully. Password setup email sent.');
    }

    public function editSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:suppliers,email,' . $id,
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $supplier->update($request->all());

        return redirect()->route('admin.suppliers')->with('success', 'Supplier updated successfully.');
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('admin.suppliers')->with('success', 'Supplier deleted successfully.');
    }

    // Customer Management
    public function customers()
    {
        $customers = Customer::latest()->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
    }

    // Reports
    public function reports(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $type = $request->get('type', 'sales');

        $data = [];

        switch ($type) {
            case 'sales':
                $data = $this->getSalesReport($dateFrom, $dateTo);
                break;
            case 'inventory':
                $data = $this->getInventoryReport();
                break;
            case 'users':
                $data = $this->getUsersReport();
                break;
        }

        return view('admin.reports.index', compact('data', 'dateFrom', 'dateTo', 'type'));
    }

    public function exportReport(Request $request)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'excel');

        switch ($type) {
            case 'users':
                return Excel::download(new UsersExport(), 'users_report.' . ($format === 'pdf' ? 'pdf' : 'xlsx'));
            // Add more export types as needed
        }

        return back()->with('error', 'Export type not supported.');
    }

    // Helper Methods
    private function getMonthlySales()
    {
        $months = [];
        $sales = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $sales[] = Order::whereMonth('created_at', $date->month)
                           ->whereYear('created_at', $date->year)
                           ->where('payment_status', 'paid')
                           ->sum('total_amount');
        }

        return compact('months', 'sales');
    }

    private function getSalesReport($dateFrom, $dateTo)
    {
        return [
            'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'total_sales' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                                 ->where('payment_status', 'paid')->sum('total_amount'),
            'orders_by_status' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                                      ->selectRaw('status, count(*) as count')
                                      ->groupBy('status')->get(),
            'top_medicines' => Medicine::select('medicines.*')
                                     ->join('order_items', 'medicines.id', '=', 'order_items.medicine_id')
                                     ->join('orders', 'order_items.order_id', '=', 'orders.id')
                                     ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
                                     ->selectRaw('sum(order_items.quantity) as total_sold')
                                     ->groupBy('medicines.id')
                                     ->orderBy('total_sold', 'desc')
                                     ->take(10)->get(),
        ];
    }

    private function getInventoryReport()
    {
        return [
            'total_medicines' => Medicine::count(),
            'low_stock_count' => Medicine::lowStock()->count(),
            'expired_count' => Medicine::where('expiry_date', '<', now())->count(),
            'expiring_soon_count' => Medicine::expiring(30)->count(),
            'total_value' => Medicine::selectRaw('sum(quantity * cost_price) as value')->first()->value ?? 0,
        ];
    }

    private function getUsersReport()
    {
        return [
            'pharmacists' => Pharmacist::count(),
            'suppliers' => Supplier::count(),
            'customers' => Customer::count(),
            'active_pharmacists' => Pharmacist::where('is_active', true)->count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'active_customers' => Customer::where('is_active', true)->count(),
        ];
    }
}