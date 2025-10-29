<?php

namespace App\Services;

use App\Models\User;
use App\Models\DeliveryMan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SupplierService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(User $model)
    {
        parent::__construct($model);
//        $this->fileUploadService = $fileUploadService;
    }
    public function createOrUpdate(Request|array $request, int $id = null): User
    {
        $data           = $request->all();
        // dd($data);
        $userRequest = $request->only(['first_name','email','phone','status','avatar','type']);
        $supplierRequest = $request->except(['first_name','email','phone','status','avatar','type','_token','_method']);
        // dd('data',$data, $userRequest,$supplierRequest);
        try {
            DB::beginTransaction();
            if ($id) {
                // Update
                $supplier           = $this->get($id);

                // Avatar
                if (isset($userRequest['avatar']) && $userRequest['avatar'] != null) {
                    $userRequest['avatar']    = $this->fileUploadService->uploadFile($request,'avatar',User::FILE_STORE_PATH,$supplier->avatar);
                }
                $userRequest['updated_by'] = Auth::id();
                $supplier->supplier()->update($supplierRequest);
                $supplier->update($userRequest);

                DB::commit();
                return $supplier;
            } else {
                // Create

                if (isset($userRequest['avatar']) && $userRequest['avatar'] != null) {
                    $userRequest['avatar']      = $this->fileUploadService->uploadFile($request,'avatar',User::FILE_STORE_PATH,);
                }
                $userRequest['created_by'] = Auth::id();
                $userRequest['password'] = Hash::make('12345678');
                // Store user
                $supplier                       = $this->model::create($userRequest);
                $supplier->supplier()->create($supplierRequest);
                // dd($supplier);
                DB::commit();
                return $supplier;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

}
