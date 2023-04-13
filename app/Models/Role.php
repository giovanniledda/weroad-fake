<?php

namespace App\Models;

use App\Traits\HasPublicUuids;
use function config;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory, HasPublicUuids;

    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'roleId', 'userId');
    }

    public static function findByName(string $name): Role
    {
        return Cache::remember(
            'role_'.Str::snake($name),
            config('app.cache_duration_in_secs'),
            function () use ($name) {
                return Role::whereName($name)->sole();
            });
    }
}
