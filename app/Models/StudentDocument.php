<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    public const CATEGORIES = ['passport', 'academic_transcript', 'language_test', 'cv', 'financial_document', 'other'];
    public const STATUSES = ['pending', 'approved', 'rejected', 'superseded'];

    protected $fillable = ['user_id', 'replaces_document_id', 'category', 'storage_path', 'original_name', 'mime_type', 'size_bytes', 'status', 'reviewed_by', 'reviewed_at', 'review_note'];
    protected $casts = ['reviewed_at' => 'datetime', 'size_bytes' => 'integer'];

    public function student() { return $this->belongsTo(User::class, 'user_id'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function replacedDocument() { return $this->belongsTo(self::class, 'replaces_document_id'); }
}
