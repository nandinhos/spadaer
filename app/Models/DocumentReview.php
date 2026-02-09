<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReview extends Model
{
    use \App\Traits\Auditable, HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_id',
        'user_id',
        'review_date',
        'observations',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'review_date' => 'datetime',
    ];

    /**
     * O usuário que realizou a revisão.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * O documento que foi revisado.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
