<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgentService extends BaseService
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

            $user->first_name       = $data['first_name'];
            $user->last_name        = $data['last_name'] ?? '';
            $user->email            = $data['email'];
            $user->phone            = $data['phone'];
            $user->username            = $data['username'] ?? null;
            if(auth()->user()->id != $id){
                $user->type         = $data['type'];
            }
            $user->status           = $data['status'];
            $user->email_verified_at           = now();
            $user->updated_by       = Auth::id();

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
            // dd($data);
            // Store user
            $user                       = $this->model::create($data);

            return $user;
        }
    }

    public function delete($id){
        $user = $this->get($id);
        $avatar = $user->avatar;
        $this->deleteForceDeleteModel($id);
        if(!is_null($avatar)){

            $this->fileUploadService->delete($avatar);
        }
    }

}
