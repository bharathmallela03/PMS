<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Medicine;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\StockRequest;
use App\Models\Supplier;
use App\Models\Bill;
use App\Models\BillItem;
use App\Mail\StockAlertMail;
use App\Exports\MedicineExport;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;


class PharmacistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:pharmacist');
    }

    public function dashboard()
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        
        $data = [
            'total_medicines' => Medicine::where('pharmacist_id', $pharmacist->id)->count(),
            'low_stock_count' => Medicine::where('pharmacist_id', $pharmacist->id)->lowStock()->count(),
            'total_orders' => Order::where('pharmacist_id', $pharmacist->id)->count(),
            'pending_orders' => Order::where('pharmacist_id', $pharmacist->id)->where('status', 'pending')->count(),
            'today_sales' => Order::where('pharmacist_id', $pharmacist->id)
                                  ->whereDate('created_at', today())
                                  ->where('payment_status', 'paid')
                                  ->sum('total_amount'),
            'monthly_sales' => Order::where('pharmacist_id', $pharmacist->id)
                                   ->whereMonth('created_at', now()->month)
                                   ->where('payment_status', 'paid')
                                   ->sum('total_amount'),
            'recent_orders' => Order::with('customer')
                                   ->where('pharmacist_id', $pharmacist->id)
                                   ->latest()->take(5)->get(),
            'low_stock_medicines' => Medicine::where('pharmacist_id', $pharmacist->id)
                                            ->lowStock()->take(5)->get(),
            'sales_chart' => $this->getSalesChart($pharmacist->id),
        ];

        return view('pharmacist.dashboard', $data);
    }

    // Medicine Management
    public function medicines(Request $request)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $query = Medicine::where('pharmacist_id', $pharmacist->id)->with('company');

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

        return view('pharmacist.medicines.index', compact('medicines', 'companies', 'categories'));
    }

    public function createMedicine()
    {
        $companies = Company::active()->get();
        return view('pharmacist.medicines.create', compact('companies'));
    }
    public function searchMedicines(Request $request)
{
    $query = $request->get('query');
    
    if (strlen($query) >= 2) {
        $medicines = Medicine::where('name', 'LIKE', "%{$query}%")
                            ->where('is_active', true)
                            ->where('quantity', '>', 0)
                            ->select('id', 'name', 'price', 'quantity')
                            ->limit(10)
                            ->get();
        
        return response()->json($medicines);
    }
    
    return response()->json([]);
}

//     public function billingIndex()
// {
//     $pharmacist = Auth::guard('pharmacist')->user();
    
//     // Fetch bills for the current pharmacist
//     $bills = Bill::where('pharmacist_id', $pharmacist->id)
//                  ->latest()
//                  ->paginate(15); 
    
//     return view('pharmacist.billing.index', compact('bills'));
// }

public function billingIndex()
{
    $pharmacist = Auth::guard('pharmacist')->user();
    
    // 1. Fetch the list of bills to display in the table
    $bills = Bill::where('pharmacist_id', $pharmacist->id)
                 ->latest()
                 ->paginate(15); 
    
    // 2. Fetch active medicines for the "New Bill" modal
    // Note: This data is now available if your modal needs it on page load.
    // However, your JS fetches this dynamically, so this line is optional
    // but good practice to have.
    $medicines = Medicine::where('pharmacist_id', $pharmacist->id)
                       ->where('quantity', '>', 0)
                       ->where('is_active', true)
                       ->get();
    
    // 3. Return the view with BOTH variables
    return view('pharmacist.billing.index', compact('bills', 'medicines'));
}

public function storeBilling(Request $request) 
{
    try {
        $validatedData = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'nullable|string|max:20',
            'patient_address' => 'nullable|string',
            'items' => 'required|array',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.total' => 'required|numeric',
            'discount_percentage' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0'
        ]);

        $pharmacist = Auth::guard('pharmacist')->user();
        
        $subtotal = collect($validatedData['items'])->sum('total');
        $discountAmount = ($subtotal * ($validatedData['discount_percentage'] ?? 0)) / 100;

        // Create the Bill
        $bill = Bill::create([
            'pharmacist_id' => $pharmacist->id,
            'bill_number' => 'BILL-' . time(),
            'patient_name' => $validatedData['patient_name'],
            'patient_phone' => $validatedData['patient_phone'],
            'patient_address' => $validatedData['patient_address'],
            'subtotal' => $subtotal,
            'discount_percentage' => $validatedData['discount_percentage'] ?? 0,
            'discount_amount' => $discountAmount,
            'total_amount' => $validatedData['total_amount'],
            'status' => 'paid', // Default to 'paid' for in-person billing
        ]);

        // Create bill items and update medicine stock
        foreach ($validatedData['items'] as $itemData) {
            $medicine = Medicine::find($itemData['medicine_id']);
            $quantity = $itemData['quantity'];

            if ($medicine->quantity < $quantity) {
                $bill->delete(); // Rollback bill creation
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$medicine->name}."
                ], 400);
            }

            BillItem::create([
                'bill_id' => $bill->id,
                'medicine_id' => $itemData['medicine_id'],
                'quantity' => $quantity,
                'price' => $itemData['price'],
                'total' => $itemData['total'],
            ]);

            $medicine->decrement('quantity', $quantity);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bill created successfully!',
            'bill_id' => $bill->id
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating bill: ' . $e->getMessage()
        ], 500);
    }
}

//  public function billingIndex()
// {
//     $pharmacist = Auth::guard('pharmacist')->user();
//     $bills = collect([]); // Replace with actual bills query
    
//     return view('pharmacist.billing', compact('bills'));
// }

//     public function storeBilling(Request $request)
// {
//     try {
//         $validatedData = $request->validate([
//             'patient_name' => 'required|string|max:255',
//             'patient_phone' => 'nullable|string|max:20',
//             'patient_age' => 'nullable|integer|min:1|max:120',
//             'medicine_id' => 'required|array',
//             'medicine_id.*' => 'required|exists:medicines,id',
//             'quantity' => 'required|array',
//             'quantity.*' => 'required|integer|min:1',
//             'price' => 'required|array',
//             'total' => 'required|array',
//             'discount' => 'nullable|numeric|min:0',
//             'tax' => 'nullable|numeric|min:0',
//             'grand_total' => 'required|numeric|min:0'
//         ]);

//         $pharmacist = Auth::guard('pharmacist')->user();
        
//         $customer = Customer::firstOrCreate(
//             ['contact_number' => $validatedData['patient_phone']],
//             ['name' => $validatedData['patient_name']]
//         );

//         $order = Order::create([
//             'customer_id' => $customer->id,
//             'pharmacist_id' => $pharmacist->id,
//             'order_number' => 'BILL-' . time(),
//             'status' => 'delivered', // Corrected from 'completed'
//             'payment_status' => 'paid',
//             'payment_method' => 'cash',
//             'subtotal' => array_sum($validatedData['total']),
//             'discount_amount' => (array_sum($validatedData['total']) * ($validatedData['discount'] ?? 0)) / 100,
//             'tax_amount' => ((array_sum($validatedData['total']) - (array_sum($validatedData['total']) * ($validatedData['discount'] ?? 0)) / 100) * ($validatedData['tax'] ?? 0)) / 100,
//             'total_amount' => $validatedData['grand_total'],
//         ]);

//         foreach ($validatedData['medicine_id'] as $key => $medicineId) {
//             $medicine = Medicine::find($medicineId);
//             $quantity = $validatedData['quantity'][$key];

//             if ($medicine->quantity < $quantity) {
//                 $order->delete(); // Rollback order
//                 return response()->json([
//                     'success' => false,
//                     'message' => "Insufficient stock for {$medicine->name}. Only {$medicine->quantity} available."
//                 ], 400);
//             }

//             OrderItem::create([
//                 'order_id' => $order->id,
//                 'medicine_id' => $medicineId,
//                 'quantity' => $quantity,
//                 'price' => $validatedData['price'][$key],
//                 'total' => $validatedData['total'][$key],
//             ]);

//             $medicine->decrement('quantity', $quantity);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Bill created successfully!',
//             'order_id' => $order->id
//         ]);

//     } catch (\Illuminate\Validation\ValidationException $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Validation failed',
//             'errors' => $e->errors()
//         ], 422);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error creating bill: ' . $e->getMessage()
//         ], 500);
//     }
// }

// public function storeBilling(Request $request) 
// {
//     try {
//         $request->validate([
//             'patient_name' => 'required|string|max:255',
//             'patient_phone' => 'nullable|string|max:20',
//             'patient_age' => 'nullable|integer|min:1|max:120',
//             'patient_address' => 'nullable|string',
//             'medicine_name' => 'required|array',
//             'medicine_name.*' => 'required|string',
//             'medicine_id' => 'required|array',
//             'medicine_id.*' => 'required|exists:medicines,id',
//             'quantity' => 'required|array',
//             'quantity.*' => 'required|integer|min:1',
//             'price' => 'required|array',
//             'price.*' => 'required|numeric|min:0',
//             'total' => 'required|array',
//             'total.*' => 'required|numeric|min:0',
//             'discount' => 'nullable|numeric|min:0|max:100',
//             'tax' => 'nullable|numeric|min:0',
//             'grand_total' => 'required|numeric|min:0'
//         ]);

//         $pharmacist = Auth::guard('pharmacist')->user();
        
//         // Calculate subtotal from items
//         $subtotal = array_sum($request->total);
//         $discountAmount = ($subtotal * ($request->discount ?? 0)) / 100;
//         $taxableAmount = $subtotal - $discountAmount;
//         $taxAmount = ($taxableAmount * ($request->tax ?? 0)) / 100;
        
//         // Create bill data
//         $billData = [
//             'bill_number' => 'BILL-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
//             'pharmacist_id' => $pharmacist->id,
//             'patient_name' => $request->patient_name,
//             'patient_phone' => $request->patient_phone,
//             'patient_age' => $request->patient_age,
//             'patient_address' => $request->patient_address,
//             'subtotal' => $subtotal,
//             'discount_percent' => $request->discount ?? 0,
//             'discount_amount' => $discountAmount,
//             'tax_percent' => $request->tax ?? 0,
//             'tax_amount' => $taxAmount,
//             'total_amount' => $request->grand_total,
//             'status' => 'completed',
//             'created_at' => now(),
//             'updated_at' => now()
//         ];

//         // For now, we'll create a temporary bill object
//         // TODO: Replace with actual Bill model when created
//         $bill = (object) $billData;
//         $bill->id = rand(1000, 9999);

//         // Process each medicine item
//         $billItems = [];
//         for ($i = 0; $i < count($request->medicine_id); $i++) {
//             $medicine = Medicine::find($request->medicine_id[$i]);
            
//             // Check if enough stock is available
//             if ($medicine->quantity < $request->quantity[$i]) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => "Insufficient stock for {$medicine->name}. Available: {$medicine->quantity}, Requested: {$request->quantity[$i]}"
//                 ], 400);
//             }

//             // Update medicine stock
//             $medicine->decrement('quantity', $request->quantity[$i]);

//             // Prepare bill item data
//             $billItems[] = [
//                 'medicine_id' => $request->medicine_id[$i],
//                 'medicine_name' => $request->medicine_name[$i],
//                 'quantity' => $request->quantity[$i],
//                 'unit_price' => $request->price[$i],
//                 'total_price' => $request->total[$i]
//             ];
//         }

//         // TODO: Save bill and bill items to database when models are created
//         /*
//         $bill = Bill::create($billData);
        
//         foreach ($billItems as $item) {
//             BillItem::create([
//                 'bill_id' => $bill->id,
//                 'medicine_id' => $item['medicine_id'],
//                 'quantity' => $item['quantity'],
//                 'unit_price' => $item['unit_price'],
//                 'total_price' => $item['total_price']
//             ]);
//         }
//         */

//         return response()->json([
//             'success' => true,
//             'message' => 'Bill created successfully!',
//             'bill_id' => $bill->id,
//             'bill_number' => $billData['bill_number']
//         ]);

//     } catch (\Illuminate\Validation\ValidationException $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Validation failed',
//             'errors' => $e->errors()
//         ], 422);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error creating bill: ' . $e->getMessage()
//         ], 500);
//     }
// }

public function storeMedicine(Request $request)
{
    try {
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'minimum_stock' => 'nullable|integer|min:1',
        ]);

        $pharmacist = Auth::guard('pharmacist')->user();
        $data = $request->all();
        $data['pharmacist_id'] = $pharmacist->id;
        $data['is_active'] = true;

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Create directory if it doesn't exist
            $directory = storage_path('app/public/medicines');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            
            $imagePath = $directory . '/' . $imageName;

            // Resize and save image using Intervention Image
            Image::make($image->getPathname())->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($imagePath);

            $data['photo'] = $imageName;
        }

        Medicine::create($data);

        return redirect()->route('pharmacist.medicines')->with('success', 'Medicine added successfully.');
        
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error adding medicine: ' . $e->getMessage());
    }
}
    public function editMedicine($id)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $medicine = Medicine::where('pharmacist_id', $pharmacist->id)->findOrFail($id);
        $companies = Company::active()->get();
        
        return view('pharmacist.medicines.edit', compact('medicine', 'companies'));
    }

    public function updateMedicine(Request $request, $id)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $medicine = Medicine::where('pharmacist_id', $pharmacist->id)->findOrFail($id);

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'minimum_stock' => 'nullable|integer|min:1',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($medicine->photo) {
                Storage::disk('public')->delete('medicines/' . $medicine->photo);
            }

            $image = $request->file('photo');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = storage_path('app/public/medicines/') . $imageName;

            Image::make($image)->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($imagePath);

            $data['photo'] = $imageName;
        }

        $medicine->update($data);

        return redirect()->route('pharmacist.medicines')->with('success', 'Medicine updated successfully.');
    }

    public function deleteMedicine($id)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $medicine = Medicine::where('pharmacist_id', $pharmacist->id)->findOrFail($id);

        if ($medicine->photo) {
            Storage::disk('public')->delete('medicines/' . $medicine->photo);
        }

        $medicine->delete();

        return redirect()->route('pharmacist.medicines')->with('success', 'Medicine deleted successfully.');
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
        ]);

        $pharmacist = Auth::guard('pharmacist')->user();
        $medicine = Medicine::where('pharmacist_id', $pharmacist->id)->findOrFail($id);

        $currentQuantity = $medicine->quantity;
        $changeQuantity = $request->quantity;

        if ($request->action === 'add') {
            $newQuantity = $currentQuantity + $changeQuantity;
        } else {
            $newQuantity = max(0, $currentQuantity - $changeQuantity);
        }

        $medicine->update(['quantity' => $newQuantity]);

        return response()->json([
            'success' => true,
            'new_quantity' => $newQuantity,
            'message' => 'Stock updated successfully.'
        ]);
    }

    public function exportMedicines(Request $request)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        return Excel::download(new MedicineExport($pharmacist->id), 'medicines.xlsx');
    }

    // Company Management
    public function companies()
    {
        $companies = Company::latest()->paginate(15);
        return view('pharmacist.companies.index', compact('companies'));
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

    // Billing
    // public function billing()
    // {
    //     $pharmacist = Auth::guard('pharmacist')->user();
    //     $medicines = Medicine::where('pharmacist_id', $pharmacist->id)
    //                        ->where('quantity', '>', 0)
    //                        ->where('is_active', true)
    //                        ->get();
        
    //     return view('pharmacist.billing.index', compact('medicines'));
    // }

    public function generateInvoice(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,upi,bank_transfer',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $pharmacist = Auth::guard('pharmacist')->user();

        // Find or create customer
        $customer = Customer::where('contact_number', $request->customer_phone)->first();
        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->customer_name,
                'contact_number' => $request->customer_phone,
                'address' => $request->customer_address,
                'email' => null,
                'password' => null,
                'is_active' => true,
            ]);
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }

        $discountAmount = $request->discount_amount ?? 0;
        $taxRate = $request->tax_rate ?? 0;
        $taxAmount = ($subtotal - $discountAmount) * ($taxRate / 100);
        $totalAmount = $subtotal - $discountAmount + $taxAmount;

        // Create order
        $order = Order::create([
            'customer_id' => $customer->id,
            'pharmacist_id' => $pharmacist->id,
            'status' => Order::STATUS_COMPLETED,
            'payment_status' => Order::PAYMENT_PAID,
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'notes' => $request->notes,
        ]);

        // Create order items and update stock
        foreach ($request->items as $item) {
            $medicine = Medicine::find($item['medicine_id']);
            
            OrderItem::create([
                'order_id' => $order->id,
                'medicine_id' => $medicine->id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);

            // Update medicine stock
            $medicine->decrement('quantity', $item['quantity']);

            // Check for low stock alert
            if ($medicine->isLowStock()) {
                $this->sendStockAlert($medicine);
            }
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'redirect_url' => route('pharmacist.billing.invoice', $order->id)
        ]);
    }

    public function showInvoice($id)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $order = Order::with(['customer', 'items.medicine', 'pharmacist'])
                     ->where('pharmacist_id', $pharmacist->id)
                     ->findOrFail($id);

        return view('pharmacist.billing.invoice', compact('order'));
    }
    // In app/Http/Controllers/PharmacistController.php

    public function showOrder($id)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $order = Order::with(['customer', 'items.medicine'])
                    ->where('pharmacist_id', $pharmacist->id)
                    ->findOrFail($id);

        return view('pharmacist.orders.show', compact('order'));
    }
    public function downloadInvoice($id)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $order = Order::with(['customer', 'items.medicine', 'pharmacist'])
                     ->where('pharmacist_id', $pharmacist->id)
                     ->findOrFail($id);

        $pdf = Pdf::loadView('pharmacist.billing.invoice-pdf', compact('order'));
        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    // Orders
    // public function orders(Request $request)
    // {
    //     $pharmacist = Auth::guard('pharmacist')->user();
    //     $query = Order::with(['customer', 'items'])
    //                  ->where('pharmacist_id', $pharmacist->id);

    //     if ($request->has('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->has('payment_status')) {
    //         $query->where('payment_status', $request->payment_status);
    //     }

    //     $orders = $query->latest()->paginate(15);

    //     return view('pharmacist.orders.index', compact('orders'));
    // }
    public function orders(Request $request)
{
    $pharmacist = Auth::guard('pharmacist')->user();

    // Base query for calculating stats
    $statsQuery = Order::where('pharmacist_id', $pharmacist->id);

    // Calculate stats before pagination
    $totalOrders = (clone $statsQuery)->count();
    $deliveredOrders = (clone $statsQuery)->where('status', 'delivered')->count();
    $pendingOrders = (clone $statsQuery)->where('status', 'pending')->count();
    $todayOrders = (clone $statsQuery)->whereDate('created_at', today())->count();

    // Main query for fetching the list of orders
    $ordersQuery = Order::with(['customer', 'items'])
                     ->where('pharmacist_id', $pharmacist->id);

    if ($request->has('status')) {
        $ordersQuery->where('status', $request->status);
    }

    $orders = $ordersQuery->latest()->paginate(15);

    // Pass all the data to the view
    return view('pharmacist.orders.index', compact(
        'orders',
        'totalOrders',
        'deliveredOrders',
        'pendingOrders',
        'todayOrders'
    ));
}
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $pharmacist = Auth::guard('pharmacist')->user();
        $order = Order::where('pharmacist_id', $pharmacist->id)->findOrFail($id);

        $order->update(['status' => $request->status]);

        if ($request->status === 'shipped') {
            $order->update(['shipped_at' => now()]);
        } elseif ($request->status === 'delivered') {
            $order->update(['delivered_at' => now()]);
        } elseif ($request->status === 'cancelled') {
            $order->update(['cancelled_at' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Order status updated successfully.']);
    }

    // Reports
    public function salesReport(Request $request)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $period = $request->get('period', 'monthly');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $data = $this->generateSalesReport($pharmacist->id, $dateFrom, $dateTo, $period);

        return view('pharmacist.reports.sales', compact('data', 'period', 'dateFrom', 'dateTo'));
    }

    public function exportSalesReport(Request $request)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $period = $request->get('period', 'monthly');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        return Excel::download(new SalesExport($pharmacist->id, $dateFrom, $dateTo), 'sales_report.xlsx');
    }

    public function inventoryReport()
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        
        $data = [
            'total_medicines' => Medicine::where('pharmacist_id', $pharmacist->id)->count(),
            'low_stock_medicines' => Medicine::where('pharmacist_id', $pharmacist->id)->lowStock()->get(),
            'expired_medicines' => Medicine::where('pharmacist_id', $pharmacist->id)->where('expiry_date', '<', now())->get(),
            'expiring_medicines' => Medicine::where('pharmacist_id', $pharmacist->id)->expiring(30)->get(),
            'total_stock_value' => Medicine::where('pharmacist_id', $pharmacist->id)->selectRaw('sum(quantity * cost_price) as value')->first()->value ?? 0,
            'categories' => Medicine::where('pharmacist_id', $pharmacist->id)->selectRaw('category, count(*) as count, sum(quantity) as total_quantity')->groupBy('category')->get(),
        ];

        return view('pharmacist.reports.inventory', compact('data'));
    }

    // Stock Alerts
    public function stockAlerts()
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $lowStockMedicines = Medicine::where('pharmacist_id', $pharmacist->id)->lowStock()->get();
        $stockRequests = StockRequest::where('pharmacist_id', $pharmacist->id)->with(['medicine', 'supplier'])->latest()->get();

        // This line is required to fetch suppliers for the modal dropdown
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        // Note that 'suppliers' is now included in compact()
        return view('pharmacist.stock-alerts', compact('lowStockMedicines', 'stockRequests', 'suppliers'));
    }

    public function requestRestock(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $pharmacist = Auth::guard('pharmacist')->user();

        StockRequest::create([
            'pharmacist_id' => $pharmacist->id,
            'supplier_id' => $request->supplier_id,
            'medicine_id' => $request->medicine_id,
            'requested_quantity' => $request->quantity,
            'status' => StockRequest::STATUS_PENDING,
            'pharmacist_notes' => $request->notes,
        ]);

        // Send email to supplier
        $supplier = Supplier::find($request->supplier_id);
        $medicine = Medicine::find($request->medicine_id);
        
        Mail::to($supplier->email)->send(new StockAlertMail($medicine, $pharmacist, $supplier, $request->quantity));

        return response()->json(['success' => true, 'message' => 'Restock request sent successfully.']);
    }

    public function getMedicineStock($id)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $medicine = Medicine::where('pharmacist_id', $pharmacist->id)->findOrFail($id);

        return response()->json([
            'quantity' => $medicine->quantity,
            'price' => $medicine->price,
            'is_available' => $medicine->quantity > 0,
        ]);
    }

    // Helper Methods
    private function getSalesChart($pharmacistId)
    {
        $days = [];
        $sales = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');
            $sales[] = Order::where('pharmacist_id', $pharmacistId)
                           ->whereDate('created_at', $date)
                           ->where('payment_status', 'paid')
                           ->sum('total_amount');
        }

        return compact('days', 'sales');
    }

    private function generateSalesReport($pharmacistId, $dateFrom, $dateTo, $period)
    {
        $orders = Order::where('pharmacist_id', $pharmacistId)
                      ->whereBetween('created_at', [$dateFrom, $dateTo])
                      ->where('payment_status', 'paid');

        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $topMedicines = Medicine::select('medicines.*')
                              ->join('order_items', 'medicines.id', '=', 'order_items.medicine_id')
                              ->join('orders', 'order_items.order_id', '=', 'orders.id')
                              ->where('orders.pharmacist_id', $pharmacistId)
                              ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
                              ->where('orders.payment_status', 'paid')
                              ->selectRaw('medicines.*, sum(order_items.quantity) as total_sold, sum(order_items.total) as total_revenue')
                              ->groupBy('medicines.id')
                              ->orderBy('total_sold', 'desc')
                              ->take(10)->get();

        $salesByPeriod = $this->getSalesByPeriod($pharmacistId, $dateFrom, $dateTo, $period);

        return compact('totalSales', 'totalOrders', 'averageOrderValue', 'topMedicines', 'salesByPeriod');
    }

    private function getSalesByPeriod($pharmacistId, $dateFrom, $dateTo, $period)
    {
        $query = Order::where('pharmacist_id', $pharmacistId)
                     ->whereBetween('created_at', [$dateFrom, $dateTo])
                     ->where('payment_status', 'paid');

        switch ($period) {
            case 'daily':
                return $query->selectRaw('DATE(created_at) as period, sum(total_amount) as total')
                           ->groupBy('period')
                           ->orderBy('period')
                           ->get();
            case 'weekly':
                return $query->selectRaw('YEARWEEK(created_at) as period, sum(total_amount) as total')
                           ->groupBy('period')
                           ->orderBy('period')
                           ->get();
            case 'monthly':
                return $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, sum(total_amount) as total')
                           ->groupBy('period')
                           ->orderBy('period')
                           ->get();
            case 'yearly':
                return $query->selectRaw('YEAR(created_at) as period, sum(total_amount) as total')
                           ->groupBy('period')
                           ->orderBy('period')
                           ->get();
            default:
                return collect();
        }
    }

    private function sendStockAlert($medicine)
    {
        // Find suppliers who have this medicine
        $suppliers = Supplier::whereHas('medicines', function($query) use ($medicine) {
            $query->where('name', $medicine->name)->orWhere('brand', $medicine->brand);
        })->get();

        if ($suppliers->isEmpty()) {
            // If no specific suppliers found, get all active suppliers
            $suppliers = Supplier::where('is_active', true)->take(3)->get();
        }

        foreach ($suppliers as $supplier) {
            Mail::to($supplier->email)->send(new StockAlertMail($medicine, $medicine->pharmacist, $supplier));
        }
    }

    // /**
    //  * Display the stock alerts page with dynamic data.
    //  */
    // public function stockAlerts(Request $request)
    // {
    //     $pharmacist = Auth::guard('pharmacist')->user();
    //     $query = Medicine::where('pharmacist_id', $pharmacist->id);

    //     // Fetch all medicines with a calculated alert level
    //     $medicines = (clone $query)->get()->map(function ($medicine) {
    //         if ($medicine->quantity == 0) {
    //             $medicine->alert_level = 'out';
    //         } elseif ($medicine->quantity <= ($medicine->minimum_stock * 0.5) && $medicine->minimum_stock > 0) {
    //             $medicine->alert_level = 'critical';
    //         } elseif ($medicine->isLowStock()) { // Assumes isLowStock() is defined in Medicine model
    //             $medicine->alert_level = 'low';
    //         } else {
    //             $medicine->alert_level = 'ok';
    //         }
    //         return $medicine;
    //     })->filter(function ($medicine) {
    //         return in_array($medicine->alert_level, ['critical', 'low', 'out']);
    //     });

    //     // Data for Summary Cards
    //     $criticalAlertsCount = $medicines->where('alert_level', 'critical')->count();
    //     $lowStockCount = $medicines->where('alert_level', 'low')->count();
    //     $outOfStockCount = $medicines->where('alert_level', 'out')->count();
    //     $wellStockedCount = (clone $query)->whereColumn('quantity', '>', 'minimum_stock')->count();

    //     // Data for Filters and Modals
    //     $categories = (clone $query)->distinct()->pluck('category')->filter();
    //     $suppliers = Supplier::where('is_active', true)->get();

    //     return view('pharmacist.stock-alerts', [
    //         'medicines' => $medicines,
    //         'criticalAlertsCount' => $criticalAlertsCount,
    //         'lowStockCount' => $lowStockCount,
    //         'outOfStockCount' => $outOfStockCount,
    //         'wellStockedCount' => $wellStockedCount,
    //         'categories' => $categories,
    //         'suppliers' => $suppliers
    //     ]);
    // }

    // /**
    //  * Handle a single or bulk restock request.
    //  */
    // public function requestRestock(Request $request)
    // {
    //     $request->validate([
    //         'items' => 'required|array',
    //         'items.*.medicine_id' => 'required|exists:medicines,id',
    //         'items.*.quantity' => 'required|integer|min:1',
    //         'supplier_id' => 'required|exists:suppliers,id',
    //     ]);

    //     $pharmacist = Auth::guard('pharmacist')->user();
    //     $supplier = Supplier::findOrFail($request->supplier_id);

    //     foreach ($request->items as $item) {
    //         StockRequest::create([
    //             'pharmacist_id' => $pharmacist->id,
    //             'supplier_id' => $supplier->id,
    //             'medicine_id' => $item['medicine_id'],
    //             'requested_quantity' => $item['quantity'],
    //             'status' => 'pending',
    //         ]);
            
    //         // Optional: Send mail for each request
    //         // $medicine = Medicine::find($item['medicine_id']);
    //         // Mail::to($supplier->email)->send(new StockAlertMail($medicine, $pharmacist, $supplier, $item['quantity']));
    //     }

    //     return response()->json([
    //         'success' => true, 
    //         'message' => count($request->items) . ' restock request(s) sent successfully.'
    //     ]);
    // }
    
    /**
     * Export stock alerts report.
     */
    public function exportStockAlerts(Request $request)
    {
        $pharmacist = Auth::guard('pharmacist')->user();
        $medicineIds = explode(',', $request->query('ids', ''));

        return Excel::download(new StockAlertsExport($pharmacist->id, $medicineIds), 'stock_alerts_report.xlsx');
    }

    public function showBill(Bill $bill)
    {
        // Ensure the pharmacist can only view their own bills
        if ($bill->pharmacist_id !== auth('pharmacist')->id()) {
            abort(403);
        }

        // You need to create this view file: resources/views/pharmacist/billing/show.blade.php
        return view('pharmacist.billing.show', compact('bill'));
    }

    public function printBill(Bill $bill)
    {
        if ($bill->pharmacist_id !== auth('pharmacist')->id()) {
            abort(403);
        }

        // Load a dedicated view for the PDF
        // You need to create this view file: resources/views/pharmacist/billing/pdf.blade.php
        $pdf = Pdf::loadView('pharmacist.billing.pdf', compact('bill'));

        // Stream the PDF to the browser
        return $pdf->stream('bill-'.$bill->bill_number.'.pdf');
    }

    public function deleteBill(Bill $bill)
    {
        if ($bill->pharmacist_id !== auth('pharmacist')->id()) {
            abort(403);
        }
        
        // Note: You might want to restore medicine stock here before deleting
        // foreach ($bill->billItems as $item) {
        //     $item->medicine->increment('quantity', $item->quantity);
        // }

        $bill->delete(); // This performs a soft delete if your model uses it

        return redirect()->route('pharmacist.billing')->with('success', 'Bill deleted successfully.');
    }
}