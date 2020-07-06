<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $project;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = create('User');
        $this->project = create('Project');
    }

    /** @test */
    public function anyone_can_view_all_projects()
    {
        $this->json('GET', route('api.projects.index'))
            ->assertStatus(200)
            ->assertSee($this->project->title);
    }

    /** @test */
    public function anyone_can_view_a_single_project()
    {
        $this->json('GET', route('api.projects.show', [
            'project' => $this->project
        ]))
            ->assertStatus(200)
            ->assertSee($this->project->title);
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
            'project' => $this->project
        ]), [])
            ->assertstatus(401);
    }

    /** @test */
    public function a_guest_cannot_archive_a_project()
    {
        $this->json('DELETE', route('api.projects.archive', [
            'id' => $this->project->id
        ]))
            ->assertStatus(401);
    }

    /** @test */
    public function a_guest_cannot_restore_a_project()
    {
        $this->json('PATCH', route('api.projects.restore', [
            'id' => $this->project->id
        ]), [])
            ->assertStatus(401);
    }

    /** @test */
    public function a_guest_cannot_destroy_a_project()
    {
        $this->json('DELETE', route('api.projects.destroy', [
            'id' => $this->project->id
        ]))
            ->assertstatus(401);
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
                'project' => $this->project
            ]), [])
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_archive_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('DELETE', route('api.projects.archive', [
                'id' => $this->project->id
            ]))
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_restore_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('PATCH', route('api.projects.restore', [
                'id' => $this->project->id
            ]), [])
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_destroy_a_project()
    {
        $this->actingAs($this->user, 'api')
            ->json('DELETE', route('api.projects.destroy', [
                'id' => $this->project->id
            ]))
            ->assertStatus(403);
    }

    /** @test */
    public function the_project_owner_can_update_the_project()
    {
        $this->actingAs($this->project->owner, 'api')
            ->json('PATCH', route('api.projects.update', [
                'project' => $this->project
            ]), $attributes = raw('Project'))
            ->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $this->project->id,
            'owner_id' => $this->project->owner_id,
            'title' => $attributes['title'],
            'description' => $attributes['description'],
        ]);
    }

    /** @test */
    public function the_project_owner_can_archive_the_project()
    {
        $this->actingAs($this->project->owner, 'api')
            ->json('DELETE', route('api.projects.archive', [
                'id' => $this->project->id
            ]))
            ->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $this->project->id,
            'deleted_at' => $this->project->fresh()->deleted_at
        ]);
    }

    /** @test */
    public function the_project_owner_can_restore_the_project()
    {
        $this->project->delete();

        $this->actingAs($this->project->owner, 'api')
            ->json('PATCH', route('api.projects.restore', [
                'id' => $this->project->id
            ]))
            ->assertStatus(200);

        $this->assertNull($this->project->fresh()->deleted_at);
    }

    /** @test */
    public function the_project_owner_can_destroy_the_project()
    {
        $this->actingAs($this->project->owner, 'api')
            ->json('DELETE', route('api.projects.destroy', [
                'id' => $this->project->id
            ]))
            ->assertStatus(200);

        $this->assertDatabaseMissing('projects', [
            'id' => $this->project->id
        ]);
    }
}
