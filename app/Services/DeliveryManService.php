<?php

namespace App\Services;

use App\Models\User;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeliveryManService extends BaseService
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
        try {
            DB::beginTransaction();
            if ($id) {
                // Update
                $user           = $this->get($id)->load(['delivery_man']);


                $delivery_men = $data['delivery_men'];
                if (isset($delivery_men['nid_front']) && $delivery_men['nid_front'] != null) {
                    $delivery_men['nid_front']      = $this->fileUploadService->uploadFile($request,'delivery_men.nid_front',DeliveryMan::FILE_STORE_PATH,$user->delivery_man->nid_front);

                }
                if (isset($delivery_men['nid_back']) && $delivery_men['nid_back'] != null) {
                    $delivery_men['nid_back']      = $this->fileUploadService->uploadFile($request,'delivery_men.nid_back',DeliveryMan::FILE_STORE_PATH,$user->delivery_man->nid_back);

                }


                $user->delivery_man()->update($delivery_men);

                // Password
                if (isset($data['password']) && $data['password']) {
                    $user->password = Hash::make($data['password']);
                }

                // Avatar
                if (isset($data['avatar']) && $data['avatar'] != null) {
                    $user->avatar = $this->fileUploadService->uploadFile($request,'avatar',User::FILE_STORE_PATH,$user->avatar);
                }

                $user->first_name       = $data['first_name'];
                $user->last_name        = $data['last_name'] ?? null;
                $user->email            = $data['email'];
                $user->phone            = $data['phone'];
                if(auth()->user()->id != $id){
                    $user->type         = $data['type'];
                }
                $user->status           = $data['status'];
                $user->updated_by       = Auth::id();
                $user->email_verified_at       = now();
                $user->username       = $request->username ?? null;

                // Update user
                $user->save();
                DB::commit();
                return $user;
            } else {
                // Create
                $data['password']        = Hash::make($data['password']);
                if (isset($data['avatar']) && $data['avatar'] != null) {
                    $data['avatar']      = $this->fileUploadService->uploadFile($request,'avatar',User::FILE_STORE_PATH,);
                }
                $data['created_by'] = Auth::id();
                $data['email_verified_at'] = now();
                // Store user
                $user                       = $this->model::create($data);

                $delivery_men = $data['delivery_men'];
                if (isset($delivery_men['nid_front']) && $delivery_men['nid_front'] != null) {
                    $delivery_men['nid_front']      = $this->fileUploadService->uploadFile($request,'delivery_men.nid_front',DeliveryMan::FILE_STORE_PATH,);
                }
                if (isset($delivery_men['nid_back']) && $delivery_men['nid_back'] != null) {
                    $delivery_men['nid_back']      = $this->fileUploadService->uploadFile($request,'delivery_men.nid_back',DeliveryMan::FILE_STORE_PATH,);
                }
                // dd('delivery_men',$delivery_men);

                $user->delivery_man()->create($delivery_men);
                DB::commit();
                return $user;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

}
