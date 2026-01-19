<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string|max:20',
            'total_price' => 'required|numeric',
            'items' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'total_price' => $validated['total_price'],
            'status' => 'pending',
            'items' => $validated['items'],
            'notes' => $validated['notes'],
        ]);

        return response()->json($order, 201);
    }
}
