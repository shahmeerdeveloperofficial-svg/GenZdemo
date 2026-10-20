<?php

namespace App\Http\Controllers\Admin\Student;

use App\Http\Controllers\Controller;
use App\Mail\EmailNotify;
use App\Models\CourseEnrollment;
use App\Models\Program;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Str;
use Throwable;

class StudentController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $students = User::orderBy('users.id', 'desc')
                ->where('role', USER_ROLE_STUDENT)
                ->select('users.id', 'first_name', 'last_name', 'email', 'image', 'gender', 'mobile', 'status');
            return datatables($students)
                ->editColumn('name', function ($data) {
                    return $data->first_name . ' ' . $data->last_name;
                })
                ->editColumn('status', function ($data) {
                    if ((int) $data->status === (int) STATUS_SUSPENDED) {
                        return "<span class='zBadge zBadge-suspended'>" . e(__('Suspended')) . "</span>";
                    }

                    $isActive = (int) $data->status === (int) STATUS_ACTIVE;
                    $activeLabel = e(__('Active'));
                    $inactiveLabel = e(__('Inactive'));
                    $activateHint = e(__('Click to activate'));
                    $deactivateHint = e(__('Click to deactivate'));
                    $currentLabel = $isActive ? $activeLabel : $inactiveLabel;
                    $nextHint = $isActive ? $deactivateHint : $activateHint;
                    $statusUrl = e(route('admin.students.status_change', encodeId($data->id)));

                    return '<button type="button" class="zBadge ' . ($isActive ? 'zBadge-active' : 'zBadge-deactive') . ' genz-student-status-toggle"'
                        . ' data-student-status-toggle data-status-url="' . $statusUrl . '"'
                        . ' data-current-status="' . (int) $data->status . '"'
                        . ' data-active-status="' . (int) STATUS_ACTIVE . '"'
                        . ' data-inactive-status="' . (int) STATUS_DEACTIVATE . '"'
                        . ' data-active-label="' . $activeLabel . '" data-inactive-label="' . $inactiveLabel . '"'
                        . ' data-activate-hint="' . $activateHint . '" data-deactivate-hint="' . $deactivateHint . '"'
                        . ' aria-pressed="' . ($isActive ? 'true' : 'false') . '"'
                        . ' aria-label="' . $currentLabel . '. ' . $nextHint . '" title="' . $nextHint . '">'
                        . '<span>' . $currentLabel . '</span></button>';
                })
                ->editColumn('gender', function ($data) {
                    switch ($data->gender) {
                        case 1:
                            return __('Male');
                        case 2:
                            return __('Female');
                        case 3:
                            return __('Other');
                    }
                })
                ->editColumn('image', function ($data) {
                    return '<div class="min-w-160 d-flex align-items-center cg-10">
                            <div class="flex-shrink-0 w-41 h-41 bd-one bd-c-stroke rounded-circle overflow-hidden bg-eaeaea d-flex justify-content-center align-items-center">
                                <img src="' . getFileUrl($data->image) . '" alt="Image" class="rounded avatar-xs w-100 h-100 object-fit-cover">
                            </div>
                        </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<ul class="d-flex align-items-center cg-5 justify-content-end">
                        <li class="align-items-center d-flex gap-2">
                            <button onclick="getEditModal(\'' . route('admin.students.edit', encodeId($data->id)) . '\', \'#edit-modal\')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-30 justify-content-center rounded-circle w-30" title="' . __('Edit student') . '">
                                ' . view('partials.icons.edit')->render() . '
                            </button>
                            <button onclick="deleteItem(\'' . route('admin.students.delete', encodeId($data->id)) . '\', \'commonDataTable\')" class="d-flex justify-content-center align-items-center w-30 h-30 rounded-circle bd-one bd-c-stroke bg-white" title="' . __('Delete') . '">
                                ' . view('partials.icons.delete')->render() . '
                            </button>
                        </li>
                    </ul>';
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }

        $data['pageTitle'] = __('Students');
        $data['activeStudent'] = 'active';

        return view('admin.students.index', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['required', 'string', 'min:6', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'status' => ['required', Rule::in([STATUS_ACTIVE, STATUS_DEACTIVATE])],
        ]);

        try {
            $user = DB::transaction(function () use ($validated): User {
                $user = new User();
                $user->first_name = $validated['first_name'];
                $user->last_name = $validated['last_name'];
                $user->email = $validated['email'];
                $user->mobile = $validated['mobile'];
                $user->status = $validated['status'];
                $user->password = Hash::make($validated['password']);
                $user->role = USER_ROLE_STUDENT;
                $user->created_by = auth()->id();
                $user->verify_token = str_replace('-', '', Str::uuid()->toString());
                $user->save();

                return $user;
            });
        } catch (Throwable $e) {
            Log::error('Student account creation failed.', ['exception' => $e]);
            return $this->error([], __('Unable to create the student. Please check the details and try again.'));
        }

        // Email delivery is best-effort: an SMTP/configuration failure must not
        // undo a student account that has already been safely committed.
        try {
            $createStudentData = [
                '{{name}}' => $user->name,
                '{{email}}' => $user->email,
                '{{link}}' => route('login'),
                '{{password}}' => $validated['password'],
            ];
            $data = getEmailTemplate('user-create', $createStudentData);
            Mail::to($user->email)->send(new EmailNotify($data));
        } catch (Throwable $e) {
            Log::warning('Student account was created but the welcome email could not be delivered.', [
                'student_id' => $user->id,
                'exception' => $e,
            ]);
        }

        return $this->success([], __(CREATED_SUCCESSFULLY));
    }

    public function edit($id)
    {
        $data['user'] = $this->findStudentOrFail($id);

        return view('admin.students.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $student = $this->findStudentOrFail($id);
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->id)],
            'mobile' => ['required', 'string', 'min:6', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'status' => ['required', Rule::in([STATUS_ACTIVE, STATUS_DEACTIVATE, STATUS_SUSPENDED])],
        ]);

        try {
            DB::transaction(function () use ($student, $validated): void {
                $student->first_name = $validated['first_name'];
                $student->last_name = $validated['last_name'];
                $student->email = $validated['email'];
                $student->mobile = $validated['mobile'];
                $student->status = $validated['status'];

                if (!empty($validated['password'])) {
                    $student->password = Hash::make($validated['password']);
                }

                $student->save();
            });
        } catch (Throwable $e) {
            Log::error('Student account update failed.', ['student_id' => $student->id, 'exception' => $e]);
            return $this->error([], __('Unable to update the student. Please try again.'));
        }

        return $this->success([], __(UPDATED_SUCCESSFULLY));
    }

    public function statusChangeModal($id)
    {
        $data['user'] = $this->findStudentOrFail($id);

        return view('admin.students.status_change_modal', $data);
    }

    public function statusChange(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([STATUS_ACTIVE, STATUS_DEACTIVATE, STATUS_SUSPENDED])],
        ]);

        try {
            $user = $this->findStudentOrFail($id);
            $user->status = $validated['status'];
            $user->save();
            return $this->success([], __('Updated Successfully'));
        } catch (Throwable $e) {
            Log::error('Student status update failed.', ['exception' => $e]);
            return $this->error([], __('Something went wrong! Please try again.'));
        }
    }

    public function delete($id)
    {
        try {
            $user = $this->findStudentOrFail($id);
            $user->delete();

            return $this->success([], __(DELETED_SUCCESSFULLY));
        } catch (Throwable $e) {
            return $this->error([], __('Something went wrong! Please try again'));
        }
    }

    public function courses(Request $request, $id)
    {
        if ($request->ajax()) {

            $myCourses = CourseEnrollment::join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                ->join('programs', 'courses.program_id', '=', 'programs.id')
                ->where('course_enrollments.status', STATUS_ACTIVE)
                ->select([
                    'programs.title as program_title',
                    'courses.id',
                    'courses.title',
                    'courses.duration',
                    'courses.start_date',
                    'courses.program_id',
                    'courses.thumbnail',
                    'courses.status'
                ])
                ->orderBy('course_enrollments.id', 'DESC');


            // Apply search filter
            if ($request->has('search_key') && $request->search_key != '') {
                $myCourses->where(function ($query) use ($request) {
                    $query->where('courses.title', 'like', '%' . $request->search_key . '%')
                        ->orWhere('price', 'like', '%' . $request->search_key . '%')
                        ->orWhere('duration', 'like', '%' . $request->search_key . '%');
                });
            }

            // Apply program filter
            if ($request->has('program_id') && $request->program_id != '') {
                $myCourses->where('program_id', $request->program_id);
            }

            return datatables($myCourses)
                ->editColumn('status', function ($data) {
                    return $data->status == STATUS_ACTIVE
                        ? "<p class='zBadge zBadge-active'>" . __('Active') . "</p>"
                        : "<p class='zBadge zBadge-inactive'>" . __('Deactivate') . "</p>";
                })
                ->editColumn('thumbnail', function ($data) {
                    return '<div class="min-w-160 d-flex align-items-center cg-10">
                        <div class="flex-shrink-0 w-41 h-41 bd-one bd-c-stroke rounded-circle overflow-hidden bg-eaeaea d-flex justify-content-center align-items-center">
                            <img src="' . getFileUrl($data->thumbnail) . '" alt="Image" class="rounded avatar-xs w-100 h-100 object-fit-cover">
                        </div>
                    </div>';
                })
                ->rawColumns(['status', 'action', 'thumbnail'])
                ->make(true);
        }

        $data['user'] = $this->findStudentOrFail($id);
        $data['pageTitle'] = $data['user']->name.' '.__('Purchase Course');
        $data['activeStudent'] = 'active';
        $data['programs'] = Program::where('status', STATUS_ACTIVE)->get();

        return view('admin.students.course_list', $data);
    }

    private function findStudentOrFail($encodedId): User
    {
        return User::whereKey(decodeId($encodedId))
            ->where('role', USER_ROLE_STUDENT)
            ->firstOrFail();
    }
}
