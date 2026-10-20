<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationPreferenceController extends Controller
{
    private array $events=['lms.certificate.issued'=>'Course certificates','lms.assignment.graded'=>'Assignment grading'];

    public function index()
    {
        abort_unless(Schema::hasTable('notification_preferences'),503,'Notification preferences are not enabled until the notification migration is applied.');
        $preferences=DB::table('notification_preferences')->where('user_id',auth()->id())->where('channel','in_app')->pluck('enabled','event_key');
        return view('student.notifications.preferences', ['events'=>$this->events,'preferences'=>$preferences]);
    }

    public function update(Request $request)
    {
        abort_unless(Schema::hasTable('notification_preferences'),503,'Notification preferences are not enabled until the notification migration is applied.');
        $data=$request->validate(['preferences'=>['nullable','array'],'preferences.*'=>['boolean']]);
        foreach($this->events as $key=>$label) DB::table('notification_preferences')->updateOrInsert(['user_id'=>auth()->id(),'event_key'=>$key,'channel'=>'in_app'],['enabled'=>(bool)($data['preferences'][$key]??false),'updated_at'=>now(),'created_at'=>now()]);
        return back()->with('success',__('Notification preferences saved.'));
    }
}
