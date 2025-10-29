<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Models\User;
use App\Models\Order;
use App\Traits\ApiResponse;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Traits\PaginatedResourceTrait;
use App\Http\Requests\Api\V1\UserRequest;
use App\Http\Resources\User\UserResource;

class ProfileController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    public $userService;
    public $fileUploadService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->fileUploadService = app(FileUploadService::class);
    }
    public function show()
    {
        try {
            $user = $this->userService->getUser(auth()->user()->id);

            // dd($total_order_amount, $total_paid_amount, $total_due_amount);
            if (!$user) {
                return $this->error('User not found', 404);
            }
            return $this->success(new UserResource($user));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    public function update(UserRequest $request)
    {
        $user = auth()->user();

        try {
            // Prepare user data, excluding sensitive or irrelevant fields
            $userData = $request->except(['_method', 'manager_phone', 'password_confirmation', 'email']);

            // Handle avatar upload
            if (!empty($userData['avatar'])) {
                $userData['avatar'] = $this->fileUploadService->uploadFile(
                    $request,
                    'avatar',
                    User::FILE_STORE_PATH,
                    $user->avatar
                );
            }

            // Update related restaurant's manager_phone if applicable
            if ($request->has('manager_phone') && $user->type === User::TYPE_RESTAURANT) {
                $user->restaurant()->update([
                    'manager_phone' => $request->manager_phone
                ]);
            }

            // Update user
            $user->update($userData);

            // Reload user with restaurant relation
            $user->load('restaurant');

            return $this->success(new UserResource($user));
        } catch (\Exception $e) {
            logger()->error('User update failed: ' . $e->getMessage());
            return $this->error('Failed to update user.');
        }
    }

}
