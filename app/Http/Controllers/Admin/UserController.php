<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Services\UserService;
use App\DataTables\UserDataTable;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class UserController extends Controller
{
    protected $userServices;

    public function __construct(UserService $userServices)
    {
        $this->userServices = $userServices;

    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List User'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add User'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit User'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete User'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Restore User'), only: ['restore']),
            new Middleware(PermissionMiddleware::using('My Orders User'), only: ['userOrders']),
        ];
    }


    public function index(UserDataTable $dataTable)
    {
        setPageMeta('User List');

        setCreateRoute(route('users.create'),'route');

        return $dataTable->render('admin.users.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        setPageMeta(__('Add User'));
        setCreateRoute(null);
        return view('admin.users.create');
    }

    public function store(UserRequest $request) : \Illuminate\Http\RedirectResponse
    {
        try {
            $this->userServices->createOrUpdate($request);

            sendFlash('Successfully created user', 'success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit(User $user) : \Illuminate\View\View
    {
        setPageMeta('Edit User');
        setCreateRoute(null);

        return view('admin.users.edit', compact('user'));
    }
    public function show(User $user) : \Illuminate\View\View
    {
        setPageMeta('Show User');
        setCreateRoute(null);
        $user = $user->load(['orders:id,total,total_paid,order_status,delivery_status,payment_status,order_for_id']);

        return view('admin.users.show', compact('user'));
    }


    public function update(UserRequest $request, $id): RedirectResponse
    {
        try {
            $this->userServices->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        try {
            $this->userServices->deleteForceDeleteModel($id);

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
                    $this->userServices->deleteForceDeleteModel($userId);
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
            $this->userServices->restore($id);
            sendFlash(__('Successfully Restored'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

    public function userOrders(User $user) : \Illuminate\View\View
    {
        $orders = Order::where('order_for_id', $user->id)
        ->with(['customer'])
        ->latest()
        ->paginate(10);

        setPageMeta('Show User Orders');
        setCreateRoute(null);
        // dd($orders);

        return view('admin.users.user-orders', compact('user','orders'));
    }

}
