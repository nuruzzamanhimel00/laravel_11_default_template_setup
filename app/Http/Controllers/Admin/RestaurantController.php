<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\RestaurantService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\DataTables\RestaurantDataTable;
use App\Services\AdministrationService;
use App\Http\Requests\RestaurantRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class RestaurantController extends Controller
{
    protected $restaurantService;
    protected $roleService;

    public function __construct(RestaurantService $restaurantService,RoleService $roleService)
    {
        $this->restaurantService = $restaurantService;
        $this->roleService = $roleService;
    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Restaurant'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Restaurant'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Restaurant'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Restaurant'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('My Orders Restaurant'), only: ['restaurantOrders']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }



    public function index(RestaurantDataTable $dataTable)
    {
        setPageMeta('Restaurant List');

        setCreateRoute(route('restaurants.create'),'route');

        return $dataTable->render('admin.restaurant.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        checkPermission('Add Restaurant');

        setCreateRoute(null);
        setPageMeta(__('Add Restaurant'));
        return view('admin.restaurant.create');
    }

    public function store(RestaurantRequest $request) : \Illuminate\Http\RedirectResponse
    {
        checkPermission('Add Restaurant');
        $data = $request->validated();

        try {
            $this->restaurantService->createOrUpdate($request);

            sendFlash('Successfully created', 'success');
            return redirect()->route('restaurants.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        checkPermission('Edit Restaurant');
        setPageMeta('Edit Restaurant');
        setCreateRoute(null);

        $user = $this->restaurantService->get($id)->load(['restaurant']);

        return view('admin.restaurant.edit', compact('user'));
    }

    public function show($id) : \Illuminate\View\View
    {
        checkPermission('Show Restaurant');
        setPageMeta(' Restaurant Show');
        setCreateRoute(null);
        $user = $this->restaurantService->get($id)->load(['restaurant']);

        return view('admin.restaurant.show', compact('user'));
    }

    public function update(RestaurantRequest $request, $id): RedirectResponse
    {
        checkPermission('Edit Restaurant');
        $data = $request->validated();

        try {
            $this->restaurantService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('restaurants.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        checkPermission('Delete Restaurant');
        try {
            $this->restaurantService->delete($id);
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }


    public function statusUpdate(Request $request, $id){
        $user = User::find($id);
        $user->status = $request->status;
        if($user->save()){
            return response()->json(true);
        }else{

            return response()->json(true);
        }
    }

    public function restaurantOrders(User $user) : \Illuminate\View\View
    {

        setPageMeta('Show Restaurant Orders');
        setCreateRoute(null);
        // dd($user);
        $orders = Order::where('order_for_id', $user->id)
        ->with(['customer'])
        ->latest()
        ->paginate(10);

        return view('admin.restaurant.restaurant-orders', compact('user','orders'));
    }

}
