<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Services\PromotionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\DataTables\PromotionDataTable;

use App\Http\Requests\PromotionRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;

    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Promotion'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Promotion'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Promotion'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Promotion'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Restore Promotion'), only: ['restore']),
        ];
    }


    public function index(PromotionDataTable $dataTable)
    {
        setPageMeta('Promotion List');

        setCreateRoute(route('promotions.create'),'route');

        return $dataTable->render('admin.promotion.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(Request $request): \Illuminate\View\View
    {
        setPageMeta(__('Add Promotion'));
        setCreateRoute(null);


        $target_type = $request->target_type ?? null;

        return view('admin.promotion.create',compact('target_type'));
    }

    public function validProductCategory(Request $request)
    {
        $targetType = $request->target_type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $promotionId = $request->id;

        $excludedIds = $promotionId
            ? [$promotionId]
            : null;

        $applied = $this->promotionService->getValidAppliedPromotionIds(
            $targetType,
            $startDate,
            $endDate,
            $excludedIds
        );

        $categories = Category::active()
            ->latest()
            ->when(!empty($applied['category_ids']), fn($query) =>
                $query->whereNotIn('id', $applied['category_ids'])
            )
            ->has('products')
            ->childCategory()
            ->get();

        $products = Product::active()
            ->latest()
            ->when(!empty($applied['product_ids']), fn($query) =>
                $query->whereNotIn('id', $applied['product_ids'])
            )
            ->get();

        return response()->json([
            'categories' => $categories,
            'products' => $products,
        ]);
    }


    public function store(PromotionRequest $request) : \Illuminate\Http\RedirectResponse
    {
        $request->validated();
        // dd('d');
        try {
            $this->promotionService->createOrUpdate($request);

            sendFlash('Successfully created', 'success');
            return redirect()->route('promotions.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit(Request $request, $id) : \Illuminate\View\View
    {
        setPageMeta('Edit Promotion');
        setCreateRoute(null);
        $promotion = $this->promotionService->get($id)->load('promotion_items');


        $appliedCategories = $promotion->promotion_items
        ->pluck('category_id')
        ->unique()
        ->filter() // Remove null and empty values
        ->values() // Reset array keys
        ->toArray() ?? [];

        $appliedProducts = $promotion->promotion_items
            ->pluck('product_id')
            ->unique()
            ->filter() // Remove null and empty values
            ->values()
            ->toArray() ?? [];

        // dd($promotion , $appliedCategories , $appliedProducts);

        $target_type =  $promotion->target_type ;



        return view('admin.promotion.edit', compact('promotion','target_type','appliedCategories','appliedProducts'));
    }
    public function show($id) : \Illuminate\View\View
    {
        setPageMeta('Show Promotion');
        setCreateRoute(null);
        $promotion = $this->promotionService->get($id)->load([
            'promotion_items.category',
            'promotion_items.product',
        ]);

        return view('admin.promotion.show', compact('promotion'));
    }


    public function update(PromotionRequest $request, $id): RedirectResponse
    {
        $request->validated();
        try {
            $this->promotionService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('promotions.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        try {
            $this->promotionService->destroy($id);

            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }
    public function bulk_destroy(Request $request) : RedirectResponse
    {
        try {
            $userIds = explode(",", $request->id);
            if (count($userIds) > 0) {
                foreach ($userIds as $key => $userId) {
                    $this->promotionService->deleteForceDeleteModel($userId);
                }

            }
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }
    public function restore($id) : RedirectResponse
    {
        try {
            $this->promotionService->restore($id);
            sendFlash(__('Successfully Restored'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

}
