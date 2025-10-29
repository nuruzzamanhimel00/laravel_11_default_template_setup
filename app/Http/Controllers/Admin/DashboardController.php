<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\UserService;
use App\Services\RoleService;
use App\Services\DashboardService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $userService;
    protected $RoleService;
    protected $dashboardService;

    public function __construct(
        UserService $userService,
        RoleService $RoleService,
        DashboardService $dashboardService
    ){
        $this->userService      = $userService;
        $this->RoleService      = $RoleService;
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        setPageMeta('Dashboard');
        setCreateRoute(null);
        // $dash_data = $this->dashboardService->getDashboardData($request);
        // dd('dd');
        return view('admin.dashboard');
    }
}
