<?php

namespace Tests\Unit;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function a_user_could_have_many_roles()
    {
        /** @var User $user */
        $user = User::factory()
            ->has(Role::factory()->count(2))
            ->create();

        $this->assertCount(2, $user->roles);

        /** @var Role $role */
        $role = $user->roles->random();

        $this->assertInstanceOf(Role::class, $role);
    }

    /**
     * @test
     */
    public function a_user_can_be_assigned_with_a_specific_role()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $user->assignRole(RoleEnum::Admin);

        $this->assertDatabaseHas('role_user', [
            'userId' => $user->id,
            'roleId' => Role::findByName(RoleEnum::Admin->value)->id,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_be_removed_from_a_specific_role()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $user->assignRole(RoleEnum::Admin);

        $this->assertDatabaseHas('role_user', [
            'userId' => $user->id,
            'roleId' => Role::findByName(RoleEnum::Admin->value)->id,
        ]);

        $user->removeRole(RoleEnum::Admin);

        $this->assertDatabaseMissing('role_user', [
            'userId' => $user->id,
            'roleId' => Role::findByName(RoleEnum::Admin->value)->id,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_be_checked_for_a_specific_role()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $user->assignRole(RoleEnum::Admin);

        $this->assertTrue($user->hasRole(RoleEnum::Admin));
    }

    /**
     * @test
     */
    public function admins_and_editors_can_be_retrieved()
    {
        User::factory()
            ->count(10)
            ->create()->each(function (User $user) {
                $user->assignRole(RoleEnum::Admin);
            });

        User::factory()
            ->count(15)
            ->create()->each(function (User $user) {
                $user->assignRole(RoleEnum::Editor);
            });

        $this->assertDatabaseCount('users', 25);

        $this->assertCount(10, User::admin()->get());

        $this->assertCount(15, User::editor()->get());
    }
}
