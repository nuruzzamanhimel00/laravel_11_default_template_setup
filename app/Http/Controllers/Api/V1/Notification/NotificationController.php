<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Traits\ApiResponse;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Notification\NotificationResource;

class NotificationController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;
    // public $categoryService;
    // public function __construct(CategoryService $categoryService)
    // {
    //     $this->categoryService = $categoryService;
    // }
    //get all active pages
    public function index()
    {
        try {
            $notifications = Notification::getPaginateData();
            $resource =  $this->paginatedResponse($notifications, NotificationResource::class);
            return $this->success($resource);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return $this->error($e->getMessage());
        }
    }
    public function read($id)
    {
        $notification = Notification::get($id);

        $notification->markAsRead();

        return $this->success(new NotificationResource($notification));
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(true);
    }

}
