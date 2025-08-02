<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Medicine;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function dashboard()
    {
        $customer = Auth::user();

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

        return response()->json(['success' => true, 'data' => $data]);
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

        $medicines = $query->paginate(12);

        return response()->json(['success' => true, 'data' => $medicines]);
    }

    public function showMedicine($id)
    {
        $medicine = Medicine::with(['company', 'pharmacist'])->where('is_active', true)->find($id);
        if(!$medicine) {
            return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $medicine]);
    }

    // Cart Management
    public function cart()
    {
        $customer = Auth::user();
        $cartItems = Cart::with('medicine')->where('customer_id', $customer->id)->get();
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->medicine->price;
        });

        return response()->json(['success' => true, 'data' => ['cart_items' => $cartItems, 'total' => $total]]);
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $customer = Auth::user();
        $medicine = Medicine::findOrFail($request->medicine_id);

        if ($medicine->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $medicine->quantity . ' items available.'
            ], 400);
        }

        $cartItem = Cart::where('customer_id', $customer->id)
                       ->where('medicine_id', $request->medicine_id)
                       ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
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

    public function removeFromCart($id)
    {
        $customer = Auth::user();
        $cartItem = Cart::where('customer_id', $customer->id)->findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully.',
            'cart_count' => $customer->getCartCount()
        ]);
    }

    // ... other methods from your controller converted to return JSON
}
