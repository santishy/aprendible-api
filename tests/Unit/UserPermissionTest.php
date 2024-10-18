<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

class UserPermissionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_assign_permission_to_a_user()
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }

    public function test_cannot_assign_the_same_permission_twice()
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);
        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }
}
