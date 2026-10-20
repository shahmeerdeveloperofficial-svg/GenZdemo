<?php

namespace App\Http\Controllers\Admin\Affiliate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Mail};
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AffiliateRequest;
use App\Mail\EmailNotify;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Str;
use Spatie\Permission\Models\Role;

class AffiliateController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $affiliates = User::query()
                ->where('users.role', USER_ROLE_AFFILIATE)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.status', 'users.email', 'users.mobile', 'users.affiliate_key');

            return datatables($affiliates)
                ->addIndexColumn()
                ->addColumn('name', function ($user) {
                    return $user->first_name . ' ' . $user->last_name;
                })
                ->addColumn('email', function ($user) {
                    return $user->email;
                })
                ->addColumn('mobile', function ($user) {
                    return $user->mobile;
                })
                ->addColumn('affiliate_key', function ($user) {
                    return $user->affiliate_key;
                })
                ->addColumn('status', function ($user) {
                    return $user->status == STATUS_ACTIVE
                        ? "<p class='zBadge zBadge-active'>" . __('Active') . "</p>"
                        : "<p class='zBadge zBadge-inactive'>" . __('Deactivate') . "</p>";
                })
                ->addColumn('action', function ($user) {
                    return '<div class="d-flex align-items-center g-10 justify-content-end">
                            <button onclick="getEditModal(\'' . route('admin.affiliates.edit', $user->id) . '\'' . ', \'#edit-modal\')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="Edit">
                                  ' . view('partials.icons.edit')->render() . '
                             </button>
                             <button onclick="deleteItem(\'' . route('admin.affiliates.delete', $user->id) . '\', \'affiliateDataTable\')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="Delete">
                                  ' . view('partials.icons.delete')->render() . '
                             </button>
                         </div>';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $data['pageTitle'] = __('Affiliates');
        $data['showManageAffiliate'] = 'show';
        $data['activeAffiliate'] = 'active';
        return view('admin.affiliates.index', $data);
    }

    public function store(AffiliateRequest $request)
    {
        
        try {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->status = $request->status;
            $user->password = bcrypt($request->password);
            $user->role = USER_ROLE_AFFILIATE;
            $user->affiliate_key = $this->generateUniqueAffiliateKey();
            $user->parent_id = auth()->id();
            $user->verify_token = str_replace('-', '', Str::uuid()->toString());
            $user->save();

            return $this->success([], __(CREATED_SUCCESSFULLY));
        } catch (\Exception $e) {
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }


    public function update(AffiliateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->status = $request->status;

            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            return $this->success([], __(UPDATED_SUCCESSFULLY));
        } catch (\Exception $e) {
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }


    public function edit($id)
    {
        $data['user'] = User::findOrFail($id);
        return view('admin.affiliates.edit')->with($data);
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return $this->success([], __(DELETED_SUCCESSFULLY));
        } catch (\Exception $e) {
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }



    private function generateUniqueAffiliateKey(): string
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {

            $key = sprintf(
                'aff-%s-%s',
                Str::lower(Str::random(8)),
                Str::lower(Str::random(8))
            );

            if (!User::where('affiliate_key', $key)->exists()) {
                return $key;
            }
        }

        throw new \RuntimeException('Unable to generate unique affiliate key');
    }
}
