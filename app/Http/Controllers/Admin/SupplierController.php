<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\SupplierDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Services\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;



class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;

    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Supplier'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Supplier'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Supplier'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Supplier'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Restore Supplier'), only: ['restore']),
        ];
    }


    public function index(SupplierDataTable $dataTable)
    {
        setPageMeta('Supplier List');

        setCreateRoute(route('suppliers.create'),'route');

        return $dataTable->render('admin.supplier.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        setPageMeta(__('Add Supplier'));
        setCreateRoute(null);
        return view('admin.supplier.create');
    }

    public function store(SupplierRequest $request) : \Illuminate\Http\RedirectResponse
    {
        try {
            $this->supplierService->createOrUpdate($request);

            sendFlash('Successfully created Supplier', 'success');
            return redirect()->route('suppliers.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        setPageMeta('Edit Supplier');
        setCreateRoute(null);
        $user = $this->supplierService->get($id)->load(['supplier']);
        // dd($user);
        return view('admin.supplier.edit', compact('user'));
    }
    public function show($id) : \Illuminate\View\View
    {
        setPageMeta('Show Supplier');
        setCreateRoute(null);
        $user = $this->supplierService->get($id)->load(['supplier']);
        // dd($user);
        return view('admin.supplier.show', compact('user'));
    }

    public function update(SupplierRequest $request, $id): RedirectResponse
    {
        try {
            $this->supplierService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('suppliers.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        try {
            $this->supplierService->deleteForceDeleteModel($id);

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
                    $this->supplierService->deleteForceDeleteModel($userId);
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
            $this->supplierService->restore($id);
            sendFlash(__('Successfully Restored'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

}
