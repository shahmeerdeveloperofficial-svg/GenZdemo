@foreach($permissionGroups as $group => $modules)
    <section class="mb-4">
        <h5 class="fs-16 fw-700 lh-24 text-title-text mb-2">{{ __($group) }}</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light"><tr>
                    <th>{{ __('Module') }}</th>
                    @foreach(config('role_permissions.actions') as $action => $label)
                        <th class="text-center">{{ __($label) }}</th>
                    @endforeach
                </tr></thead>
                <tbody>
                @foreach($modules as $module)
                    <tr>
                        <td>{{ __($module['label']) }}</td>
                        @foreach($module['permissions'] as $action => $permission)
                            <td class="text-center">
                                <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                    aria-label="{{ __($module['label']) }} {{ __($action) }}"
                                    @checked(in_array($permission, $oldPermissions, true))>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endforeach
