<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role as RoleEnum;
use App\Traits\HasPublicUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPublicUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'userId', 'roleId');
    }

    public function assignRole(string $role): self
    {
        $role = Role::findByName($role);

        $this->roles()->attach($role->id);

        return $this;
    }

    public function removeRole(string $role): self
    {
        $role = Role::findByName($role);

        $this->roles()->detach($role->id);

        return $this;
    }

    public function hasRole(string $role): bool
    {
        $role = Role::findByName($role);

        return $this->roles->contains($role->id);
    }

    public function scopeWithRole(Builder $builder, string $role): void
    {
        $builder->whereRelation('roles', 'name', $role);
    }

    public function scopeAdmin(Builder $builder): void
    {
        $builder->withRole(RoleEnum::Admin->value);
    }

    public function scopeEditor(Builder $builder): void
    {
        $builder->withRole(RoleEnum::Editor->value);
    }
}
