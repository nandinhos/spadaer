<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne; // Para o membro da comissão
use Illuminate\Database\Eloquent\Relations\HasManyThrough; // Para as caixas conferidas

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'rank',
        'full_name',
        'order_number',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * As comissões que este usuário pertence.
     */
    public function commissions()
    {
        return $this->belongsToMany(Commission::class, 'commission_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * As revisões de documentos feitas por este usuário.
     */
    public function documentReviews()
    {
        return $this->hasMany(DocumentReview::class);
    }

    // Relacionamento com o registro de membro da comissão (se houver)
    public function commissionMember(): HasOne
    {
        return $this->hasOne(CommissionMember::class);
    }

    // Caixas conferidas por este usuário (através da tabela commission_members)
    public function checkedBoxes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Box::class,              // Modelo final que queremos acessar (Caixa)
            CommissionMember::class, // Modelo intermediário (Membro da Comissão)
            'user_id',               // Chave estrangeira no modelo intermediário (commission_members.user_id)
            'checker_member_id',     // Chave estrangeira no modelo final (boxes.checker_member_id)
            'id',                    // Chave local no modelo atual (users.id)
            'id'                     // Chave local no modelo intermediário (commission_members.id)
        );
    }
}
