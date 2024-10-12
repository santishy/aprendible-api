<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

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
