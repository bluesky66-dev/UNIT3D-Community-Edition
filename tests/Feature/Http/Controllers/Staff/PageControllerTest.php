<?php

namespace Tests\Feature\Http\Controllers\Staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Staff\PageController
 */
class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function create_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$user = factory(\App\Models\User::class)->create();

$response = $this->actingAs($user)->get(route('staff.pages.create'));

$response->assertOk();
$response->assertViewIs('Staff.page.create');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function destroy_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$page = factory(\App\Models\Page::class)->create();
$user = factory(\App\Models\User::class)->create();

$response = $this->actingAs($user)->delete(route('staff.pages.destroy', ['id' => $page->id]));

$response->assertRedirect(withSuccess('Page has been deleted successfully'));
$this->assertDeleted($staff);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$page = factory(\App\Models\Page::class)->create();
$user = factory(\App\Models\User::class)->create();

$response = $this->actingAs($user)->get(route('staff.pages.edit', ['id' => $page->id]));

$response->assertOk();
$response->assertViewIs('Staff.page.edit');
$response->assertViewHas('page');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$user = factory(\App\Models\User::class)->create();

$response = $this->actingAs($user)->get(route('staff.pages.index'));

$response->assertOk();
$response->assertViewIs('Staff.page.index');
$response->assertViewHas('pages');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$user = factory(\App\Models\User::class)->create();

$response = $this->actingAs($user)->post(route('staff.pages.store'), [
            // TODO: send request data
        ]);

$response->assertRedirect(withErrors($v->errors()));

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$page = factory(\App\Models\Page::class)->create();
$user = factory(\App\Models\User::class)->create();

$response = $this->actingAs($user)->post(route('staff.pages.update', ['id' => $page->id]), [
            // TODO: send request data
        ]);

$response->assertRedirect(withErrors($v->errors()));

        // TODO: perform additional assertions
    }

    // test cases...
}