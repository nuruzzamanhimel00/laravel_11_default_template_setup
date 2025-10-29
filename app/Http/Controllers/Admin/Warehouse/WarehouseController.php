<?php

namespace App\Http\Controllers\Admin\Warehouse;

use Illuminate\Http\Request;
use App\Services\Warehouse\WarehouseService;
use App\Http\Requests\Warehouse\WarehouseRequest;
use App\Http\Controllers\Controller;
use App\DataTables\WarehouseDataTable;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Warehouse'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Warehouse'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Warehouse'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Warehouse'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Restore Warehouse'), only: ['restore']),
        ];
    }

    public function index(WarehouseDataTable $dataTable)
    {
        setPageMeta('Warehouse List');
        // setCreateRoute(route('warehouses.create'),'route');
        setCreateRoute(null);

        return $dataTable->render('common.datatable');
    }
    public function create()
    {
        setPageMeta('Warehouse create');
        setCreateRoute(null);
        return view('admin.warehouse.create');
    }

    public function store(WarehouseRequest $request)
    {
        try {
            $warehouse = $this->warehouseService->createWarehouse($request);
            return redirect(route('warehouses.index'))->with('success', 'Warehouse created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        setPageMeta('Warehouse edit');
        setCreateRoute(null);
        try {
            $warehouse = $this->warehouseService->getWarehouseById($id);
            return view('admin.warehouse.edit', compact('warehouse'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function show($id)
    {
        setPageMeta('Warehouse show');
        setCreateRoute(null);
        try {
            $warehouse = $this->warehouseService->getWarehouseById($id);
            return view('admin.warehouse.show', compact('warehouse'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(WarehouseRequest $request, $id)
    {
        try {
            $warehouse = $this->warehouseService->updateWarehouse($id, $request);
            return redirect(route('warehouses.index'))->with('success', 'Warehouse updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->warehouseService->deleteWarehouse($id);
            return back()->with('success', 'Warehouse deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
