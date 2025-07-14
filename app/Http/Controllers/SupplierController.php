<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Medicine;
use App\Models\Company;
use App\Models\StockRequest;
use App\Models\Pharmacist;
use App\Imports\MedicineImport;
use App\Exports\MedicineExport;
use App\Mail\RestockNotificationMail;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:supplier');
    }

    public function dashboard()
    {
        $supplier = Auth::guard('supplier')->user();
        
        $data = [
            'total_medicines' => Medicine::where('supplier_id', $supplier->id)->count(),
            'active_medicines' => Medicine::where('supplier_id', $supplier->id)->where('is_active', true)->count(),
            'pending_requests' => StockRequest::where('supplier_id', $supplier->id)->where('status', 'pending')->count(),
            'fulfilled_requests' => StockRequest::where('supplier_id', $supplier->id)->where('status', 'fulfilled')->count(),
            'total_stock_value' => Medicine::where('supplier_id', $supplier->id)->selectRaw('sum(quantity * cost_price) as value')->first()->value ?? 0,
            'low_stock_count' => Medicine::where('supplier_id', $supplier->id)->lowStock()->count(),
            'recent_requests' => StockRequest::with(['pharmacist', 'medicine'])
                                             ->where('supplier_id', $supplier->id)
                                             ->latest()->take(5)->get(),
            'inventory_by_category' => $this->getInventoryByCategory($supplier->id),
        ];

        return view('supplier.dashboard', $data);
    }

    // Medicine Management
    public function medicines(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        $query = Medicine::where('supplier_id', $supplier->id)->with('company');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('status')) {
            if ($request->status === 'low_stock') {
                $query->lowStock();
            } elseif ($request->status === 'out_of_stock') {
                $query->where('quantity', 0);
            } elseif ($request->status === 'expired') {
                $query->where('expiry_date', '<', now());
            }
        }

        $medicines = $query->latest()->paginate(15);
        $companies = Company::active()->get();
        $categories = Medicine::distinct()->pluck('category')->filter();

        return view('supplier.medicines.index', compact('medicines', 'companies', 'categories'));
    }

    public function createMedicine()
    {
        $companies = Company::active()->get();
        return view('supplier.medicines.create', compact('companies'));
    }

    public function storeMedicine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'expiry_date' => 'required|date|after:today',
            'batch_number' => 'required|string|max:100',
            'company_id' => 'required|exists:companies,id',
            'minimum_stock' => 'nullable|integer|min:1',
        ]);

        $supplier = Auth::guard('supplier')->user();
        $data = $request->all();
        $data['supplier_id'] = $supplier->id;
        $data['is_active'] = true;

        Medicine::create($data);

        return redirect()->route('supplier.medicines')->with('success', 'Medicine added successfully.');
    }

    public function editMedicine($id)
    {
        $supplier = Auth::guard('supplier')->user();
        $medicine = Medicine::where('supplier_id', $supplier->id)->findOrFail($id);
        $companies = Company::active()->get();
        
        return view('supplier.medicines.edit', compact('medicine', 'companies'));
    }

    public function updateMedicine(Request $request, $id)
    {
        $supplier = Auth::guard('supplier')->user();
        $medicine = Medicine::where('supplier_id', $supplier->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'expiry_date' => 'required|date|after:today',
            'batch_number' => 'required|string|max:100',
            'company_id' => 'required|exists:companies,id',
            'minimum_stock' => 'nullable|integer|min:1',
        ]);

        $medicine->update($request->all());

        return redirect()->route('supplier.medicines')->with('success', 'Medicine updated successfully.');
    }

    public function deleteMedicine($id)
    {
        $supplier = Auth::guard('supplier')->user();
        $medicine = Medicine::where('supplier_id', $supplier->id)->findOrFail($id);
        $medicine->delete();

        return redirect()->route('supplier.medicines')->with('success', 'Medicine deleted successfully.');
    }

    // Bulk Upload
    public function bulkUpload()
    {
        $companies = Company::active()->get();
        return view('supplier.medicines.bulk-upload', compact('companies'));
    }

    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $supplier = Auth::guard('supplier')->user();
            
            Excel::import(new MedicineImport($supplier->id), $request->file('file'));

            return redirect()->route('supplier.medicines')->with('success', 'Medicines imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error importing file: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'name',
            'brand',
            'generic_name',
            'category',
            'description',
            'quantity',
            'price',
            'cost_price',
            'expiry_date',
            'batch_number',
            'company_name',
            'minimum_stock'
        ];

        $sampleData = [
            [
                'Paracetamol 500mg',
                'Crocin',
                'Acetaminophen',
                'Analgesic',
                'Pain relief and fever reducer',
                '100',
                '2.50',
                '2.00',
                '2025-12-31',
                'BATCH001',
                'GSK',
                '50'
            ]
        ];

        $data = array_merge([$headers], $sampleData);

        return response()->streamDownload(function() use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'medicine_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    // Company Management
    public function companies()
    {
        $companies = Company::latest()->paginate(15);
        return view('supplier.companies.index', compact('companies'));
    }

    public function storeCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:companies',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
        ]);

        Company::create($request->all() + ['is_active' => true]);

        return response()->json(['success' => true, 'message' => 'Company added successfully.']);
    }

    // Stock Requests
    public function stockRequests(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        $query = StockRequest::with(['pharmacist', 'medicine'])
                           ->where('supplier_id', $supplier->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $stockRequests = $query->latest()->paginate(15);

        return view('supplier.stock-requests', compact('stockRequests'));
    }

    public function fulfillStockRequest(Request $request, $id)
    {
        $request->validate([
            'fulfilled_quantity' => 'required|integer|min:1',
            'supplier_notes' => 'nullable|string|max:500',
        ]);

        $supplier = Auth::guard('supplier')->user();
        $stockRequest = StockRequest::where('supplier_id', $supplier->id)->findOrFail($id);

        $stockRequest->update([
            'fulfilled_quantity' => $request->fulfilled_quantity,
            'supplier_notes' => $request->supplier_notes,
            'status' => StockRequest::STATUS_FULFILLED,
            'fulfilled_at' => now(),
        ]);

        // Update medicine stock if medicine belongs to supplier
        $medicine = Medicine::where('supplier_id', $supplier->id)
                          ->where('id', $stockRequest->medicine_id)
                          ->first();
        
        if ($medicine) {
            $medicine->increment('quantity', $request->fulfilled_quantity);
        }

        // Send notification email to pharmacist
        Mail::to($stockRequest->pharmacist->email)
            ->send(new RestockNotificationMail($stockRequest, $medicine));

        return response()->json(['success' => true, 'message' => 'Stock request fulfilled successfully.']);
    }

    // Reports
    public function inventoryReport(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        
        $data = [
            'total_medicines' => Medicine::where('supplier_id', $supplier->id)->count(),
            'active_medicines' => Medicine::where('supplier_id', $supplier->id)->where('is_active', true)->count(),
            'low_stock_medicines' => Medicine::where('supplier_id', $supplier->id)->lowStock()->get(),
            'expired_medicines' => Medicine::where('supplier_id', $supplier->id)->where('expiry_date', '<', now())->get(),
            'expiring_medicines' => Medicine::where('supplier_id', $supplier->id)->expiring(30)->get(),
            'total_stock_value' => Medicine::where('supplier_id', $supplier->id)->selectRaw('sum(quantity * cost_price) as value')->first()->value ?? 0,
            'categories' => Medicine::where('supplier_id', $supplier->id)->selectRaw('category, count(*) as count, sum(quantity) as total_quantity, sum(quantity * cost_price) as total_value')->groupBy('category')->get(),
        ];

        return view('supplier.reports.inventory', compact('data'));
    }

    public function exportInventoryReport(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        return Excel::download(new MedicineExport($supplier->id), 'inventory_report.xlsx');
    }

    // Helper Methods
    private function getInventoryByCategory($supplierId)
    {
        return Medicine::where('supplier_id', $supplierId)
                      ->selectRaw('category, count(*) as count, sum(quantity) as total_quantity')
                      ->groupBy('category')
                      ->orderBy('count', 'desc')
                      ->take(5)
                      ->get();
    }
}