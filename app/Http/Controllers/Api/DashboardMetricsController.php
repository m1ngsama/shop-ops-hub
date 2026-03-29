<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetricsService;
use App\Services\ReplenishmentService;
use Illuminate\Http\JsonResponse;

class DashboardMetricsController extends Controller
{
    public function __invoke(
        DashboardMetricsService $dashboardMetricsService,
        ReplenishmentService $replenishmentService
    ): JsonResponse {
        return response()->json([
            'summary' => $dashboardMetricsService->summary(),
            'channels' => $dashboardMetricsService->channelPerformance(),
            'top_products' => $dashboardMetricsService->topProducts(),
            'low_stock' => $replenishmentService->recommendations()->take(5)->map(function (array $alert): array {
                return [
                    'sku' => $alert['product']->sku,
                    'name' => $alert['product']->name,
                    'available_units' => $alert['available_units'],
                    'safety_stock' => $alert['product']->safety_stock,
                ];
            }),
        ]);
    }
}
