<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Services\AgentService;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\RestaurantService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\DataTables\AgentDataTable;
use App\Services\AdministrationService;
use App\Http\Requests\AgentRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class AgentController extends Controller
{
    protected $agentService;
    protected $roleService;

    public function __construct(AgentService $agentService, RoleService $roleService)
    {
        $this->agentService = $agentService;
        $this->roleService = $roleService;
    }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Agent'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Agent'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Agent'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Agent'), only: ['destroy']),

        ];
    }



    public function index(AgentDataTable $dataTable)
    {
        setPageMeta('Agent List');

        setCreateRoute(route('agents.create'),'route');

        return $dataTable->render('admin.agent.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        checkPermission('Add Agent');

        setCreateRoute(null);
        setPageMeta(__('Add Agent'));
        return view('admin.agent.create');
    }

    public function store(AgentRequest $request) : \Illuminate\Http\RedirectResponse
    {
        checkPermission('Add Agent');
        $data = $request->validated();

        try {
            $this->agentService->createOrUpdate($request);

            sendFlash('Successfully created', 'success');
            return redirect()->route('agents.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        checkPermission('Edit Agent');
        setPageMeta('Edit Agent');
        setCreateRoute(null);

        $user = $this->agentService->get($id);

        return view('admin.agent.edit', compact('user'));
    }

    public function show($id) : \Illuminate\View\View
    {
        checkPermission('Show Agent');
        setPageMeta(' Agent Show');
        setCreateRoute(null);
        $user = $this->agentService->get($id);

        return view('admin.agent.show', compact('user'));
    }

    public function update(AgentRequest $request, $id): RedirectResponse
    {
        checkPermission('Edit Agent');
        $data = $request->validated();

        try {
            $this->agentService->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('agents.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        checkPermission('Delete Restaurant');
        try {
            $this->agentService->delete($id);
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }


    public function statusUpdate(Request $request, $id){
        $user = User::find($id);
        $user->status = $request->status;
        if($user->save()){
            return response()->json(true);
        }else{

            return response()->json(true);
        }
    }



}
