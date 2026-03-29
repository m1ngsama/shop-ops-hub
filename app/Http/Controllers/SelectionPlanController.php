<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SelectionPlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SelectionPlanController extends Controller
{
    public function index(SelectionPlanService $selectionPlanService): View
    {
        $snapshot = $selectionPlanService->snapshot();

        return view('storefront.plan', [
            'items' => $snapshot['items'],
            'summary' => $snapshot['summary'],
            'planSummary' => $snapshot['summary'],
        ]);
    }

    public function store(Request $request, Product $product, SelectionPlanService $selectionPlanService): RedirectResponse
    {
        $quantity = max(1, (int) $request->integer('quantity', 1));

        $selectionPlanService->add($product, $quantity);

        return back()->with('status', "{$product->name} 已加入意向清单。");
    }

    public function update(Request $request, Product $product, SelectionPlanService $selectionPlanService): RedirectResponse
    {
        $quantity = (int) $request->integer('quantity', 1);
        $selectionPlanService->update($product, $quantity);

        return back()->with('status', "{$product->name} 的意向数量已更新。");
    }

    public function destroy(Product $product, SelectionPlanService $selectionPlanService): RedirectResponse
    {
        $selectionPlanService->remove($product);

        return back()->with('status', "{$product->name} 已从意向清单移除。");
    }

    public function clear(SelectionPlanService $selectionPlanService): RedirectResponse
    {
        $selectionPlanService->clear();

        return redirect()
            ->route('storefront.plan.index')
            ->with('status', '意向清单已清空。');
    }
}
