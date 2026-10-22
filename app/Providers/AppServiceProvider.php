<?php

namespace App\Providers;

use App\Models\Language;
use App\Models\Setting;
use Illuminate\Database\Schema\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config([
            'datatables.engines.collection' => \App\DataTables\CollectionDataTable::class,
            'datatables.engines.eloquent' => \App\DataTables\EloquentDataTable::class,
            'datatables.engines.query' => \App\DataTables\QueryDataTable::class,
        ]);

        Gate::before(function ($user, $ability) {
            if ((int) $user->role === USER_ROLE_ADMIN) {
                return true;
            }

            $rolePermissions = [
                USER_ROLE_STAFF => config('genz.authorization.staff', []),
                USER_ROLE_CONSULTANT => config('genz.authorization.consultant', []),
                USER_ROLE_INSTRUCTOR => config('genz.authorization.instructor', []),
                USER_ROLE_FINANCE => config('genz.authorization.finance', []),
                USER_ROLE_CMS_EDITOR => config('genz.authorization.cms_editor', []),
            ];

            if (isset($rolePermissions[(int) $user->role])) {
                // Explicit Spatie roles are the source of truth once assigned.
                // Keep config-based defaults only for legacy accounts with no role pivots.
                if ($user->roles->isNotEmpty()) {
                    return null;
                }

                return in_array($ability, $rolePermissions[(int) $user->role], true) ? true : null;
            }

            return null;
        });

        Paginator::defaultView('layouts.partial.common_pagination');

        Builder::defaultStringLength(191);
        try {
            $connection = DB::connection()->getPdo();
            if ($connection) {
                $allOptions = [];
                $allOptions['settings'] = Setting::all()->pluck('option_value', 'option_key')->toArray();
                config($allOptions);

            }
            $language = Language::where('iso_code', session()->get('local'))->first();

            if (!$language) {
                $language = Language::find(1);
                if ($language) {
                    $ln = $language->iso_code;
                    session(['local' => $ln]);
                    App::setLocale(session()->get('local'));
                }
            } else {
                $language = Language::where('default', ACTIVE)->first();
                if ($language) {
                    $ln = $language->iso_code;
                    session(['local' => $ln]);
                    App::setLocale(session()->get('local'));
                }
            }

            config(['app.defaultLanguage' => getDefaultLanguage()]);
            config(['app.currencySymbol' => getCurrencySymbol()]);
            config(['app.isoCode' => getIsoCode()]);
            config(['app.currencyPlacement' => getCurrencyPlacement()]);
            config(['app.debug' => getOption('app_debug', false)]);
            config(['app.timezone' => getOption('app_timezone','UTC')]);
            date_default_timezone_set( getOption('app_timezone','UTC'));

            config(['services.google.client_id' => getOption('google_client_id')]);
            config(['services.google.client_secret' => getOption('google_client_secret')]);
            config(['services.google.redirect' => url('auth/google/callback')]);

            config(['services.facebook.client_id' => getOption('facebook_client_id')]);
            config(['services.facebook.client_secret' => getOption('facebook_client_secret')]);
            config(['services.facebook.redirect' => url('auth/facebook/callback')]);
            if (!empty(getOption('google_recaptcha_status')) && getOption('google_recaptcha_status') == 1){
                config(['recaptchav3.sitekey' => getOption('google_recaptcha_site_key')]);
                config(['recaptchav3.secret' => getOption('google_recaptcha_secret_key')]);
            }

            View::share('totalMessage', 1);

            if (env('FORCE_SSL') == true) {
                URL::forceScheme('https');
            }
        } catch (\Exception $e) {
            Log::info('Service Provider - ' . $e->getMessage());
        }
    }

}
