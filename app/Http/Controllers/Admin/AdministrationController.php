<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AdministrationDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdministrationRequest;
use App\Models\User;
use App\Services\RoleService;
use App\Services\AdministrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;



class AdministrationController extends Controller
{
    protected $administrationServices;
    protected $roleService;

    public function __construct(AdministrationService $administrationServices,RoleService $roleService)
    {
        $this->administrationServices = $administrationServices;
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
            new Middleware(PermissionMiddleware::using('List Admin'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Admin'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Admin'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Admin'), only: ['destroy']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }


    public function index(AdministrationDataTable $dataTable)
    {
        setPageMeta('System User List');

        setCreateRoute(route('administrations.create'),'route');

        return $dataTable->render('admin.administrations.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(): \Illuminate\View\View
    {
        checkPermission('Add Admin');
        $roles = $this->roleService->get();

        setPageMeta(__(' System User Create'));
        setCreateRoute(null);

        return view('admin.administrations.create', compact('roles'));
    }

    public function store(AdministrationRequest $request) : \Illuminate\Http\RedirectResponse
    {
        checkPermission('Add Admin');
        $data = $request->validated();

        try {
            $this->administrationServices->createOrUpdate($request);

            sendFlash('Successfully created Admin', 'success');
            return redirect()->route('administrations.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id) : \Illuminate\View\View
    {
        checkPermission('Edit Admin');
        setPageMeta(' System User Edit');
        setCreateRoute(null);
        $roles = $this->roleService->get();
        $user = $this->administrationServices->get($id);

        return view('admin.administrations.edit', compact('user','roles'));
    }

    public function show($id) : \Illuminate\View\View
    {
        checkPermission('Show Admin');
        setPageMeta(' System User Show');
        setCreateRoute(null);
        $roles = $this->roleService->get();
        $user = $this->administrationServices->get($id);

        return view('admin.administrations.show', compact('user','roles'));
    }

    public function update(AdministrationRequest $request, $id): RedirectResponse
    {
        checkPermission('Edit Admin');
        $data = $request->validated();

        try {
            $this->administrationServices->createOrUpdate($request, $id);

            sendFlash('Successfully Updated');
            return redirect()->route('administrations.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id) : RedirectResponse
    {
        checkPermission('Delete Admin');
        try {
            $this->administrationServices->delete($id);
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }
    public function bulk_destroy(Request $request) : RedirectResponse
    {
        try {
            $userIds = explode(",", $request->id);
            if (count($userIds) > 0) {
                foreach ($userIds as $key => $userId) {
                    $this->administrationServices->deleteForceDeleteModel($userId);
                }

            }
            sendFlash(__('Successfully Deleted'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }
    public function restore($id) : RedirectResponse
    {
        try {
            $this->administrationServices->restore($id);
            sendFlash(__('Successfully Restored'));
            return back();
        } catch (\Exception $e) {
            sendFlash(__($e->getMessage()), 'error');
            return back();
        }
    }

}
