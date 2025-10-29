<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\BrandService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Models\ProductVariantSize;
use App\Services\AttributeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\RedirectResponse;
use App\DataTables\AttributeDataTable;
use App\Http\Requests\AttributeRequest;
use App\Services\AdministrationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class AttributeController extends Controller
{
    protected $attributeService;


    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;

    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Attribute'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Attribute'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Attribute'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Attribute'), only: ['destroy']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }


    public function index(AttributeDataTable $dataTable)
    {
        setPageMeta('Attribute List');

        return $dataTable->render('admin.attribute.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        checkPermission('Add Attribute');

        setPageMeta(__('Add Attribute'));

        return view('admin.attribute.create');
    }

    public function show($id){


    }

    public function store(AttributeRequest $request) : \Illuminate\Http\RedirectResponse
    {
        checkPermission('Add Attribute');
        $data = $request->validated();

        try {
            $this->attributeService->createOrUpdate($request);

            sendFlash('Successfully created', 'success');
            return redirect()->route('attributes.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        checkPermission('Edit Attribute');
        setPageMeta('Edit Attribute');
        $attribute = $this->attributeService->get($id)->load(['values']);

        return view('admin.attribute.edit',compact('attribute'));
    }

    public function update(AttributeRequest $request, $id): RedirectResponse
    {
        checkPermission('Edit Attribute');
        $data = $request->validated();
        ;
        try {
            $this->attributeService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('attributes.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        checkPermission('Delete Attribute');
        try {
            $this->attributeService->delete($id);
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }




}
