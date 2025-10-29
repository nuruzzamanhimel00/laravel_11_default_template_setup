<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
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
        if ($id) {
            // Update
            $user           = $this->get($id);

            // Password
            if (isset($data['password']) && $data['password']) {
                $user->password = Hash::make($data['password']);
            }

            // Avatar
            if (isset($data['avatar']) && $data['avatar'] != null) {
                $user->avatar = $this->fileUploadService->uploadFile($request,'avatar',User::FILE_STORE_PATH,$user->avatar);
            }

            $user->first_name       = $data['first_name']??null;
            $user->last_name        = $data['last_name']??null;
            if(isset($data['email'])){
                $user->email            = $data['email']??null;
            }
            $user->phone            = $data['phone']??null;
            if(auth()->user()->id != $id && isset( $data['type']) && $data['type']){
                $user->type         = $data['type'];
            }
            if(isset($data['status'])){
                $user->status           = $data['status'];
            }
            $user->updated_by       = Auth::id();
            $user->username       = $request->username ?? null;
            // Update user
            $user->save();

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
            // Give user role
            return $user;
        }
    }

    public function getUser($userId)
    {
        try {
            return $this->model->active()
            ->select(['id', 'first_name', 'last_name', 'email', 'phone', 'type', 'avatar'])
            ->where('id', $userId)
            ->when(auth()->user()->type == User::TYPE_RESTAURANT, function ($query) {
                $query->with(['restaurant:id,user_id,address,manager_phone']);
            })
            ->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }
    }

}
