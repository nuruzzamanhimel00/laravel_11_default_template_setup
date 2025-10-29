<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;
use App\Http\Resources\Category\CategoryResource;

class CategoryController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    //get all active pages
    public function index()
    {
        try {

            $categories = $this->categoryService->getActive(['id','name','image','parent_id']);
            // dd($categories);
            $resource = $this->paginatedResponse($categories, CategoryResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function getParentCategories()
    {
        try {

            $categories = $this->categoryService->getActiveParentCategories(['id','name','image','parent_id']);
            // dd($categories);
            $resource = $this->paginatedResponse($categories, CategoryResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function show($slugOrId)
    {
        try {
            $category = $this->categoryService->getCategory($slugOrId);
            if (!$category) {
                return $this->error('category not found', 404);
            }
            return $this->success(new CategoryResource($category));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function getSubCategories($id)
    {
        try {

            $categories = $this->categoryService->getSubCategories(['id','name','image','parent_id'],$id);
            $resource = $this->paginatedResponse($categories, CategoryResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

}
