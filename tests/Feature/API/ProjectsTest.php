<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $publicProject;

    protected $internalProject;

    protected $privateProject;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = create('User');

        $this->publicProject = create('Project', [
            'visibility' => 'public'
        ]);

        $this->internalProject = create('Project', [
            'visibility' => 'internal'
        ]);

        $this->privateProject = create('Project', [
            'visibility' => 'private'
        ]);
    }

    /** @test */
    public function anyone_can_view_all_public_projects()
    {
        $this->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertSee($this->publicProject->title);
    }

    /** @test */
    public function anyone_can_view_a_public_project()
    {
        $this->json('GET', route('api.projects.show', [
            'project' => $this->publicProject
        ]))
            ->assertStatus(200)
            ->assertSee($this->publicProject->title);
    }

    /** @test */
    public function anyone_can_filter_a_public_project_by_owner()
    {
        $randomProject = create('Project', [
            'visibility' => 'public'
        ]);

        $this->json('GET', route('api.projects.index', [
            'by' => $this->publicProject->owner_id
        ]))
            ->assertStatus(200)
            ->assertSee($this->publicProject->title)
            ->assertDontSee($randomProject->title);
    }

    /** @test */
    public function a_guest_cannot_view_any_internal_projects()
    {
        $this->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertDontSee($this->internalProject->title);
    }

    /** @test */
    public function a_guest_cannot_view_any_private_projects()
    {
        $this->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertDontSee($this->internalProject->title);
    }

    /** @test */
    public function a_guest_cannot_view_an_internal_project()
    {
        $this->json('GET', route('api.projects.show', [
            'project' => $this->internalProject
        ]))
            ->assertStatus(403);
    }

    /** @test */
    public function a_guest_cannot_view_a_private_project()
    {
        $this->json('GET', route('api.projects.show', [
            'project' => $this->privateProject
        ]))
            ->assertStatus(403);
    }

    /** @test */
    public function a_guest_cannot_create_a_project()
    {
        $this->json('POST', route('api.projects.store'), [])
            ->assertstatus(401);
    }

    /** @test */
    public function a_guest_cannot_update_a_project()
    {
        $this->json('PATCH', route('api.projects.update', [
            'project' => $this->publicProject
        ]), [])
            ->assertstatus(401);
    }

    /** @test */
    public function a_guest_cannot_archive_a_project()
    {
        $this->json('DELETE', route('api.projects.archive', [
            'id' => $this->publicProject->id
        ]))
            ->assertStatus(401);
    }

    /** @test */
    public function a_guest_cannot_restore_a_project()
    {
        $this->json('PATCH', route('api.projects.restore', [
            'id' => $this->publicProject->id
        ]), [])
            ->assertStatus(401);
    }

    /** @test */
    public function a_guest_cannot_destroy_a_project()
    {
        $this->json('DELETE', route('api.projects.destroy', [
            'id' => $this->publicProject->id
        ]))
            ->assertstatus(401);
    }

    /** @test */
    public function a_user_cannot_view_any_internal_projects()
    {
        $this->actingAs($this->user, 'api')
            ->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertDontSee($this->internalProject);
    }

    /** @test */
    public function a_user_cannot_view_any_private_projects()
    {
        $this->actingAs($this->user, 'api')
            ->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertDontSee($this->privateProject);
    }

    /** @test */
    public function a_user_cannot_view_an_internal_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('GET', route('api.projects.show', [
                'project' => $this->internalProject
            ]))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_view_a_private_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('GET', route('api.projects.show', [
                'project' => $this->privateProject
            ]))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_create_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('POST', route('api.projects.store'), $attributes = raw('Project'))
            ->assertStatus(201);

        $this->assertDatabaseHas('projects', [
            'owner_id' => $this->user->id,
            'title' => $attributes['title'],
            'description' => $attributes['description'],
        ]);
    }

    /** @test */
    public function a_user_cannot_update_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('PATCH', route('api.projects.update', [
                'project' => $this->publicProject
            ]), [])
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_archive_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('DELETE', route('api.projects.archive', [
                'id' => $this->publicProject->id
            ]))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_restore_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('PATCH', route('api.projects.restore', [
                'id' => $this->publicProject->id
            ]), [])
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_destroy_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('DELETE', route('api.projects.destroy', [
                'id' => $this->publicProject->id
            ]))
            ->assertStatus(403);
    }

    /** @test */
    public function the_project_owner_can_view_any_of_their_internal_projects()
    {
        $this->actingAs($this->internalProject->owner, 'api')
            ->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertSee($this->internalProject->title);
    }

    /** @test */
    public function the_project_owner_can_view_any_of_their_private_projects()
    {
        $this->actingAs($this->privateProject->owner, 'api')
            ->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertSee($this->privateProject->title);
    }

    /** @test */
    public function the_project_owner_can_view_their_internal_project()
    {
        $this->actingAs($this->internalProject->owner, 'api')
            ->json('GET', route('api.projects.show', [
                'project' => $this->internalProject
            ]))
            ->assertStatus(200)
            ->assertSee($this->internalProject->title);
    }

    /** @test */
    public function the_project_owner_can_view_their_private_project()
    {
        $this->actingAs($this->privateProject->owner, 'api')
            ->json('GET', route('api.projects.show', [
                'project' => $this->privateProject
            ]))
            ->assertStatus(200)
            ->assertSee($this->privateProject->title);
    }

    /** @test */
    public function the_project_owner_can_update_the_project()
    {
        $this->actingAs($this->publicProject->owner, 'api')
            ->json('PATCH', route('api.projects.update', [
                'project' => $this->publicProject
            ]), $attributes = raw('Project'))
            ->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $this->publicProject->id,
            'owner_id' => $this->publicProject->owner_id,
            'title' => $attributes['title'],
            'description' => $attributes['description'],
        ]);
    }

    /** @test */
    public function the_project_owner_can_archive_the_project()
    {
        $this->actingAs($this->publicProject->owner, 'api')
            ->json('DELETE', route('api.projects.archive', [
                'id' => $this->publicProject->id
            ]))
            ->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $this->publicProject->id,
            'deleted_at' => $this->publicProject->fresh()->deleted_at
        ]);
    }

    /** @test */
    public function the_project_owner_can_restore_the_project()
    {
        $this->publicProject->delete();

        $this->actingAs($this->publicProject->owner, 'api')
            ->json('PATCH', route('api.projects.restore', [
                'id' => $this->publicProject->id
            ]))
            ->assertStatus(200);

        $this->assertNull($this->publicProject->fresh()->deleted_at);
    }

    /** @test */
    public function the_project_owner_can_destroy_the_project()
    {
        $this->actingAs($this->publicProject->owner, 'api')
            ->json('DELETE', route('api.projects.destroy', [
                'id' => $this->publicProject->id
            ]))
            ->assertStatus(200);

        $this->assertDatabaseMissing('projects', [
            'id' => $this->publicProject->id
        ]);
    }
}
