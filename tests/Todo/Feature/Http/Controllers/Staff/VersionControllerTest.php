<?php

namespace Tests\Feature\Http\Controllers\Staff;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Staff\VersionController
 */
class VersionControllerTest extends TestCase
{
    /**
     * @test
     */
    public function check_version_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $user = factory(\App\Models\User::class)->create();

        $response = $this->actingAs($user)->get('dashboard/UNIT3D');

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}