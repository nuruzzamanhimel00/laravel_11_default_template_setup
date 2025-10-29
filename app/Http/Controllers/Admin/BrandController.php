<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Middleware\PermissionMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Models\Brand;
use App\Http\Controllers\Controller;
use App\DataTables\BrandDataTable;
use App\Http\Requests\BrandRequest;

class BrandController extends Controller
{
    protected $brand_service;
    public function __construct(BrandService $brand_service)
    {
        $this->brand_service   = $brand_service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Brand'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Brand'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Brand'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Brand'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Restore Brand'), only: ['restore']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(BrandDataTable $dataTable)
    {
        setPageMeta('Brand List');

        setCreateRoute(route('brands.create'),'route');

        return $dataTable->render('admin.brand.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        setPageMeta('Create Brand');
        setCreateRoute(null);
         return view('admin.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        $request->validated();
        try {
            $res = $this->brand_service->store($request);
            return redirect()->route('brands.index')->with('success', 'Created successfully');
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
         setPageMeta('Edit Brand');
         setCreateRoute(null);

         return view('admin.brand.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        $request->validated();
        try {
            $res = $this->brand_service->update($request, $brand);
            return redirect()->route('brands.index')->with('success', 'Updated successfully');
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            $res = $this->brand_service->destroy($brand);
            return redirect()->route('brands.index')->with('success', 'Deleted successfully');
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }
}
