<?php

namespace Tests;

use App\Enums\Role;
use App\Enums\Role as RoleEnum;
use App\Models\User;
use Database\Seeders\Production\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function createEditor(): User
    {
        /** @var User $editor */
        $editor = User::factory()->create();

        $editor->assignRole(RoleEnum::Editor);

        return $editor;
    }

    public function createAdmin(): User
    {
        /** @var User $admin */
        $admin = User::factory()->create();

        $admin->assignRole(RoleEnum::Admin);

        return $admin;
    }
}
