<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Models\StockLog;
use App\Traits\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    use IsAdmin;

    /**
     * Get dashboard overview statistics
     */
    public function overview()
    {
        $this->authorizeAdmin();

        $totalUsers = User::count();
        $totalCustomers = User::whereHas('role', function ($q) {
            $q->where('name', 'customer');
        })->count();

        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_amount');

        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 10)->count();

        $activeOrders = Order::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->count();

        $pendingPayments = Payment::where('status', 'pending')->count();

        return response()->json([
            'message' => 'Dashboard overview retrieved successfully',
            'data' => [
                'users' => [
                    'total' => $totalUsers,
                    'customers' => $totalCustomers,
                    'admins' => $totalUsers - $totalCustomers,
                ],
                'orders' => [
                    'total' => $totalOrders,
                    'active' => $activeOrders,
                    'total_revenue' => $totalRevenue,
                ],
                'products' => [
                    'total' => $totalProducts,
                    'low_stock' => $lowStockProducts,
                ],
                'payments' => [
                    'pending' => $pendingPayments,
                ],
            ],
        ]);
    }

    /**
     * Get sales statistics
     */
    public function sales(Request $request)
    {
        $this->authorizeAdmin();

        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays((int) $period);

        $salesByDate = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->orderBy('date')
            ->get();

        $totalSales = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('total_amount');

        $totalOrders = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->count();

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return response()->json([
            'message' => 'Sales statistics retrieved successfully',
            'data' => [
                'period_days' => $period,
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order_value' => round($averageOrderValue, 2),
                'sales_by_date' => $salesByDate,
            ],
        ]);
    }

    /**
     * Get top selling products
     */
    public function topProducts(Request $request)
    {
        $this->authorizeAdmin();

        $limit = $request->get('limit', 10);
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int) $period);

        $topProducts = Product::with('category')
            ->whereHas('order_items', function ($q) use ($startDate) {
                $q->whereHas('order', function ($oq) use ($startDate) {
                    $oq->where('status', 'completed')
                        ->where('created_at', '>=', $startDate);
                });
            })
            ->withCount(['order_items' => function ($q) use ($startDate) {
                $q->whereHas('order', function ($oq) use ($startDate) {
                    $oq->where('status', 'completed')
                        ->where('created_at', '>=', $startDate);
                });
            }])
            ->withSum(['order_items' => function ($q) use ($startDate) {
                $q->whereHas('order', function ($oq) use ($startDate) {
                    $oq->where('status', 'completed')
                        ->where('created_at', '>=', $startDate);
                });
            }], 'quantity')
            ->orderByDesc('order_items_count')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'quantity_sold' => $product->order_items_sum_quantity ?? 0,
                    'orders_count' => $product->order_items_count,
                    'price' => $product->price,
                ];
            });

        return response()->json([
            'message' => 'Top products retrieved successfully',
            'data' => [
                'period_days' => $period,
                'top_products' => $topProducts,
            ],
        ]);
    }

    /**
     * Get low stock products
     */
    public function lowStockProducts(Request $request)
    {
        $this->authorizeAdmin();

        $threshold = $request->get('threshold', 10);
        $perPage = $request->get('per_page', 15);

        $products = Product::with('category')
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->paginate($perPage);

        return response()->json([
            'message' => 'Low stock products retrieved successfully',
            'data' => $products,
        ]);
    }

    /**
     * Get recent orders
     */
    public function recentOrders(Request $request)
    {
        $this->authorizeAdmin();

        $limit = $request->get('limit', 10);
        $status = $request->get('status');

        $orders = Order::with('user', 'order_items')
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'Recent orders retrieved successfully',
            'data' => $orders,
        ]);
    }

    /**
     * Get order statistics by status
     */
    public function orderStats()
    {
        $this->authorizeAdmin();

        $stats = Order::groupBy('status')
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total_revenue')
            ->get()
            ->keyBy('status');

        return response()->json([
            'message' => 'Order statistics retrieved successfully',
            'data' => $stats,
        ]);
    }

    /**
     * Get payment statistics
     */
    public function paymentStats()
    {
        $this->authorizeAdmin();

        $stats = Payment::groupBy('status')
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total_amount')
            ->get()
            ->keyBy('status');

        $totalProcessed = Payment::where('status', 'success')->sum('amount');

        return response()->json([
            'message' => 'Payment statistics retrieved successfully',
            'data' => [
                'by_status' => $stats,
                'total_processed' => $totalProcessed,
            ],
        ]);
    }

    /**
     * Get stock movement
     */
    public function stockMovement(Request $request)
    {
        $this->authorizeAdmin();

        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);

        $movements = StockLog::where('created_at', '>=', $startDate)
            ->with('product')
            ->orderByDesc('created_at')
            ->paginate(20);

        $summary = StockLog::where('created_at', '>=', $startDate)
            ->groupBy('type')
            ->selectRaw('type, COUNT(*) as count, SUM(quantity) as total_quantity')
            ->get()
            ->keyBy('type');

        return response()->json([
            'message' => 'Stock movement retrieved successfully',
            'data' => [
                'movements' => $movements,
                'summary' => $summary,
            ],
        ]);
    }
}
