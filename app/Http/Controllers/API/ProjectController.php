<?php

namespace App\Http\Controllers\API;

use App\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            return Auth::user()->projects;
        }

        return Project::where('visibility', 'public')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateAttributes();

        $project = Project::create([
            'owner_id' => auth()->user()->id,
            'title' => request('title'),
            'description' => request('description'),
        ]);

        return response($project, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return $project;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $this->validateAttributes();

        $project->update([
            'title' => request('title'),
            'description' => request('description'),
        ]);

        return response($project, 200);
    }

    /**
     * Soft delete the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $project = Project::where('id', $id)
            ->first();

        $this->authorize('delete', $project);

        $project->delete();
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $project = Project::onlyTrashed()
            ->where('id', $id)
            ->first();

        $this->authorize('restore', $project);

        $project->restore();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::withTrashed()
            ->where('id', $id)
            ->first();

        $this->authorize('forceDelete', $project);

        $project->forceDelete();
    }

    /**
     * Validates the attributes given against requirements
     *
     * @return void
     */
    public function validateAttributes()
    {
        request()->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
    }
}
