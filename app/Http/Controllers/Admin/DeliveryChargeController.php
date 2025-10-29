<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\DeliveryCharge;
use App\Http\Controllers\Controller;
use App\Services\DeliveryChargeService;
use App\DataTables\DeliveryChargeDataTable;
use App\Http\Requests\DeliveryChargeRequest;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class DeliveryChargeController extends Controller
{
    protected $deliveryChargeService;
    public function __construct(DeliveryChargeService $deliveryChargeService)
    {
        $this->deliveryChargeService   = $deliveryChargeService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Delivery Charge'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Delivery Charge'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Delivery Charge'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Delivery Charge'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('Restore Delivery Charge'), only: ['restore']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(DeliveryChargeDataTable $dataTable)
    {
        setPageMeta('Delivery Charge List');
        setCreateRoute(null);
        // setCreateRoute(route('delivery-charges.create'),'route');

        return $dataTable->render('admin.delivery_charge.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        setPageMeta('Create Delivery Charge');
        setCreateRoute(null);
         return view('admin.delivery_charge.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeliveryChargeRequest $request)
    {
        $request->validated();
        // dd('dd');
        try {
            $res = $this->deliveryChargeService->store($request);
            return redirect()->route('delivery-charges.index')->with('success', 'Created successfully');
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($delivery_charge)
    {
        $data = $this->deliveryChargeService->get($delivery_charge);
        // dd($data);
         setPageMeta('Edit Delivery Charge ');
         setCreateRoute(null);

         return view('admin.delivery_charge.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeliveryChargeRequest $request, DeliveryCharge $deliveryCharge)
    {
        $request->validated();

        try {
            $res = $this->deliveryChargeService->update($request, $deliveryCharge);
            return redirect()->route('delivery-charges.index')->with('success', 'Updated successfully');
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryCharge $deliveryCharge)
    {
        try {
            $res = $this->deliveryChargeService->destroy($deliveryCharge);
            return redirect()->route('delivery-charges.index')->with('success', 'Deleted successfully');
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }
}
