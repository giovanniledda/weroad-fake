<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        //        'id',
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

    /**
     * Get the route key for the model.
     *
     * @return string
     */
//    public function getRouteKeyName()
//    {
//        return 'uuid';
//    }

    public function roles(): BelongsToMany
    {
//        return $this->belongsToMany(Role::class);
        return $this->belongsToMany(Role::class, 'role_user', 'userId', 'roleId');
    }

    public function assignRole(RoleEnum $role): self
    {
        $role = Role::findByName($role->value);

        $this->roles()->attach($role->id);

        return $this;
    }

    public function removeRole(RoleEnum $role): self
    {
        $role = Role::findByName($role->value);

        $this->roles()->detach($role->id);

        return $this;
    }

    public function hasRole(RoleEnum $role): bool
    {
        $role = Role::findByName($role->value);

        return $this->roles->contains($role->id);
    }

    public function scopeWithRole(Builder $builder, RoleEnum $role): void
    {
        $builder->whereRelation('roles', 'name', $role->value);
    }

    public function scopeAdmin(Builder $builder): void
    {
        $builder->withRole(RoleEnum::Admin);
    }

    public function scopeEditor(Builder $builder): void
    {
        $builder->withRole(RoleEnum::Editor);
    }
}
