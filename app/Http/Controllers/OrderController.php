<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('orders.index', [
            'orders' => Order::query()
                ->with(['product', 'channel'])
                ->latest('ordered_at')
                ->get(),
        ]);
    }
}
