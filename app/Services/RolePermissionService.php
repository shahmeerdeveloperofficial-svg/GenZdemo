<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    private static array $systemRoleCache = [];

    public static function clearSystemRoleCache(): void
    {
        self::$systemRoleCache = [];
    }

    public function modules(): array
    {
        return config('role_permissions.modules', []);
    }

    public function actions(): array
    {
        return config('role_permissions.actions', []);
    }

    public function permissions(): array
    {
        $permissions = [];
        foreach ($this->modules() as $module => $details) {
            foreach ($this->actions() as $action => $label) {
                $permissions[] = [
                    'name' => $module . '.' . $action,
                    'guard_name' => 'web',
                    'module' => $module,
                    'module_label' => $details['label'],
                    'group' => $details['group'],
                    'action' => $action,
                    'action_label' => $label,
                ];
            }
        }

        return $permissions;
    }

    public function groupedPermissions(): array
    {
        $groups = [];
        foreach ($this->modules() as $module => $details) {
            $groups[$details['group']][$module] = [
                'label' => $details['label'],
                'permissions' => collect($this->actions())
                    ->mapWithKeys(fn ($label, $action) => [$action => $module . '.' . $action])
                    ->all(),
            ];
        }

        return $groups;
    }

    public function systemRoleName(string $key): string
    {
        return '__system_' . $key;
    }

    public function systemRoles(): array
    {
        return config('role_permissions.system_roles', []);
    }

    public function systemRoleFor(User $user): ?Role
    {
        $definition = $this->systemRoles()[(int) $user->role] ?? null;
        if (!$definition) {
            return null;
        }

        $name = $this->systemRoleName($definition['key']);
        if (!array_key_exists($name, self::$systemRoleCache)) {
            self::$systemRoleCache[$name] = Role::query()
            ->where('guard_name', 'web')
            ->where('name', $name)
            ->first();
        }

        return self::$systemRoleCache[$name];
    }

    public function allows(User $user, string $permission): bool
    {
        if ((int) $user->role === USER_ROLE_ADMIN) {
            return true;
        }

        // All authenticated back-office roles can reach their dashboard; the
        // matrix controls access to the operational modules shown from there.
        if ($permission === 'dashboard.view') {
            return true;
        }

        // A staff member with a named custom role uses that role's matrix as
        // their staff access profile. Other account types keep their own base profile.
        if ((int) $user->role === USER_ROLE_STAFF) {
            $customRoles = $user->roles
                ->reject(fn (Role $role) => str_starts_with($role->name, '__system_'));

            if ($customRoles->isNotEmpty()) {
                return $customRoles->contains(fn (Role $role) => $this->roleHasPermission($role, $permission));
            }
        }

        $systemRole = $this->systemRoleFor($user);
        if ($systemRole && $this->roleHasPermission($systemRole, $permission)) {
            return true;
        }

        // Assigned custom roles can extend the role profile for supported accounts.
        return $user->roles
            ->reject(fn (Role $role) => str_starts_with($role->name, '__system_'))
            ->contains(fn (Role $role) => $this->roleHasPermission($role, $permission));
    }

    private function roleHasPermission(Role $role, string $permission): bool
    {
        if (isset($role->status) && (int) $role->status !== STATUS_ACTIVE) {
            return false;
        }

        $role->loadMissing('permissions');

        return $role->permissions->contains('name', $permission);
    }

    public function moduleForRoute(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        foreach (config('role_permissions.route_modules', []) as $prefix => $module) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix . '.')) {
                return $module;
            }
        }

        return null;
    }

    public function actionForRequest(Request $request): string
    {
        $routeName = strtolower((string) $request->route()?->getName());
        $path = strtolower($request->path());
        $route = $routeName . ' ' . $path;
        $method = strtoupper($request->method());

        if (preg_match('/(^|[.\/_-])(delete|destroy|remove|revoke|cancel)([.\/_-]|$)/', $route)) {
            return 'delete';
        }

        if (preg_match('/(^|[.\/_-])(create|store|add|import|save-file|note\.store)([.\/_-]|$)/', $route)
            && in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            return 'add';
        }

        if ($method === 'POST') {
            return 'edit';
        }

        if (in_array($method, ['PUT', 'PATCH'], true)) {
            return 'edit';
        }

        if (preg_match('/(^|[.\/_-])(edit|update|status-change|status_change|review|assign|complete|grade|publish|issue)([.\/_-]|$)/', $route)) {
            return 'edit';
        }

        return 'view';
    }

    public function moduleForLegacyAbility(string $ability): ?string
    {
        $map = [
            'Manage Staffs' => 'staff',
            'Manage Affiliates' => 'affiliates',
            'Manage Events' => 'events',
            'Manage Consultations' => 'consultations',
            'Manage Free Consultations' => 'consultation_requests',
            'Manage Courses' => 'courses',
            'Manage Students' => 'students',
            'Manage Service' => 'services',
            'Manage Transaction' => 'transactions',
            'Manage Cms Settings' => 'cms',
            'Manage Setting' => 'settings',
            'Manage Leads' => 'leads',
            'Manage Admissions' => 'admissions',
            'Manage Student Documents' => 'student_documents',
        ];

        return $map[$ability] ?? null;
    }

    public function grantLegacyDefaults(Role $role, array $legacyAbilities): void
    {
        $byModule = [];
        foreach ($legacyAbilities as $ability) {
            $module = $this->moduleForLegacyAbility($ability);
            if ($module) {
                $byModule[] = $module;
            }
        }

        $permissions = [];
        foreach (array_unique($byModule) as $module) {
            foreach (array_keys($this->actions()) as $action) {
                $permissions[] = $module . '.' . $action;
            }
        }

        if ($permissions) {
            $role->givePermissionTo($permissions);
        }
    }
}
