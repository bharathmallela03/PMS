<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Medicine;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomerAddress;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        
        $data = [
            'total_orders' => Order::where('customer_id', $customer->id)->count(),
            'pending_orders' => Order::where('customer_id', $customer->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('customer_id', $customer->id)->where('status', 'delivered')->count(),
            'total_spent' => Order::where('customer_id', $customer->id)->where('payment_status', 'paid')->sum('total_amount'),
            'cart_items' => $customer->getCartCount(),
            'recent_orders' => Order::with(['items.medicine', 'pharmacist'])
                                   ->where('customer_id', $customer->id)
                                   ->latest()->take(5)->get(),
            'recommended_medicines' => $this->getRecommendedMedicines($customer->id),
        ];

        return view('customer.dashboard', $data);
    }

    // Medicine Browse
    public function medicines(Request $request)
    {
        $query = Medicine::with('company')->where('is_active', true)->where('quantity', '>', 0);

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

        if ($request->has('price_range')) {
            switch ($request->price_range) {
                case 'under_50':
                    $query->where('price', '<', 50);
                    break;
                case '50_100':
                    $query->whereBetween('price', [50, 100]);
                    break;
                case '100_500':
                    $query->whereBetween('price', [100, 500]);
                    break;
                case 'above_500':
                    $query->where('price', '>', 500);
                    break;
            }
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $medicines = $query->paginate(12);
        $categories = Medicine::active()->distinct()->pluck('category')->filter();

        return view('customer.medicines.index', compact('medicines', 'categories'));
    }

    public function showMedicine($id)
    {
        $medicine = Medicine::with(['company', 'pharmacist'])->where('is_active', true)->findOrFail($id);
        $relatedMedicines = Medicine::where('category', $medicine->category)
                                  ->where('id', '!=', $medicine->id)
                                  ->where('is_active', true)
                                  ->where('quantity', '>', 0)
                                  ->take(4)->get();

        return view('customer.medicines.show', compact('medicine', 'relatedMedicines'));
    }

    public function searchMedicines(Request $request)
    {
        $search = $request->get('q');
        $medicines = Medicine::where('is_active', true)
                           ->where('quantity', '>', 0)
                           ->where(function($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%")
                                     ->orWhere('brand', 'like', "%{$search}%")
                                     ->orWhere('generic_name', 'like', "%{$search}%");
                           })
                           ->take(10)->get();

        return response()->json($medicines);
    }

    // Cart Management
    public function cart()
    {
        $customer = Auth::guard('customer')->user();
        $cartItems = Cart::with('medicine')->where('customer_id', $customer->id)->get();
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->medicine->price;
        });

        return view('customer.cart.index', compact('cartItems', 'total'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $customer = Auth::guard('customer')->user();
        $medicine = Medicine::findOrFail($request->medicine_id);

        // Check if medicine is available
        if ($medicine->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $medicine->quantity . ' items available.'
            ]);
        }

        // Check if item already exists in cart
        $cartItem = Cart::where('customer_id', $customer->id)
                       ->where('medicine_id', $request->medicine_id)
                       ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($medicine->quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Total would exceed available stock.'
                ]);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'customer_id' => $customer->id,
                'medicine_id' => $request->medicine_id,
                'quantity' => $request->quantity,
                'price' => $medicine->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully.',
            'cart_count' => $customer->getCartCount()
        ]);
    }

    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $customer = Auth::guard('customer')->user();
        $cartItem = Cart::where('customer_id', $customer->id)->findOrFail($id);

        // Check stock availability
        if ($cartItem->medicine->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $cartItem->medicine->quantity . ' items available.'
            ]);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully.',
            'subtotal' => $cartItem->quantity * $cartItem->medicine->price
        ]);
    }

    public function removeFromCart($id)
    {
        $customer = Auth::guard('customer')->user();
        $cartItem = Cart::where('customer_id', $customer->id)->findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully.',
            'cart_count' => $customer->getCartCount()
        ]);
    }

    public function clearCart()
    {
        $customer = Auth::guard('customer')->user();
        Cart::where('customer_id', $customer->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully.'
        ]);
    }

    // Checkout
    public function checkout()
    {
        $customer = Auth::guard('customer')->user();
        $cartItems = Cart::with('medicine')->where('customer_id', $customer->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->medicine->price;
        });

        $addresses = CustomerAddress::where('customer_id', $customer->id)->get();

        return view('customer.checkout', compact('cartItems', 'subtotal', 'addresses'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,online,card',
            'delivery_address' => 'required|array',
            'delivery_address.name' => 'required|string|max:255',
            'delivery_address.phone' => 'required|string|max:20',
            'delivery_address.address_line_1' => 'required|string|max:255',
            'delivery_address.address_line_2' => 'nullable|string|max:255',
            'delivery_address.city' => 'required|string|max:100',
            'delivery_address.state' => 'required|string|max:100',
            'delivery_address.pincode' => 'required|string|max:10',
            'delivery_address.country' => 'required|string|max:100',
        ]);

        $customer = Auth::guard('customer')->user();
        $cartItems = Cart::with('medicine')->where('customer_id', $customer->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.'
            ]);
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->medicine->quantity < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$item->medicine->name}. Only {$item->medicine->quantity} items available."
                ]);
            }
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->medicine->price;
        });

        $shippingAmount = $subtotal > 500 ? 0 : 50; // Free shipping above 500
        $taxAmount = $subtotal * 0.05; // 5% tax
        $totalAmount = $subtotal + $shippingAmount + $taxAmount;

        // Get pharmacist (for now, use the first medicine's pharmacist)
        $pharmacist = $cartItems->first()->medicine->pharmacist;

        // Create order
        $order = Order::create([
            'customer_id' => $customer->id,
            'pharmacist_id' => $pharmacist->id,
            'status' => Order::STATUS_PENDING,
            'payment_status' => $request->payment_method === 'cod' ? Order::PAYMENT_PENDING : Order::PAYMENT_PAID,
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'total_amount' => $totalAmount,
            'shipping_address' => $request->delivery_address,
            'billing_address' => $request->delivery_address,
        ]);

        // Create order items and update stock
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'medicine_id' => $item->medicine_id,
                'quantity' => $item->quantity,
                'price' => $item->medicine->price,
                'total' => $item->quantity * $item->medicine->price,
            ]);

            // Update medicine stock
            $item->medicine->decrement('quantity', $item->quantity);
        }

        // Clear cart
        Cart::where('customer_id', $customer->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $order->id,
            'redirect_url' => route('customer.orders.show', $order->id)
        ]);
    }

    // Orders
    public function orders(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $query = Order::with(['items.medicine', 'pharmacist'])
                     ->where('customer_id', $customer->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $customer = Auth::guard('customer')->user();
        $order = Order::with(['items.medicine', 'pharmacist'])
                     ->where('customer_id', $customer->id)
                     ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }

    public function cancelOrder($id)
    {
        $customer = Auth::guard('customer')->user();
        $order = Order::where('customer_id', $customer->id)->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled.'
            ]);
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'cancelled_at' => now()
        ]);

        // Restore medicine stock
        foreach ($order->items as $item) {
            $item->medicine->increment('quantity', $item->quantity);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
    }

    public function downloadInvoice($id)
    {
        $customer = Auth::guard('customer')->user();
        $order = Order::with(['items.medicine', 'pharmacist'])
                     ->where('customer_id', $customer->id)
                     ->findOrFail($id);

        $pdf = Pdf::loadView('customer.orders.invoice-pdf', compact('order'));
        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    // Profile Management
    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile.index', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . auth('customer')->id(),
            'contact_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
        ]);

        $customer = Auth::guard('customer')->user();
        $customer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Auth::guard('customer')->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);
        }

        $customer->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }

    // Address Management
    public function addresses()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = CustomerAddress::where('customer_id', $customer->id)->get();

        return view('customer.addresses.index', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:home,office,other',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        $customer = Auth::guard('customer')->user();

        CustomerAddress::create([
            'customer_id' => $customer->id,
            'type' => $request->type,
            'name' => $request->name,
            'phone' => $request->phone,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.'
        ]);
    }

    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|in:home,office,other',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        $customer = Auth::guard('customer')->user();
        $address = CustomerAddress::where('customer_id', $customer->id)->findOrFail($id);

        $address->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.'
        ]);
    }

    public function deleteAddress($id)
    {
        $customer = Auth::guard('customer')->user();
        $address = CustomerAddress::where('customer_id', $customer->id)->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.'
        ]);
    }

    // Helper Methods
    private function getRecommendedMedicines($customerId)
    {
        // Get medicines from previous orders
        $previousMedicines = Medicine::select('medicines.*')
                                   ->join('order_items', 'medicines.id', '=', 'order_items.medicine_id')
                                   ->join('orders', 'order_items.order_id', '=', 'orders.id')
                                   ->where('orders.customer_id', $customerId)
                                   ->where('medicines.is_active', true)
                                   ->where('medicines.quantity', '>', 0)
                                   ->distinct()
                                   ->take(4)
                                   ->get();

        // If no previous orders, get popular medicines
        if ($previousMedicines->isEmpty()) {
            $previousMedicines = Medicine::select('medicines.*')
                                       ->join('order_items', 'medicines.id', '=', 'order_items.medicine_id')
                                       ->where('medicines.is_active', true)
                                       ->where('medicines.quantity', '>', 0)
                                       ->selectRaw('sum(order_items.quantity) as total_sold')
                                       ->groupBy('medicines.id')
                                       ->orderBy('total_sold', 'desc')
                                       ->take(4)
                                       ->get();
        }

        return $previousMedicines;
    }
}