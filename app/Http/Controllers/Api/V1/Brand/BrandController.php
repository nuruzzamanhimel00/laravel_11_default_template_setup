<?php

namespace App\Http\Controllers\Api\V1\Brand;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Category\CategoryResource;

class BrandController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $brandService;
    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }
    //get all active pages
    public function index()
    {

        try {
            $brands = $this->brandService->getPaginate();

            $resource =  $this->paginatedResponse($brands, BrandResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function show($id)
    {
        try {
            $Brand = $this->brandService->getData($id);
            if (!$Brand) {
                return $this->error('Brand not found', 404);
            }
            return $this->success(new BrandResource($Brand));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

}
