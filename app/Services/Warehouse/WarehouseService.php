<?php
namespace App\Services\Warehouse;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\Warehouse;

class WarehouseService extends BaseService
{
    public function __construct(Warehouse $model)
    {
        parent::__construct($model);
    }

    public function getAllWarehouses($status=null, $paginate=20)
    {
        $warehouses = Warehouse::getFilteredData(20);
        return $warehouses;
    }

    public function getWarehouseById($id)
    {
        try {
            return $this->model::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Warehouse retrieval failed: ' . $e->getMessage());
            throw new \Exception("Warehouse not found.", 404);
        }
    }

    public function createWarehouse($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if($request->hasFile('image')){
                $image = $this->fileUploadService->uploadFile($request,'image',$this->model::FILE_STORE_PATH);
                $data['image'] = $image;
            }
            if(isset($request->full_phone)){
                $data['phone'] = $request->full_phone;
            }
            $data['status'] = isset($request->status) ? STATUS_ACTIVE : STATUS_INACTIVE;
            $data['is_default'] = isset($request->is_default) ? true : false;
            $warehouse= $this->model::updateOrCreate(['name' => $data['name']], $data);
            DB::commit();
            return $warehouse;
        } catch (\Exception $e) {
            Log::error('Warehouse creation failed: ' . $e->getMessage());
            DB::rollBack();
            throw new \Exception("Failed to create warehouse.", 500);
        }
    }

    public function updateWarehouse($id, $request)
    {
        DB::beginTransaction();
        $data = $request->validated();
        try {
            $warehouse = $this->model::findOrFail($id);
            if($request->hasFile('image')){
                $image = $this->fileUploadService->uploadFile($request,'image',$this->model::FILE_STORE_PATH, $warehouse->image);
                $data['image'] = $image;
            }else{
                $data['image'] = $warehouse->image;
            }
            if(isset($request->full_phone)){
                $data['phone'] = $request->full_phone;
            }
            $warehouse = $warehouse->update($data);
            DB::commit();
            return $warehouse;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Warehouse update failed: ' . $e->getMessage());
            throw new \Exception("Failed to update warehouse.", 500);
        }
    }

    public function deleteWarehouse($id)
    {
        try {
            $this->model::findOrFail($id)->delete();
        } catch (\Exception $e) {
            Log::error('Warehouse deletion failed: ' . $e->getMessage());
            throw new \Exception("Failed to delete warehouse.", 500);
        }
    }
}
