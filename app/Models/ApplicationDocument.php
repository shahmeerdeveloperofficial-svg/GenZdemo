<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    protected $fillable = ['admission_application_id','student_document_id','linked_by'];
    public function document() { return $this->belongsTo(StudentDocument::class, 'student_document_id'); }
    public function application() { return $this->belongsTo(AdmissionApplication::class, 'admission_application_id'); }
}
