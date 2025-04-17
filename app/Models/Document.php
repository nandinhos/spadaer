<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_number',
        'item_number',
        'code',
        'descriptor',
        'document_number',
        'title',
        'document_date',
        'project',
        'confidentiality',
        'version',
        'is_copy',
        //'conference_military',
        //'conference_date',
        // 'user_id',
        // 'box_id',
        // 'project_id',
    ];

    protected $casts = [
        'document_date' => 'date:Y-m-d',
        'is_copy' => 'boolean',
    ];
    
    // Add a mutator to handle secrecy values
    public function setSecrecyAttribute($value)
    {
        // If the value is too long, truncate it or map it to an allowed value
        if ($value === 'COMPANY CONFIDENTIAL') {
            $this->attributes['confidentiality'] = 'CONFIDENTIAL';
        } else {
            $this->attributes['confidentiality'] = $value;
        }
    }

    // Relacionamentos (opcional, se criar models Box, Project, User)
    // public function user() { return $this->belongsTo(User::class); }
    // public function box() { return $this->belongsTo(Box::class); }
    // public function project() { return $this->belongsTo(Project::class); }
}