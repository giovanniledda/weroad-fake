<?php

namespace Tests\Unit;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Production\RoleSeeder;
use Tests\TestCase;

class RoleTest extends TestCase
{
    /**
     * @test
     */
    public function a_role_could_have_many_associated_users()
    {
        $role = Role::factory()
            ->has(User::factory()->count(5))
            ->create();

        $this->assertCount(5, $role->users);

        $user = $role->users->random();

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function a_role_has_predefined_values()
    {
        $this->seed(RoleSeeder::class);

        $this->assertDatabaseCount('roles', 2);

        $this->seed(RoleSeeder::class);

        // testing seeder idempotence
        $this->assertDatabaseCount('roles', 2);

        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'editor',
        ]);
    }

    /**
     * @test
     */
    public function a_role_can_be_retrieved_by_name()
    {
        $this->seed(RoleSeeder::class);

        $adminRole = Role::findByName(RoleEnum::Admin->value);

        $this->assertEquals('admin', $adminRole->name);
    }
}
