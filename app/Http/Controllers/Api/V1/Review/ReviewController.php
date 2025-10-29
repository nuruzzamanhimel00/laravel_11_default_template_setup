<?php

namespace App\Http\Controllers\Api\V1\Review;

use App\Models\Review;
use App\Models\ReviewImage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use App\Traits\PaginatedResourceTrait;
use App\Http\Requests\Api\V1\ReviewRequest;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Review\ReviewResource;
use App\Http\Resources\Category\CategoryResource;

class ReviewController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $reviewService;
    public $fileUploadService;
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        $this->fileUploadService = app(FileUploadService::class);
    }
    //get all active pages
    public function index()
    {

        try {
            $review = $this->reviewService->getPaginate();
            // return $review;
            $resource =  $this->paginatedResponse($review, ReviewResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function getProductReviews($id)
    {

        try {
            $review = $this->reviewService->getProductReviewsPaginate($id);
            // return $review;
            $resource =  $this->paginatedResponse($review, ReviewResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $review = $this->reviewService->getData($id);
    //         if (!$review) {
    //             return $this->error('review not found', 404);
    //         }
    //         return $this->success(new ReviewResource($review));
    //     } catch (\Exception $e) {
    //         logger($e->getMessage());
    //         return $this->error($e->getMessage());
    //     }
    // }
/**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request)
    {
        $request->validated();
        $request->merge(['user_id' => auth()->user()->id]);
        // dd($request->all());
        try {
            $this->reviewService->canReviewValidation($request->product_id);
            $res = $this->reviewService->createOrUpdate($request);
            return $this->success($res,'Created successfully',200);

        }catch(\Exception $e){
        logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function update(ReviewRequest $request,$id)
    {
        $request->validated();
        // dd($request->all());
        try {
            $review = $this->reviewService->get($id);
            if(!$review){
                throw new \Exception('Review not found');
            }

            $this->reviewService->allowReviewValidation($review->user_id);
            $this->reviewService->createOrUpdate($request,$id);
            return $this->success('','Update successfully',200);

        }catch(\Exception $e){
        logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function destroy($id){
        try {
            $review = $this->reviewService->get($id);
            if(!$review){
                throw new \Exception('Review not found');
            }

            $this->reviewService->allowReviewValidation($review->user_id);

            $res = $this->reviewService->destroy($id);
            return $this->success($res,'Deleted successfully',200);

        }catch(\Exception $e){
        logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function canReview($id){
        if(!auth()->check()){
            return response()->json([
                'status' => false
            ]);
        }
        try {
            $this->reviewService->canReviewValidation($id);
            return response()->json([
                'status' => true
            ]);

        }catch(\Exception $e){
            logger($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
            // return $this->error($e->getMessage());
        }
    }



}
