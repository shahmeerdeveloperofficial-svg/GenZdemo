<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\FileManager;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Exception;
use function NunoMaduro\Collision\Exceptions\getPrevious;

class ServiceController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', \Illuminate\Validation\Rule::in([STATUS_ACTIVE, STATUS_DEACTIVATE])],
            'price_type' => ['nullable', \Illuminate\Validation\Rule::in(['free', 'paid'])],
            'search_key' => ['nullable', 'string', 'max:120'],
        ]);
        $query = Service::query();

        if($request->has('search_key')){
            $query->where(fn ($q) => $q->where('title','like','%'.$request->search_key.'%')
                   ->orWhere('price','like','%'.$request->search_key.'%'));
        }
        if ($request->filled('status')) $query->where('status', $request->input('status'));
        if ($request->input('price_type') === 'free') $query->where('price', '<=', 0);
        if ($request->input('price_type') === 'paid') $query->where('price', '>', 0);
        if(auth()->user()->role != USER_ROLE_STUDENT){
            $data['service'] = $query->orderBy('id', 'DESC')->paginate(12)->withQueryString();
        }else{
            $data['service'] = $query->orderBy('id', 'DESC')->where('status',STATUS_ACTIVE)->paginate(12)->withQueryString();
        }

        if ($request->ajax()) {
            return view('admin.services.partials.list', $data)->render();
        }
        $data['pageTitle'] = __("Manage Service");
        $data['activeService'] = 'active';
        $data['criteriaFields'] = [
            ['name' => 'search_key', 'label' => __('Search services'), 'type' => 'search'],
            ['name' => 'price_type', 'label' => __('Price'), 'options' => ['free' => __('Free'), 'paid' => __('Paid')]],
        ];
        if ((int) auth()->user()->role !== USER_ROLE_STUDENT) {
            $data['criteriaFields'][] = ['name' => 'status', 'label' => __('Status'), 'options' => [STATUS_ACTIVE => __('Active'), STATUS_DEACTIVATE => __('Inactive')]];
        }

        return view(getPrefix().'.services.index', $data);
    }

    public function create()
    {
        $data['pageTitle'] = __("Create Service");
        $data['activeService'] = 'active';
        $data['pageTitleParent'] = __("Manage Service");
        return view('admin.services.create', $data);
    }

    public function edit($id)
    {
        $data['pageTitle'] = __("Update Service");
        $data['activeService'] = 'active';
        $data['service'] = Service::find(decodeId($id));
        $data['pageTitleParent'] = __("Manage Service");
        return view('admin.services.edit', $data);
    }

    public function details($id)
    {
        $data['pageTitle'] = __("Service Details");
        $data['activeService'] = 'active';
        $data['serviceData'] = Service::find(decodeId($id));
        $data['pageTitleParent'] = __("Manage Service");
        return view(getPrefix().'.services.details', $data);
    }

    public function store(ServiceRequest $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            if ($id) {
                $services = Service::find($id);
            } else {
                $services = new Service();
            }
            if (Service::where('slug', getSlug($request->title))->where('id', '!=', $id)->withTrashed()->count() > 0) {
                $slug = getSlug($request->title) . '-' . rand(100000, 999999);
            } else {
                $slug = getSlug($request->title);
            }

            $services->title = $request->title;
            $services->price = $request->price;
            $services->slug = $slug;
            $services->description = $request->description;
            $services->status = $request->status;

            $features = [];
            if ($request->has('feature_name')) {
                foreach ($request->input('feature_name') as $key => $name) {
                    $features[] = [
                        'name' => $name,
                        'value' => $request->input('feature_value.' . $key),
                    ];
                }
            }
            $services->feature = $features;

            if ($request->hasFile('image')) {
                    $newFile = new FileManager();
                    $uploadedFile = $newFile->upload('service', $request->image);
                $services->image = $uploadedFile->id;
            }
            if ($request->hasFile('icon')) {
                    $newFile = new FileManager();
                    $uploadedFile = $newFile->upload('service', $request->icon);
                $services->icon = $uploadedFile->id;
            }
            $services->save();
            DB::commit();
            $message = $request->id ? __(UPDATED_SUCCESSFULLY) : __(CREATED_SUCCESSFULLY);
            return $this->success([], getMessage($message));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], getMessage($e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            $data = Service::find(decodeId($id));
            $data->delete();
            return $this->success([], getMessage(DELETED_SUCCESSFULLY));
        } catch (Exception $exception) {
            return $this->error([], getMessage(SOMETHING_WENT_WRONG));
        }
    }
}
