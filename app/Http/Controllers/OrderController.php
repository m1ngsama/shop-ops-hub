<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();
        $channelId = $request->integer('channel');

        $orders = Order::query()
            ->with(['product', 'channel'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('external_order_no', 'like', "%{$search}%")
                        ->orWhereHas('product', fn ($productQuery) => $productQuery
                            ->where('sku', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($channelId > 0, fn ($query) => $query->where('channel_id', $channelId))
            ->latest('ordered_at')
            ->paginate(12)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'channels' => Channel::query()->orderBy('name')->get(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'channel' => $channelId > 0 ? $channelId : null,
            ],
        ]);
    }
}
