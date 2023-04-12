<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use function config;

class Role extends Model
{
    use HasFactory;

    public function users(): BelongsToMany
    {
//        return $this->belongsToMany(User::class);
        return $this->belongsToMany(User::class, 'role_user', 'roleId', 'userId');
    }

    public static function findByName(string $name): static
    {
        return Cache::remember(
            'role_' . Str::snake($name),
            config('cache_duration_in_secs'),
            function () use ($name) {
                return Role::whereName($name)->sole();
            });
    }
}
