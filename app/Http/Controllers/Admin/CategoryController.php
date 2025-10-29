<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Models\InvestorPayment;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\DataTables\CategoryDataTable;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CategoryRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class CategoryController extends Controller
{
    protected $categoryService;
    protected $roleService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Category'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Category'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Category'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Category'), only: ['destroy']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }


    public function index(CategoryDataTable $dataTable)
    {
        setPageMeta('Category List');

        setCreateRoute(route('categories.create'),'route');

        return $dataTable->render('admin.category.index');
    }




    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        checkPermission('Add Category');

        setPageMeta(__('Add Category'));
        setCreateRoute(null);
        $patent_categories = Category::whereNull('parent_id')->active()->latest()->get();

        return view('admin.category.create',compact('patent_categories'));
    }

    public function store(CategoryRequest $request) : \Illuminate\Http\RedirectResponse
    {
        checkPermission('Add Category');
        $data = $request->validated();

        try {
            $this->categoryService->createOrUpdate($request);

            sendFlash('Successfully created', 'success');
            return redirect()->route('categories.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        checkPermission('Edit Category');
        setPageMeta('Edit Category');
        setCreateRoute(null);

        $data = $this->categoryService->get($id);
        $patent_categories = Category::whereNull('parent_id')->active()->latest()->get();

        // dd($data);

        return view('admin.category.edit', compact('data','patent_categories'));
    }

    public function update(CategoryRequest $request, $id): RedirectResponse
    {
        checkPermission('Edit Category');
        $data = $request->validated();
        // dd(request()->all());
        try {
            $this->categoryService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('categories.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        checkPermission('Delete Category');
        try {
            $this->categoryService->delete($id);
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

    public function reorderSubcategories(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'category_id' => 'required|exists:categories,id',
        ]);

        foreach ($request->ids as $index => $id) {
            Category::where('id', $id)
                ->where('parent_id', $request->category_id)
                ->update(['position' => $index + 1]);
        }

        return response()->json(['message' => 'Sub-category order updated successfully']);
    }
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        foreach ($request->ids as $index => $id) {
            Category::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json(['message' => 'Parent categories reordered successfully.']);
    }




}
