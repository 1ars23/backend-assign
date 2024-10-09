<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // List all projects
    public function index(Request $request)
    {
        $query = Project::query();

        // Optional filters
        if ($request->has('name')) {
            $query->where('name', $request->name);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    // Show a single project
    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    // Create a new project
    public function store(Request $request)
    {
        $project = Project::create($request->all());
        return response()->json($project, 201);
    }

    // Update a project
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        return response()->json($project);
    }

    // Delete a project and related timesheets
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->timesheets()->delete();
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully'], 200);
    }
}
