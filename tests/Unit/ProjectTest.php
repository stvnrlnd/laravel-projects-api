<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_made()
    {
        $this->assertInstanceOf('App\Project', make('Project'));
    }

    /** @test */
    public function it_can_be_created()
    {
        $this->assertDatabaseHas('users', [ 'id' => create('User')->id ]);
    }

    /** @test */
    public function it_can_be_archived()
    {
        $project = create('Project');

        $project->delete();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'deleted_at' => $project->fresh()->deleted_at
        ]);
    }

    /** @test */
    public function it_can_be_restored()
    {
        $project = create('Project', [
            'deleted_at' => \Carbon\Carbon::now()
        ]);

        $project->restore();

        $this->assertNull($project->fresh()->deleted_at);
    }

    /** @test */
    public function it_can_be_destroyed()
    {
        $project = create('Project');

        $project->forceDelete();

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
    }

    /** @test */
    public function it_belongs_to_an_owner()
    {
        $this->assertInstanceOf('App\User', make('Project')->owner);
    }
}
