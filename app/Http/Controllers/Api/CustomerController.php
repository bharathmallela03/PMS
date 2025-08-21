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
        $this->middleware('auth:sanctum'); // Changed from 'auth:api' to 'auth:sanctum'
    }

    /**
     * Get customer dashboard data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        try {
            $customer = Auth::user();

            $data = [
                'total_orders' => Order::where('customer_id', $customer->id)->count(),
                'pending_orders' => Order::where('customer_id', $customer->id)->where('status', 'pending')->count(),
                'delivered_orders' => Order::where('customer_id', $customer->id)->where('status', 'delivered')->count(),
                'total_spent' => Order::where('customer_id', $customer->id)->where('payment_status', 'paid')->sum('total_amount'),
                'cart_items' => $this->getCartCount($customer->id),
                'recent_orders' => Order::with(['items.medicine', 'pharmacist'])
                                       ->where('customer_id', $customer->id)
                                       ->latest()->take(5)->get(),
                'recommended_medicines' => $this->getRecommendedMedicines($customer->id),
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data.'
            ], 500);
        }
    }

    /**
     * Get recommended medicines for a customer.
     *
     * @param  int  $customerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecommendedMedicines($customerId)
    {
        try {
            // Get medicines from customer's order history
            $orderedMedicineIds = Order::where('customer_id', $customerId)
                ->with('items')
                ->get()
                ->pluck('items')
                ->flatten()
                ->pluck('medicine_id')
                ->unique();

            if ($orderedMedicineIds->isEmpty()) {
                // If no order history, return popular/recent medicines
                return Medicine::with('company')
                    ->where('is_active', true)
                    ->where('quantity', '>', 0)
                    ->orderBy('created_at', 'desc')
                    ->limit(6)
                    ->get();
            }

            // Get categories of previously ordered medicines
            $categories = Medicine::whereIn('id', $orderedMedicineIds)
                ->pluck('category')
                ->unique()
                ->filter();

            if ($categories->isEmpty()) {
                // If no categories found, return recent medicines
                return Medicine::with('company')
                    ->where('is_active', true)
                    ->where('quantity', '>', 0)
                    ->orderBy('created_at', 'desc')
                    ->limit(6)
                    ->get();
            }

            // Get related medicines based on categories, excluding already ordered ones
            return Medicine::with('company')
                ->whereIn('category', $categories)
                ->whereNotIn('id', $orderedMedicineIds)
                ->where('is_active', true)
                ->where('quantity', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();

        } catch (\Exception $e) {
            // Return empty collection if there's an error
            return collect([]);
        }
    }

    /**
     * Get cart count for a customer.
     *
     * @param  int  $customerId
     * @return int
     */
    private function getCartCount($customerId)
    {
        return Cart::where('customer_id', $customerId)->sum('quantity');
    }

    /**
     * Get medicines with search and filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function medicines(Request $request)
    {
        try {
            $query = Medicine::with('company')->where('is_active', true)->where('quantity', '>', 0);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('generic_name', 'like', "%{$search}%");
                });
            }

            if ($request->has('category') && !empty($request->category)) {
                $query->where('category', $request->category);
            }

            $medicines = $query->paginate(12);

            return response()->json(['success' => true, 'data' => $medicines]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load medicines.'
            ], 500);
        }
    }

    /**
     * Get a specific medicine details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMedicine($id)
    {
        try {
            $medicine = Medicine::with(['company', 'pharmacist'])->where('is_active', true)->find($id);
            
            if(!$medicine) {
                return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
            }
            
            return response()->json(['success' => true, 'data' => $medicine]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load medicine details.'
            ], 500);
        }
    }

    /**
     * Get customer cart items.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cart()
    {
        try {
            $customer = Auth::user();
            $cartItems = Cart::with('medicine.company')->where('customer_id', $customer->id)->get();
            
            $total = $cartItems->sum(function($item) {
                return $item->quantity * $item->medicine->price;
            });

            return response()->json([
                'success' => true, 
                'data' => [
                    'cart_items' => $cartItems, 
                    'total' => $total,
                    'count' => $cartItems->sum('quantity')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load cart.'
            ], 500);
        }
    }

    /**
     * Add item to cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $customer = Auth::user();
            $medicine = Medicine::findOrFail($request->medicine_id);

            if (!$medicine->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This medicine is not available.'
                ], 400);
            }

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
                $newQuantity = $cartItem->quantity + $request->quantity;
                if ($medicine->quantity < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. Total quantity would exceed available stock.'
                    ], 400);
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
                'cart_count' => $this->getCartCount($customer->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart.'
            ], 500);
        }
    }

    /**
     * Update cart item quantity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCartItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $customer = Auth::user();
            $cartItem = Cart::where('customer_id', $customer->id)->findOrFail($id);
            
            $medicine = $cartItem->medicine;
            if ($medicine->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Only ' . $medicine->quantity . ' items available.'
                ], 400);
            }

            $cartItem->update(['quantity' => $request->quantity]);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully.',
                'cart_count' => $this->getCartCount($customer->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart item.'
            ], 500);
        }
    }

    /**
     * Remove item from cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromCart($id)
    {
        try {
            $customer = Auth::user();
            $cartItem = Cart::where('customer_id', $customer->id)->findOrFail($id);
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully.',
                'cart_count' => $this->getCartCount($customer->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart.'
            ], 500);
        }
    }

    /**
     * Get customer orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request)
    {
        try {
            $customer = Auth::user();
            
            $orders = Order::with(['items.medicine', 'customerAddress', 'pharmacist'])
                ->where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders.'
            ], 500);
        }
    }

    /**
     * Get a specific order details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showOrder($id)
    {
        try {
            $customer = Auth::user();
            
            $order = Order::with(['items.medicine', 'customerAddress', 'pharmacist'])
                ->where('customer_id', $customer->id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }
    }

    /**
     * Place an order from cart items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'required|string|in:cash,card,upi',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $customer = Auth::user();
            $cartItems = Cart::with('medicine')->where('customer_id', $customer->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty.'
                ], 400);
            }

            // Calculate total
            $totalAmount = $cartItems->sum(function($item) {
                return $item->quantity * $item->medicine->price;
            });

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_address_id' => $request->address_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_date' => now(),
            ]);

            // Create order items and update medicine stock
            foreach ($cartItems as $cartItem) {
                $medicine = $cartItem->medicine;
                
                // Check stock availability
                if ($medicine->quantity < $cartItem->quantity) {
                    $order->delete(); // Rollback order creation
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$medicine->name}. Only {$medicine->quantity} items available."
                    ], 400);
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'medicine_id' => $medicine->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $medicine->price,
                    'total' => $cartItem->quantity * $medicine->price,
                ]);

                // Update medicine stock
                $medicine->decrement('quantity', $cartItem->quantity);
            }

            // Clear cart after successful order
            Cart::where('customer_id', $customer->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order' => $order->load(['items.medicine', 'customerAddress'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.'
            ], 500);
        }
    }

    /**
     * Get customer addresses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addresses()
    {
        try {
            $customer = Auth::user();
            $addresses = CustomerAddress::where('customer_id', $customer->id)->get();

            return response()->json([
                'success' => true,
                'addresses' => $addresses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load addresses.'
            ], 500);
        }
    }

    /**
     * Add a new customer address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:home,work,other',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $customer = Auth::user();

            // If this is set as default, remove default from other addresses
            if ($request->is_default) {
                CustomerAddress::where('customer_id', $customer->id)
                    ->update(['is_default' => false]);
            }

            $address = CustomerAddress::create([
                'customer_id' => $customer->id,
                'type' => $request->type,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'is_default' => $request->is_default ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully!',
                'address' => $address
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add address.'
            ], 500);
        }
    }

    /**
     * Update customer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $customer = Auth::user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:customers,email,' . $customer->id,
                'contact_number' => 'sometimes|required|string|max:20',
                'current_password' => 'required_with:password|string',
                'password' => 'sometimes|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If password is being updated, verify current password
            if ($request->has('password')) {
                if (!Hash::check($request->current_password, $customer->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect.'
                    ], 422);
                }
                $request->merge(['password' => Hash::make($request->password)]);
            }

            $customer->update($request->only(['name', 'email', 'contact_number', 'password']));

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'user' => $customer->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile.'
            ], 500);
        }
    }

    /**
     * Get medicine categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        try {
            $categories = Medicine::where('is_active', true)
                ->where('quantity', '>', 0)
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();

            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories.'
            ], 500);
        }
    }
}