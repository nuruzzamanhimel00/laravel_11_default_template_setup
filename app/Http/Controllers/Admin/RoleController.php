<?php

namespace App\Http\Controllers\Admin;

use App\Services\RoleService;
use App\DataTables\RoleDataTable;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class RoleController extends Controller
{
    protected $role_service;
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Role'), only: ['index']),
            new Middleware(PermissionMiddleware::using('Add Role'), only: ['create', 'store']),
            new Middleware(PermissionMiddleware::using('Edit Role'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('Delete Role'), only: ['destroy']),
            // new Middleware(PermissionMiddleware::using('Restore Administration'), only: ['restore']),
        ];
    }
    public function __construct(RoleService $role_service)
    {
        $this->role_service = $role_service;
    }

    public function index(RoleDataTable $dataTable)
    {
        setPageMeta('Roles');

        setCreateRoute(route('roles.create'),'route');

        return $dataTable->render('admin.roles.index');
    }

    public function create()
    {
        checkPermission('Add Role');
        setPageMeta('Create Role');
        setCreateRoute(null);

        $permissions = $this->role_service->getPermissions();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        checkPermission('Add Role');
        $data = $request->validated();

        try {
            $this->role_service->updateOrCreate($request->all());

            sendFlash('Successful created.');
            return redirect()->route('roles.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function edit($id)
    {
        checkPermission('Edit Role');
        setPageMeta('Edit Role');
        setCreateRoute(null);

        $permissions = $this->role_service->getPermissions();
        // dd($permissions->childs);
        $role            = Role::where('id', $id)->with('permissions')->first();
        $parents_id      = [];
        $role_permission = [];
        $all_permission_id = [];
        foreach ($role->permissions as $value) {
            array_push($role_permission, $value->id);
            array_push($parents_id, $value->parent_id);
        }
        foreach($permissions as $permission){
            foreach($permission->childs as $child){
                array_push($all_permission_id, $child->id);
            }
        }
        $parents_id = array_unique($parents_id);

        // dd($role_permission, $parents_id, $all_permission_id);
        return view('admin.roles.edit', compact('parents_id', 'role', 'permissions', 'role_permission','all_permission_id'));
    }

    public function update(RoleRequest $request, $id)
    {
        checkPermission('Edit Role');
        $data = $request->validated();
        try {
            $this->role_service->updateOrCreate($request->all(), $id);

            sendFlash('Successful updated.');
            return redirect()->route('roles.index');
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }

    public function destroy($id)
    {
        checkPermission('Delete Role');

        try {
            $this->role_service->delete($id);

            sendFlash('Successful deleted.');
            return back();
        } catch (\Exception $e) {
            sendFlash($e->getMessage(), 'error');
            return back();
        }
    }
}
