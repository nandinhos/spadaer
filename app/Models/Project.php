<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use Auditable, HasFactory;

    protected $fillable = ['name', 'code', 'description']; // Permitir mass assignment

    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
