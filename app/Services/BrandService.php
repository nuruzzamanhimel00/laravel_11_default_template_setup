<?php

namespace App\Services;

use App\Models\Brand;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandService extends BaseService
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    // store location
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            if($request->hasFile('image')){
                $image = $this->fileUploadService->uploadFile($request,'image',$this->model::FILE_STORE_PATH);
                $data['image'] = $image;
            }
            // $data['status'] = isset($request->status) ? STATUS_ACTIVE : STATUS_INACTIVE;
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
    public function update(Request $request, Brand $location)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $data = $request->all();
            if($request->hasFile('image')){
                $image = $this->fileUploadService->uploadFile($request,'image',$this->model::FILE_STORE_PATH, $location->image);
                $data['image'] = $image;
            }
            $data['status'] = isset($request->status) && $request->status == STATUS_ACTIVE ? STATUS_ACTIVE : STATUS_INACTIVE;
            $location->update($data);
            DB::commit();
            return $location;
        }catch(\Exception $e){
            logger($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    // location destroy
    public function destroy(Brand $location)
    {
        try {
            DB::beginTransaction();
            $location->delete();
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
        $request = request();
        $perPage = $request->get('par_page', 20);
        // $availableFor = $request->get('available_for'); // 'Restaurant' or 'Customer'
        $availableFor = auth('api')->check() ? auth('api')->user()->type: null;

        return $this->model->active()
        ->select(['id', 'name', 'image', 'status'])
        ->latest()
        // ->when($availableFor, function ($query) {
        //     $query->whereHas('products', function ($query) {
        //         $query->availableFor();
        //     });
        // })
        ->paginate($perPage);
    }
    public function getData($id)
    {
        $request = request();
        // $availableFor = $request->get('available_for'); // 'Restaurant' or 'Customer'
        $availableFor = auth('api')->check() ? auth('api')->user()->type: null;

        try {
            return $this->model->active()
            ->select(['id', 'name', 'image', 'status'])
            ->where('id', $id)
            // ->when($availableFor, function ($query) {
            //     $query->whereHas('products', function ($query) {
            //         $query->availableFor();
            //     });
            // })
            ->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }
    }

}
