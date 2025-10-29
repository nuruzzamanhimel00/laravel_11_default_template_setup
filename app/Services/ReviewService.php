<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use App\Models\ReviewImage;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ReviewService extends BaseService
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }



    // store location
    public function createOrUpdate(Request|array $request, int $id = null): Review
    {
        $reviewData = is_array($request)
            ? Arr::except($request, ['_token', '_method', 'images'])
            : $request->except(['_token', '_method', 'images']);


        try {
            DB::beginTransaction();

            if ($id) {
                // Update
                $review = $this->get($id);
                if(!$review){
                    throw new \Exception('Review not found');
                }


                $review->update($reviewData);

                $review->images()->delete();

                $reviewImages = $this->prepareReviewImages($request, true);
                if (!empty($reviewImages)) {
                    $review->images()->createMany($reviewImages);
                }

            } else {

                // Create
                $review = Review::create($reviewData);

                $reviewImages = $this->prepareReviewImages($request);
                if (!empty($reviewImages)) {
                    $review->images()->createMany($reviewImages);
                }
            }

            DB::commit();
            return $review;

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function allowReviewValidation($review_user_id){
        if($review_user_id != auth()->id()){
                    throw new \Exception('You are not allowed to update this review');
            }
    }
    public function canReviewValidation($product_id){
        if(!auth()->check()){
            throw new \Exception('You are not logged in');
        }
        // dd( auth()->id());
        $product = Product::where('id', $product_id)
        ->active()
        ->with(['review'=>function($q){
            return $q->where('user_id', auth()->id());
        },'order_items.order'=>function($q){
            return $q->where('order_for_id', auth()->id())
            ->where('order_status',Order::STATUS_ORDER_PACKAGED)
            ->where('delivery_status',Order::STATUS_DELIVERY_COMPLETE);
        }])

        ->first();

        if(!$product){
            throw new \Exception('You are not allowed to add review for this product');
        }
        if($product->order_items->count() == 0){
            throw new \Exception('You can not add review for this product');
        }
        if($product->review){
            throw new \Exception('You have already reviewed this product');
        }
    }

    private function prepareReviewImages($request, bool $isUpdate = false): array
    {
        $reviewImages = [];

        if ($request instanceof Request && $request->has('images') && is_array($request->images)) {
            foreach ($request->images as $key => $imageGroup) {
                if ($request->hasFile("images.$key.image")) {
                    $path = $this->fileUploadService->uploadFile(
                        $request,
                        "images.$key.image",
                        upload_path: ReviewImage::FILE_STORE_PATH,
                        delete_path: $isUpdate && !empty($imageGroup['old_image']) ? $imageGroup['old_image'] : null
                    );

                    $reviewImages[] = ['image' => $path];
                } elseif ($isUpdate && !empty($imageGroup['old_image'])) {
                    $reviewImages[] = ['image' => $imageGroup['old_image'] ?? ''];
                }
            }
        }

        return $reviewImages;
    }


    // location destroy
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $review = $this->get($id)->load(['images']);
            if($review->images->count() > 0){
                foreach($review->images as $image){
                    $this->fileUploadService->delete($image->image);
                }
                $review->images()->delete();
            }
            $review->delete();
            DB::commit();
            return true;
        }catch(\Exception $e){
            logger($e->getMessage());
            DB::rollBack();
            return false;
        }
    }


    public function getPaginate()
    {
        return $this->model->active()
        // ->select(['id', 'name', 'image', 'status'])
        ->with(['images','customer','product'])
        ->latest()
        ->paginate(10);
    }
    public function getProductReviewsPaginate($id)
    {
        return $this->model->active()
        // ->select(['id', 'name', 'image', 'status'])
        ->where('product_id', $id)
        ->with(['images','customer'=>function($q){
            return $q->select(['id','first_name','avatar']);
        },'product'=>function($q){
            return $q->select(['id','name','image','purchase_price','sale_price','restaurant_sale_price']);
        }])
        ->latest()
        ->paginate(10);
    }
    public function getData($id)
    {
        try {
            return $this->model->active()
            ->with(['images'])
            ->where('id', $id)
            ->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }
    }

}
