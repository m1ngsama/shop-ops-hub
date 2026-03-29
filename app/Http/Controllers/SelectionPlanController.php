<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\AuditLogService;
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

    public function store(
        Request $request,
        Product $product,
        SelectionPlanService $selectionPlanService,
        AuditLogService $auditLogService
    ): RedirectResponse
    {
        $quantity = max(1, (int) $request->integer('quantity', 1));

        $selectionPlanService->add($product, $quantity);
        $auditLogService->record('storefront.plan.added', $request, subject: $product, meta: [
            'sku' => $product->sku,
            'quantity' => $quantity,
        ]);

        return back()->with('status', "{$product->name} 已加入意向清单。");
    }

    public function update(
        Request $request,
        Product $product,
        SelectionPlanService $selectionPlanService,
        AuditLogService $auditLogService
    ): RedirectResponse
    {
        $quantity = (int) $request->integer('quantity', 1);
        $selectionPlanService->update($product, $quantity);
        $auditLogService->record('storefront.plan.updated', $request, subject: $product, meta: [
            'sku' => $product->sku,
            'quantity' => $quantity,
        ]);

        return back()->with('status', "{$product->name} 的意向数量已更新。");
    }

    public function destroy(
        Request $request,
        Product $product,
        SelectionPlanService $selectionPlanService,
        AuditLogService $auditLogService
    ): RedirectResponse
    {
        $selectionPlanService->remove($product);
        $auditLogService->record('storefront.plan.removed', $request, subject: $product, meta: [
            'sku' => $product->sku,
        ]);

        return back()->with('status', "{$product->name} 已从意向清单移除。");
    }

    public function clear(
        Request $request,
        SelectionPlanService $selectionPlanService,
        AuditLogService $auditLogService
    ): RedirectResponse
    {
        $snapshot = $selectionPlanService->snapshot();
        $selectionPlanService->clear();
        $auditLogService->record('storefront.plan.cleared', $request, meta: [
            'line_count' => $snapshot['summary']['line_count'],
            'total_quantity' => $snapshot['summary']['total_quantity'],
            'estimated_value' => $snapshot['summary']['estimated_value'],
        ]);

        return redirect()
            ->route('storefront.plan.index')
            ->with('status', '意向清单已清空。');
    }
}
