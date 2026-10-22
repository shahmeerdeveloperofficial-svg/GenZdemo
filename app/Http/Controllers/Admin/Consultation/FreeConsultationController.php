<?php

namespace App\Http\Controllers\Admin\Consultation;

use App\Http\Controllers\Controller;
use App\Mail\EmailNotify;
use App\Models\FreeConsultation;
use App\Models\User;
use App\Models\StudyLevel;
use App\Models\Country;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Str;
use function Clue\StreamFilter\fun;

class FreeConsultationController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $data['pageTitle'] = __('Consultation Requests');
        $data['activeFreeConsultation'] = 'active';

        if ($request->ajax()) {
            if(auth()->user()->role == USER_ROLE_STAFF ){
                $freeConsultations = FreeConsultation::with('study_level')->where('assign_by', auth()->user()->id)->orderByDesc('id');
            }else{
                $freeConsultations = FreeConsultation::with('study_level')->orderByDesc('id');
            }

            return datatables($freeConsultations)
                ->addIndexColumn()
                ->editColumn('name', function($data){
                        return $data->first_name.' '.$data->last_name;
                })
                ->editColumn('study_level', function($data){
                        return $data->study_level?->name;
                })
                ->editColumn('status', function ($data) {
                    switch ($data->status) {
                        case WORKING_STATUS_PENDING:
                            return "<p class='zBadge zBadge-pending'>" . __('Pending') . "</p>";
                        case WORKING_STATUS_PROCESSING:
                            return "<p class='zBadge zBadge-processing'>" . __('Processing') . "</p>";
                        case WORKING_STATUS_CANCELED:
                            return "<p class='zBadge zBadge-cancelled'>" . __('Cancelled') . "</p>";
                        default:
                            return "<p class='zBadge zBadge-completed'>" . __('Converted To Customer') . "</p>";
                    }
                })
                ->addColumn('action', function ($data) {
                    return '<div class="d-flex align-items-center g-10 justify-content-end">
                            <button onclick="getEditModal(\'' . route('admin.free_consultations.view', encodeId($data->id)) . '\', \'#view-modal\')" class="genz-row-action genz-row-action--edit" title="'.__('Edit').'" aria-label="'.__('Edit').'">
                                 ' . view('partials.icons.edit')->render() . '
                            </button>
                            <form method="POST" action="'.route('admin.free_consultations.delete', encodeId($data->id)).'" onsubmit="return confirm(\''.e(__('Delete this consultation request?')).'\')">
                                <input type="hidden" name="_token" value="'.e(csrf_token()).'">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="genz-row-action genz-row-action--delete" title="'.__('Delete').'" aria-label="'.__('Delete').'">
                                    ' . view('partials.icons.delete')->render() . '
                                </button>
                            </form>
                        </div>';
                })
                ->rawColumns(['title', 'status', 'action'])
                ->make(true);
        }

        return view('admin.free_consultations.index', $data);
    }

    public function view($id)
    {
        $data['consultation'] = FreeConsultation::findOrFail(decodeId($id));
        $data['staffList'] = User::where(['role' => USER_ROLE_STAFF, 'status' => STATUS_ACTIVE])->get();

        return view('admin.free_consultations.view', $data);
    }

    public function changeStatus(Request $request, $id)
    {
        $consultation = FreeConsultation::where('id', decodeId($id))->with('study_level')->first();

        if ($request->status == WORKING_STATUS_SUCCEED) {
            $existingUser = User::where('email', $consultation->email)->first();
            if ($existingUser) {
                return $this->error([], __('User with this email already exists.'));
            }

            $password = 'password123'; 
            $user = User::create([
                'first_name' => $consultation->first_name,
                'last_name' => $consultation->last_name,
                'email' => $consultation->email,
                'mobile' => $consultation->mobile,
                'password' => bcrypt($password),
                'role' => USER_ROLE_STUDENT,
                'status' => USER_STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]);

            // Update consultation with user_id
            $consultation->user_id = $user->id;
        }

        $consultation->update([
            'narration' => $request->narration,
            'status' => $request->status,
            'assign_by' => $request->assign_by,
        ]);

        return $this->success([], __(UPDATED_SUCCESSFULLY));
    }

    public function destroy($id)
    {
        $consultation = FreeConsultation::findOrFail(decodeId($id));
        $consultation->delete();

        return redirect()->route('admin.free_consultations.index')->with('success', __('Consultation request moved to deleted records.'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'study_level_id' => 'required|exists:study_levels,id',
            'consultation_type' => 'nullable|in:' . CONSULTATION_TYPE_PHYSICAL . ',' . CONSULTATION_TYPE_VIRTUAL,
            'fund_type' => 'nullable|in:' . FUND_TYPE_SELF . ',' . FUND_TYPE_PARENTS . ',' . FUND_TYPE_SEEKING_SCHOLARSHIP . ',' . FUND_TYPE_BANK_LOAN . ',' . FUND_TYPE_EMPLOYER_SCHOLARSHIP . ',' . FUND_TYPE_OTHER,
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'narration' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            FreeConsultation::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'study_level_id' => $request->study_level_id,
                'consultation_type' => $request->consultation_type,
                'fund_type' => $request->fund_type,
                'country_ids' => $request->country_ids ?? [],
                'narration' => $request->narration,
                'status' => WORKING_STATUS_PENDING,
            ]);

            DB::commit();
            return $this->success([], __(CREATED_SUCCESSFULLY));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], getErrorMessage($e, __('Failed to create consultation request')));
        }
    }

    public function downloadDemo()
    {
        try {
            // Get all available options from database
            $studyLevels = StudyLevel::where('status', STATUS_ACTIVE)->pluck('name')->toArray();
            $countries = Country::where('status', STATUS_ACTIVE)->pluck('name')->toArray();
            
            // Build instruction strings
            $studyLevelOptions = implode(', ', $studyLevels);
            $consultationTypeOptions = 'Physical, Virtual';
            $fundTypeOptions = 'Self, Parents, Seeking Scholarship, Bank Loan, Employer Scholarship, Other';
            $countryOptions = implode(', ', $countries);
            
            $headers = [
                'First Name *',
                'Last Name *',
                'Email *',
                'Mobile *',
                'Study Level * (' . $studyLevelOptions . ')',
                'Consultation Type (' . $consultationTypeOptions . ')',
                'Education Fund Type (' . $fundTypeOptions . ')',
                'Preferred Study Destination (Multiple allowed - comma-separated. Available: ' . $countryOptions . ')',
                'Narration'
            ];

            $sampleData = [
                ['John', 'Doe', 'john.doe@example.com', '+1234567890', 'Undergraduate', 'Virtual', 'Self', 'USA, Canada, UK', 'Interested in Computer Science'],
                ['Jane', 'Smith', 'jane.smith@example.com', '+9876543210', 'Postgraduate', 'Physical', 'Parents', 'Australia, New Zealand', 'Looking for MBA programs'],
            ];

            $fileName = 'leads_import_demo_' . time() . '.csv';
            $filePath = storage_path('app/temp/' . $fileName);
            
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $file = fopen($filePath, 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Write headers
            fputcsv($file, $headers);
            
            // Write sample data
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);

            return response()->download($filePath, 'leads_import_demo.csv')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return $this->error([], getErrorMessage($e, __('Failed to generate demo file')));
        }
    }

    public function downloadStudyLevels()
    {
        try {
            $studyLevels = StudyLevel::where('status', STATUS_ACTIVE)->pluck('name')->toArray();
            $content = implode("\n", $studyLevels);
            
            $fileName = 'study_levels_' . time() . '.txt';
            $filePath = storage_path('app/temp/' . $fileName);
            
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            file_put_contents($filePath, $content);
            
            return response()->download($filePath, 'study_levels.txt')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return $this->error([], getErrorMessage($e, __('Failed to generate file')));
        }
    }

    public function downloadCountries()
    {
        try {
            $countries = Country::where('status', STATUS_ACTIVE)->pluck('name')->toArray();
            $content = implode("\n", $countries);
            
            $fileName = 'countries_' . time() . '.txt';
            $filePath = storage_path('app/temp/' . $fileName);
            
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            file_put_contents($filePath, $content);
            
            return response()->download($filePath, 'countries.txt')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return $this->error([], getErrorMessage($e, __('Failed to generate file')));
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('import_file');
            $extension = $file->getClientOriginalExtension();
            $rows = [];
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                // For Excel files, PhpSpreadsheet is required
                // Install via: composer require phpoffice/phpspreadsheet
                if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                    return $this->error([], __('Excel file support requires PhpSpreadsheet. Please install it via: composer require phpoffice/phpspreadsheet or use CSV format.'));
                }
                
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
            } else {
                // CSV file handling
                $fileHandle = fopen($file->getRealPath(), 'r');
                
                // Skip BOM if present
                $firstLine = fgets($fileHandle);
                if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
                    $firstLine = substr($firstLine, 3);
                }
                rewind($fileHandle);
                if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
                    fseek($fileHandle, 3);
                }
                
                while (($row = fgetcsv($fileHandle)) !== FALSE) {
                    $rows[] = $row;
                }
                fclose($fileHandle);
            }

            if (count($rows) < 2) {
                return $this->error([], __('The file is empty or has no data rows'));
            }

            // Remove header row
            array_shift($rows);

            $leads = [];
            $studyLevels = StudyLevel::where('status', STATUS_ACTIVE)->pluck('name', 'id')->toArray();
            $studyLevelsByName = array_flip($studyLevels);
            $countries = Country::where('status', STATUS_ACTIVE)->pluck('name', 'id')->toArray();
            $countriesByName = array_flip($countries);

            foreach ($rows as $index => $row) {
                $lead = [
                    'row_number' => $index + 2,
                    'first_name' => $row[0] ?? '',
                    'last_name' => $row[1] ?? '',
                    'email' => $row[2] ?? '',
                    'mobile' => $row[3] ?? '',
                    'study_level' => $row[4] ?? '',
                    'consultation_type' => $row[5] ?? '',
                    'fund_type' => $row[6] ?? '',
                    'countries' => $row[7] ?? '',
                    'narration' => $row[8] ?? '',
                    'errors' => [],
                    'is_valid' => true,
                ];

                // Validation
                if (empty($lead['first_name'])) {
                    $lead['errors'][] = __('First name is required');
                    $lead['is_valid'] = false;
                }

                if (empty($lead['last_name'])) {
                    $lead['errors'][] = __('Last name is required');
                    $lead['is_valid'] = false;
                }

                if (empty($lead['email'])) {
                    $lead['errors'][] = __('Email is required');
                    $lead['is_valid'] = false;
                } elseif (!filter_var($lead['email'], FILTER_VALIDATE_EMAIL)) {
                    $lead['errors'][] = __('Invalid email format');
                    $lead['is_valid'] = false;
                } elseif (FreeConsultation::where('email', $lead['email'])->exists()) {
                    $lead['errors'][] = __('Email already exists');
                    $lead['is_valid'] = false;
                }

                if (empty($lead['mobile'])) {
                    $lead['errors'][] = __('Mobile is required');
                    $lead['is_valid'] = false;
                }

                if (empty($lead['study_level'])) {
                    $lead['errors'][] = __('Study level is required');
                    $lead['is_valid'] = false;
                } else {
                    $studyLevelId = $studyLevelsByName[$lead['study_level']] ?? null;
                    if (!$studyLevelId) {
                        $lead['errors'][] = __('Invalid study level: ' . $lead['study_level']);
                        $lead['is_valid'] = false;
                    } else {
                        $lead['study_level_id'] = $studyLevelId;
                        $lead['study_level_name'] = $lead['study_level'];
                    }
                }

                // Process countries - don't mark as invalid if countries are invalid, just skip them
                $countryIds = [];
                if (!empty($lead['countries'])) {
                    $countryNames = array_map('trim', explode(',', $lead['countries']));
                    foreach ($countryNames as $countryName) {
                        $countryId = $countriesByName[$countryName] ?? null;
                        if ($countryId) {
                            $countryIds[] = $countryId;
                        }
                        // Don't add error for invalid countries - just skip them
                    }
                }
                $lead['country_ids'] = $countryIds;

                // Process consultation type
                if (!empty($lead['consultation_type'])) {
                    $consultationType = strtolower(trim($lead['consultation_type']));
                    if ($consultationType === 'physical') {
                        $lead['consultation_type_id'] = CONSULTATION_TYPE_PHYSICAL;
                    } elseif ($consultationType === 'virtual') {
                        $lead['consultation_type_id'] = CONSULTATION_TYPE_VIRTUAL;
                    }
                }

                // Process fund type
                if (!empty($lead['fund_type'])) {
                    $fundType = strtolower(trim($lead['fund_type']));
                    $fundTypeMap = [
                        'self' => FUND_TYPE_SELF,
                        'parents' => FUND_TYPE_PARENTS,
                        'seeking scholarship' => FUND_TYPE_SEEKING_SCHOLARSHIP,
                        'bank loan' => FUND_TYPE_BANK_LOAN,
                        'employer scholarship' => FUND_TYPE_EMPLOYER_SCHOLARSHIP,
                        'other' => FUND_TYPE_OTHER,
                    ];
                    $lead['fund_type_id'] = $fundTypeMap[$fundType] ?? null;
                }

                $leads[] = $lead;
            }

            // Filter out invalid leads - only keep leads with all required fields valid
            $validLeads = array_filter($leads, function($lead) {
                return $lead['is_valid'] === true;
            });

            // Re-index array
            $validLeads = array_values($validLeads);

            // Store only valid leads in session for preview
            $sessionKey = 'import_leads_' . time();
            Session::put($sessionKey, $validLeads);

            $validCount = count($validLeads);
            $invalidCount = count($leads) - $validCount;

            if ($validCount == 0) {
                return $this->error([], __('No valid leads found. Please check that all required fields (First Name, Last Name, Email, Mobile, Study Level) are filled correctly.'));
            }

            return $this->success([
                'redirect_url' => route('admin.free_consultations.preview', ['session_key' => $sessionKey])
            ], __('File processed successfully. Found :valid valid leads. :invalid invalid rows were ignored.', ['valid' => $validCount, 'invalid' => $invalidCount]));
        } catch (Exception $e) {
            return $this->error([], getErrorMessage($e, __('Failed to process import file')));
        }
    }

    public function preview(Request $request, $sessionKey)
    {
        $leads = Session::get($sessionKey);
        
        if (!$leads) {
            return redirect()->route('admin.free_consultations.index')->with('error', __('Session expired. Please upload the file again.'));
        }

        // All leads in session are already valid (filtered in import function)
        $validCount = count($leads);
        $invalidCount = 0;

        return view('admin.free_consultations.preview', [
            'leads' => $leads,
            'sessionKey' => $sessionKey,
            'validCount' => $validCount,
            'invalidCount' => $invalidCount,
        ]);
    }

    public function saveImport(Request $request)
    {
        $request->validate([
            'session_key' => 'required|string',
        ]);

        $leads = Session::get($request->session_key);
        
        if (!$leads) {
            return $this->error([], __('Session expired. Please upload the file again.'));
        }

        DB::beginTransaction();
        try {
            $savedCount = 0;
            foreach ($leads as $lead) {
                if (!$lead['is_valid']) {
                    continue;
                }

                FreeConsultation::create([
                    'first_name' => $lead['first_name'],
                    'last_name' => $lead['last_name'],
                    'email' => $lead['email'],
                    'mobile' => $lead['mobile'],
                    'study_level_id' => $lead['study_level_id'],
                    'consultation_type' => $lead['consultation_type_id'] ?? null,
                    'fund_type' => $lead['fund_type_id'] ?? null,
                    'country_ids' => $lead['country_ids'],
                    'narration' => $lead['narration'] ?? null,
                    'status' => WORKING_STATUS_PENDING,
                ]);

                $savedCount++;
            }

            DB::commit();
            Session::forget($request->session_key);
            
            return $this->success([], __('Successfully imported :count consultation requests', ['count' => $savedCount]));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], getErrorMessage($e, __('Failed to save consultation requests')));
        }
    }
}
