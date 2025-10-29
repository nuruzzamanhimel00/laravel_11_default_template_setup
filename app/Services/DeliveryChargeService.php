<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Models\DeliveryCharge;
use Illuminate\Support\Facades\DB;

class DeliveryChargeService extends BaseService
{
    public function __construct(DeliveryCharge $model)
    {
        parent::__construct($model);
    }

    // store location
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();

            $data['status'] = isset($request->status) && $request->status == STATUS_ACTIVE ? STATUS_ACTIVE : STATUS_INACTIVE;
            $location = $this->model::create($data);
            DB::commit();
            return $location;
        }catch(\Exception $e){
            logger($e->getMessage());
            DB::rollBack();
        }

    }

    // update
    public function update(Request $request, DeliveryCharge $deliveryCharge)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $data = $request->all();

            $data['status'] = isset($request->status) && $request->status == STATUS_ACTIVE ? STATUS_ACTIVE : STATUS_INACTIVE;
            $deliveryCharge->update($data);
            DB::commit();
            return $deliveryCharge;
        }catch(\Exception $e){
            logger($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    // location destroy
    public function destroy(DeliveryCharge $deliveryCharge)
    {
        try {
            DB::beginTransaction();
            $deliveryCharge->delete();
            DB::commit();
            return true;
        }catch(\Exception $e){
            logger($e->getMessage());
            DB::rollBack();
            return false;
        }
    }


    public function getPaginate()
    {
        return $this->model->active()
        ->select(['id', 'name', 'image', 'status'])
        ->latest()
        ->paginate(10);
    }
    public function getData($id)
    {
        try {
            return $this->model->active()
            ->select(['id', 'name', 'image', 'status'])
            ->where('id', $id)
            ->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }
    }

    public function getActive($selectedFields=[
            'id', 'title', 'cost', 'status',

    ]): mixed
    {
        // $request = request();

        // $perPage = $request->get('par_page', 20);


        return $this->model
            ->active()
            ->select($selectedFields)

            ->latest()
            ->get();
    }
    public function getDeliveryCharge($selectedFields=[
            'id', 'title', 'cost', 'status',

    ]): mixed
    {

        return $this->model
            ->active()
            ->select($selectedFields)
            ->latest()
            ->first();
    }

}
