<?php

namespace App\Http\Controllers\Admin\Staffs;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Services\RolePermissionService;

class RoleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::whereNotIn('name', $this->systemRoleNames())->orderBy('id', 'DESC');
            return datatables($roles)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    if ($data->status == STATUS_ACTIVE) {
                        return '<div class="zBadge zBadge-complete">'.__("Active").'</div>';
                    } else {
                        return '<div class="zBadge zBadge-deactive">'.__("Deactivate").'</div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    return
                        '<div class="d-flex align-items-center g-10 justify-content-end">
                            <button onclick="getEditModal(\'' . route('admin.staffs.roles.edit', $data->id) . '\'' . ', \'#edit-modal\')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="Edit">
                                 ' . view('partials.icons.edit')->render() . '
                            </button>
                            <button onclick="deleteItem(\'' . route('admin.staffs.roles.delete', $data->id) . '\', \'roleDataTable\')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="Delete">
                                 ' . view('partials.icons.delete')->render() . '
                            </button>
                        </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $data['pageTitle'] = __('Roles');
        $data['showManageStaff'] = 'show';
        $data['activeRole'] = 'active';
        $service = app(RolePermissionService::class);
        $data['systemRoles'] = collect($service->systemRoles())->map(function ($definition) use ($service) {
            return [
                'key' => $definition['key'],
                'label' => $definition['label'],
                'role' => Role::where('name', $service->systemRoleName($definition['key']))->where('guard_name', 'web')->first(),
                'locked' => false,
                'edit_url' => route('admin.staffs.roles.system.edit', $definition['key']),
            ];
        })->prepend(['key' => 'admin', 'label' => 'Admin', 'role' => null, 'locked' => true])->values();
        return view('admin.staffs.roles.index', $data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:roles,name', 'not_regex:/^__system_/'],
            'status' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();
            $role = new Role();
            $role->name = $request->name;
            $role->guard_name = 'web';
            $role->status = $request->status;
            $role->save();

            DB::commit();
            $message = __('Role created. Open its permissions to configure module access.');
            return $this->success(['role' => ['id' => $role->id, 'name' => $role->name]], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['role'] = Role::findOrFail($id);
        abort_if(str_starts_with($data['role']->name, '__system_'), 404);
        $data['permissionGroups'] = app(RolePermissionService::class)->groupedPermissions();
        $data['oldPermissions'] = $data['role']->permissions->pluck('name')->toArray();
        return view('admin.staffs.roles.edit')->with($data);
    }

    public function editSystem(string $key)
    {
        abort_if($key === 'admin', 404);
        $definition = collect(app(RolePermissionService::class)->systemRoles())->firstWhere('key', $key);
        abort_unless($definition, 404);

        $data['role'] = Role::where('name', app(RolePermissionService::class)->systemRoleName($key))
            ->where('guard_name', 'web')->firstOrFail();
        $data['roleLabel'] = $definition['label'];
        $data['permissionGroups'] = app(RolePermissionService::class)->groupedPermissions();
        $data['oldPermissions'] = $data['role']->permissions->pluck('name')->toArray();
        return view('admin.staffs.roles.system-edit', $data);
    }

    public function updateSystem(Request $request, string $key)
    {
        abort_if($key === 'admin', 404);
        $definition = collect(app(RolePermissionService::class)->systemRoles())->firstWhere('key', $key);
        abort_unless($definition, 404);

        $allowedPermissions = collect(app(RolePermissionService::class)->permissions())->pluck('name')->all();
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => ['string', Rule::in($allowedPermissions)],
        ]);

        $role = Role::where('name', app(RolePermissionService::class)->systemRoleName($key))
            ->where('guard_name', 'web')->firstOrFail();
        $role->syncPermissions($request->input('permissions', []));
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        RolePermissionService::clearSystemRoleCache();

        return redirect()->route('admin.staffs.roles.index')->with('success', __('Permissions updated successfully.'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:roles,name,' . $id, 'not_regex:/^__system_/'],
            'status' => 'required|boolean',
            'permissions' => 'array',
            'permissions.*' => ['string', Rule::in(collect(app(RolePermissionService::class)->permissions())->pluck('name')->all())],
        ]);

        try {
            DB::beginTransaction();
            $role = Role::findOrFail($id);
            abort_if(str_starts_with($role->name, '__system_'), 404);

            $role->name = $request->name;
            $role->status = $request->status;
            $role->save();

            // An unchecked permission must be revocable, including clearing all grants.
            $role->syncPermissions($request->input('permissions', []));
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();
            $message = __(UPDATED_SUCCESSFULLY);
            return $this->success([], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $role = Role::findOrFail($id);
            if (str_starts_with($role->name, '__system_')) {
                DB::rollBack();
                return response()->json(['status' => false, 'data' => [], 'message' => __('System roles cannot be deleted.')], 422);
            }
            if ($role->users()->exists()) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => __('This role is assigned to users. Reassign them before deleting it.'),
                ], 422);
            }
            $role->delete();

            DB::commit();
            $message = __(DELETED_SUCCESSFULLY);
            return $this->success([], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }

    private function systemRoleNames(): array
    {
        $service = app(RolePermissionService::class);
        return collect($service->systemRoles())
            ->map(fn ($definition) => $service->systemRoleName($definition['key']))
            ->all();
    }
}
