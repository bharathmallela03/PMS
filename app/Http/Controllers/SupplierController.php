<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Medicine;
use App\Models\StockRequest;
use App\Models\Supplier;
use App\Models\Company;
use App\Exports\SupplierInventoryExport;
use App\Imports\MedicinesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Bill;
use App\Models\BillItem;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Middleware to ensure only authenticated suppliers can access these routes.
     */
    public function __construct()
    {
        $this->middleware('auth:supplier');
    }

    /**
     * Display the supplier dashboard with key statistics.
     */
    public function dashboard()
    {
        $supplier = Auth::guard('supplier')->user();

        $data = [
            'total_medicines' => Medicine::where('supplier_id', $supplier->id)->count(),
            'pending_requests' => StockRequest::where('supplier_id', $supplier->id)->where('status', 'pending')->count(),
            'fulfilled_requests' => StockRequest::where('supplier_id', $supplier->id)->where('status', 'fulfilled')->count(),
            'recent_requests' => StockRequest::with(['pharmacist.user', 'medicine'])
                                            ->where('supplier_id', $supplier->id)
                                            ->latest()
                                            ->take(5)
                                            ->get(),
        ];

        return view('supplier.dashboard', $data);
    }

    /**
     * Display a paginated list of medicines managed by the supplier.
     */
    public function medicines(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        $query = Medicine::where('supplier_id', $supplier->id)->with('company');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $medicines = $query->latest()->paginate(15);
        return view('supplier.medicines.index', compact('medicines'));
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function createMedicine()
    {
        $companies = Company::active()->get();
        return view('supplier.medicines.create', compact('companies'));
    }

    /**
     * Store a newly created medicine in storage.
     */
    public function storeMedicine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'company_id' => 'nullable|exists:companies,id',
            'description' => 'nullable|string',
        ]);

        $supplier = Auth::guard('supplier')->user();
        $data = $request->all();
        $data['supplier_id'] = $supplier->id;
        // Suppliers don't manage stock quantity directly, pharmacists do.
        // So we don't set quantity here.

        Medicine::create($data);

        return redirect()->route('supplier.medicines')->with('success', 'Medicine added successfully.');
    }

    /**
     * Show the form for editing the specified medicine.
     */
    public function editMedicine($id)
    {
        $supplier = Auth::guard('supplier')->user();
        $medicine = Medicine::where('supplier_id', $supplier->id)->findOrFail($id);
        $companies = Company::active()->get();
        
        return view('supplier.medicines.edit', compact('medicine', 'companies'));
    }

    /**
     * Update the specified medicine in storage.
     */
    public function updateMedicine(Request $request, $id)
    {
        $supplier = Auth::guard('supplier')->user();
        $medicine = Medicine::where('supplier_id', $supplier->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'company_id' => 'nullable|exists:companies,id',
            'description' => 'nullable|string',
        ]);

        $medicine->update($request->all());

        return redirect()->route('supplier.medicines')->with('success', 'Medicine updated successfully.');
    }

    /**
     * Remove the specified medicine from storage.
     */
    public function deleteMedicine($id)
    {
        $supplier = Auth::guard('supplier')->user();
        $medicine = Medicine::where('supplier_id', $supplier->id)->findOrFail($id);
        $medicine->delete();

        return redirect()->route('supplier.medicines')->with('success', 'Medicine deleted successfully.');
    }

    /**
     * Display a list of stock requests from pharmacists.
     */
    public function stockRequests(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        $query = StockRequest::with(['pharmacist.user', 'medicine'])
                             ->where('supplier_id', $supplier->id);

        if ($request->has('status') && in_array($request->status, ['pending', 'fulfilled', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $stockRequests = $query->latest()->paginate(20);
        return view('supplier.stock-requests.index', compact('stockRequests'));
    }

    /**
     * Fulfill a pending stock request.
     */
    public function fulfillStockRequest(Request $request, $id)
    {
        $supplier = Auth::guard('supplier')->user();
        $stockRequest = StockRequest::where('supplier_id', $supplier->id)
                                    ->where('status', 'pending')
                                    ->findOrFail($id);

        $request->validate([
            'action' => 'required|in:fulfill,reject',
            'supplier_notes' => 'nullable|string|max:500'
        ]);

        if ($request->action === 'fulfill') {
            $stockRequest->update([
                'status' => 'fulfilled',
                'fulfilled_at' => now(),
                'supplier_notes' => $request->supplier_notes,
            ]);

            // Logic to update pharmacist's stock
            $pharmacistMedicine = Medicine::find($stockRequest->medicine_id);
            if ($pharmacistMedicine) {
                $pharmacistMedicine->increment('quantity', $stockRequest->requested_quantity);
            }
            
            // TODO: Optionally send an email notification to the pharmacist.

            return redirect()->route('supplier.stock-requests')->with('success', 'Stock request has been fulfilled.');
        } else {
            $stockRequest->update([
                'status' => 'rejected',
                'supplier_notes' => $request->supplier_notes,
            ]);
            return redirect()->route('supplier.stock-requests')->with('info', 'Stock request has been rejected.');
        }
    }

    /**
     * Show the bulk upload form.
     */
    public function bulkUpload()
    {
        return view('supplier.medicines.bulk-upload');
    }

    /**
     * Process the uploaded file of medicines.
     */
    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'medicines_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MedicinesImport(Auth::guard('supplier')->id()), $request->file('medicines_file'));
            return redirect()->route('supplier.medicines')->with('success', 'Medicines imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             // You can format and pass these failures to the view
             return redirect()->back()->with('import_errors', $failures);
        } catch (\Exception $e) {
            Log::error('Bulk Upload Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred during the import. Please check the file format and data.');
        }
    }

    /**
     * Download the Excel template for bulk uploading medicines.
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/medicines_template.xlsx'));
    }

    /**
     * Display inventory report.
     */
    public function inventoryReport()
    {
        $supplier = Auth::guard('supplier')->user();
        $medicines = Medicine::where('supplier_id', $supplier->id)
                             ->selectRaw('category, count(*) as count')
                             ->groupBy('category')
                             ->get();
        
        return view('supplier.reports.inventory', compact('medicines'));
    }

    /**
     * Export inventory report to Excel.
     */
    public function exportInventoryReport()
    {
        $supplier = Auth::guard('supplier')->user();
        return Excel::download(new SupplierInventoryExport($supplier->id), 'inventory_report.xlsx');
    }
    public function pharmacists()
    {
        // We fetch Users that have a pharmacist profile
        $pharmacists = User::whereHas('pharmacist')->paginate(15);
        return view('supplier.pharmacists.index', compact('pharmacists'));
    }

    /**
     * Display the billing page with a history of bills.
     */
    public function billingIndex()
    {
        $supplier = Auth::guard('supplier')->user();
        $bills = Bill::where('supplier_id', $supplier->id)
                     ->with('pharmacist.user') // Eager load pharmacist user details
                     ->latest()
                     ->paginate(15);

        return view('supplier.billing.index', compact('bills'));
    }

    /**
     * Search for pharmacists by name for autocomplete suggestions.
     */
    public function searchPharmacists(Request $request)
    {
        $query = $request->get('query');
        $pharmacists = User::whereHas('pharmacist')
                           ->where('name', 'LIKE', "%{$query}%")
                           ->select('id', 'name')
                           ->take(10)
                           ->get();
        
        // We need to get the Pharmacist ID, not the User ID for the bill
        $results = [];
        foreach($pharmacists as $user) {
            $results[] = [
                'pharmacist_id' => $user->pharmacist->id, // Get the actual pharmacist ID
                'name' => $user->name,
            ];
        }

        return response()->json($results);
    }
    
    /**
     * Search for medicines for the billing form.
     */
    public function searchMedicines(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        $query = $request->get('query');
        
        $medicines = Medicine::where('supplier_id', $supplier->id)
                               ->where('name', 'LIKE', "%{$query}%")
                               ->select('id', 'name', 'price')
                               ->take(10)
                               ->get();
        
        return response()->json($medicines);
    }

    /**
     * Store a new bill for a pharmacist.
     */
    public function storeBill(Request $request)
    {
        $validatedData = $request->validate([
            'pharmacist_id' => 'required|exists:pharmacists,id',
            'pharmacist_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $supplier = Auth::guard('supplier')->user();
        $subtotal = collect($validatedData['items'])->sum('total');

        // Create the Bill for the pharmacist from the supplier
        $bill = Bill::create([
            'supplier_id' => $supplier->id,
            'pharmacist_id' => $validatedData['pharmacist_id'],
            'bill_number' => 'SUP-BILL-' . time(),
            'patient_name' => $validatedData['pharmacist_name'], // Storing Pharmacist name here
            'subtotal' => $subtotal,
            'total_amount' => $validatedData['total_amount'],
            'status' => 'fulfilled', // A supplier bill is considered fulfilled
        ]);

        // Create bill items and INCREMENT the pharmacist's stock
        foreach ($validatedData['items'] as $itemData) {
            BillItem::create([
                'bill_id' => $bill->id,
                'medicine_id' => $itemData['medicine_id'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'total' => $itemData['total'],
            ]);

            // Find the pharmacist's version of this medicine and increase stock
            $pharmacistMedicine = Medicine::where('id', $itemData['medicine_id'])
                                          ->where('pharmacist_id', $validatedData['pharmacist_id'])
                                          ->first();

            if ($pharmacistMedicine) {
                $pharmacistMedicine->increment('quantity', $itemData['quantity']);
            }
            // Note: This assumes the medicine already exists in the pharmacist's inventory.
            // A more complex system might create it if it doesn't exist.
        }

        return response()->json([
            'success' => true,
            'message' => 'Bill created successfully and pharmacist stock updated!',
            'bill_id' => $bill->id,
        ]);
    }
    
}
