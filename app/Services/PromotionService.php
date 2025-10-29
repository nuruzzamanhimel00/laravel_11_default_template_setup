<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Promotion;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Models\PromotionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PromotionService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(Promotion $model)
    {
        parent::__construct($model);
//        $this->fileUploadService = $fileUploadService;
    }
    public function createOrUpdate(Request|array $request, int $id = null): Promotion
    {

        $requestData = $request->except(['_token','_method']);


        try {
            DB::beginTransaction();
            if ($id) {
                // Update
                $promotion           = $this->get($id);
                $requestData['in_homepage'] = $request->has('in_homepage') ? (bool) $request->get('in_homepage') : 0;

                // Avatar
                if (isset($requestData['image']) && $requestData['image'] != null) {
                    $requestData['image']    = $this->fileUploadService->uploadFile($request,'image',Promotion::FILE_STORE_PATH,$promotion->image);
                }
                $promotion->update($requestData);

                $promotion->promotion_items()->delete();

                $data = $this->preparePromotionItemData(
                    $request->applied_for,
                    $request->applied_for_ids,
                    $request->start_date,
                    $request->end_date,
                    $request->target_type,
                );

                $promotion->promotion_items()->createMany($data);

                DB::commit();
                return $promotion;
            } else {
                // Create

                if (isset($requestData['image']) && $requestData['image'] != null) {
                    $requestData['image']      = $this->fileUploadService->uploadFile($request,'image',Promotion::FILE_STORE_PATH,);
                }

                // Store user
                $promotion                       = $this->model::create($requestData);

                $data = $this->preparePromotionItemData(
                    $request->applied_for,
                    $request->applied_for_ids,
                    $request->start_date,
                    $request->end_date,
                    $request->target_type,
                );

                $promotion->promotion_items()->createMany($data);

                DB::commit();
                return $promotion;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function preparePromotionItemData($appliedFor, $appliedForIds, $startDate, $endDate, $targetType)
    {
        $data = [];

        if ($appliedFor === Promotion::APPLICABLE_PRODUCTS) {
            foreach ($appliedForIds as $productId) {
                $data[] = [
                    'product_id' => $productId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ];
            }
            return $data;
        }

        // Applied to categories
        $products = Product::active()
            ->whereIn('category_id', $appliedForIds)
            ->get(['id', 'category_id']);

        if ($products->isEmpty()) {
            DB::rollBack();
            throw new \Exception('No active products found in the selected categories.');
        }

        $productIds = $products->pluck('id');

        $existingPromotionItems = PromotionItem::query()
            ->whereHas('promotion', function ($query) use ($targetType) {
                $query->where('target_type', $targetType)->active();
            })
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->whereIn('product_id', $productIds)
            ->pluck('product_id');

        $availableProducts = $products->reject(function ($product) use ($existingPromotionItems) {
            return $existingPromotionItems->contains($product->id);
        });

        if ($availableProducts->isEmpty()) {
            DB::rollBack();
            throw new \Exception('All products in the selected categories are already included in an active promotion.');
        }

        foreach ($availableProducts as $product) {
            $data[] = [
                'product_id' => $product->id,
                'category_id' => $product->category_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        }

        return $data;
    }


    public function destroy($id){
        $promotion = $this->get($id);
        $promotion->promotion_items()->delete();
        $promotion->delete();
    }

    public function getActive($selectedFields=[
            'id', 'title', 'message', 'image',
            'start_date', 'end_date', 'status',
            'target_type', 'applied_for',
            'offer_type', 'offer_value', 'in_homepage'
    ], $withRelations = [
        'promotion_items:id,promotion_id,category_id,product_id',
        'promotion_items.category',
        'promotion_items.product'
    ]): mixed
    {
        $request = request();

        $perPage = $request->get('par_page', 20);
        // $availableFor = $request->get('available_for'); // 'Restaurant' or 'Customer'
        $availableFor = auth('api')->check() ? auth('api')->user()->type: null;
        $inHomepage = $request->has('in_homepage') ? (bool) $request->get('in_homepage') : null;

        $relationships = [];

        if (in_array('promotion_items', $withRelations)) {
            $relationships['promotion_items'] = function ($q) use($withRelations) {
                $q->select('id', 'promotion_id', 'category_id','product_id');
                if (in_array('promotion_items.category', $withRelations)) {
                    $q->with('category');
                }
                if (in_array('promotion_items.product', $withRelations)) {
                    $q->with('product');
                }

            };
        }

        return $this->model
            ->active()
            ->with($withRelations)
            ->select($selectedFields)
            ->when($availableFor, fn($query) => $query->where('target_type', $availableFor))
            ->when(!is_null($inHomepage), fn($query) => $query->where('in_homepage', $inHomepage))
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->paginate($perPage);
    }


    public function getValidAppliedPromotionIds(string $targetType = User::TYPE_REGULAR_USER, $startDate = null, $endDate = null, ?array $ids = null): array
    {
        // $endDate = Carbon::parse($endDate)->subDay(1);
        // dd($endDate);
        $promotions = Promotion::query()
            ->active()
            ->where('target_type', $targetType)

            ->where(function ($query) use($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($subQuery) use($startDate, $endDate) {
                          $subQuery->where('start_date', '<=', $startDate)
                                   ->where('end_date', '>=', $endDate);
                      });
            })
            ->when($ids, fn($query) => $query->whereNotIn('id', $ids))
            ->latest()
            ->with([
                'promotion_items:id,promotion_id,category_id,product_id,start_date,end_date'
            ])
            ->get();

        // dd($promotions, $promotions->flatMap->promotion_items);

        $appliedCategories = $promotions->flatMap->promotion_items
        ->pluck('category_id')
        ->unique()
        ->filter() // Remove null and empty values
        ->values() // Reset array keys
        ->toArray() ?? [];

        $appliedProducts = $promotions->flatMap->promotion_items
            ->pluck('product_id')
            ->unique()
            ->filter() // Remove null and empty values
            ->values()
            ->toArray() ?? [];

        // dd('appliedProducts',$appliedProducts , $appliedCategories);

        return [
            'category_ids' => $appliedCategories,
            'product_ids' => $appliedProducts,
        ];
    }

}
