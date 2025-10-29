<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\DeliveryManDataTable;
use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryManRequest;
use App\Models\User;
use App\Services\DeliveryManService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;



class DeliveryManController extends Controller
{
    protected $deliveryManService;

    public function __construct(DeliveryManService $deliveryManService)
    {
        $this->deliveryManService = $deliveryManService;

    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Delivery Man'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Delivery Man'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Delivery Man'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Delivery Man'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Restore Delivery Man'), only: ['restore']),
        ];
    }


    public function index(DeliveryManDataTable $dataTable)
    {
        setPageMeta('Delivery Man List');
        setCreateRoute(route('delivery-mans.create'),'route');

        return $dataTable->render('admin.delivery_man.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        setPageMeta(__('Add Delivery Man'));
        setCreateRoute(null);
        return view('admin.delivery_man.create');
    }

    public function store(DeliveryManRequest $request) : \Illuminate\Http\RedirectResponse
    {
        try {
            $this->deliveryManService->createOrUpdate($request);

            sendFlash('Successfully created Delivery Man', 'success');
            return redirect()->route('delivery-mans.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        setPageMeta('Edit Delivery Man');
        setCreateRoute(null);
        $user = $this->deliveryManService->get($id)->load(['delivery_man']);
        // dd($user);
        return view('admin.delivery_man.edit', compact('user'));
    }
    public function show($id) : \Illuminate\View\View
    {
        setPageMeta('Show Delivery Man');
        setCreateRoute(null);
        $user = $this->deliveryManService->get($id)->load(['delivery_man']);
        // dd($user);
        return view('admin.delivery_man.show', compact('user'));
    }

    public function update(DeliveryManRequest $request, $id): RedirectResponse
    {
        try {
            $this->deliveryManService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('delivery-mans.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        try {
            $this->deliveryManService->deleteForceDeleteModel($id);

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
                    $this->deliveryManService->deleteForceDeleteModel($userId);
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
            $this->deliveryManService->restore($id);
            sendFlash(__('Successfully Restored'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

}
