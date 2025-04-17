<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'ordinance_number',
        'ordinance_file',
        'ordinance_date'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ordinance_date' => 'date'
    ];

    /**
     * Os membros que pertencem a esta comissão.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'commission_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Os documentos associados a esta comissão.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}