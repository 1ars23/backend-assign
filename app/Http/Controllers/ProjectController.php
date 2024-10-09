<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/projects",
     *     tags={"Projects"},
     *     summary="Get list of projects",
     *     description="Returns list of projects",
     *     operationId="getProjectsList",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter projects by name"
     *     ),
     *     @OA\Parameter(
     *         name="department",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter projects by department"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */
    public function index(Request $request)
    {
        // Eager load users and their timesheets for all projects
        $query = Project::with(['users', 'timesheets']);

        // Filtering based on request
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->department) {
            $query->where('department', $request->department);
        }

        $projects = $query->get();

        // Hide pivot data from users
        foreach ($projects as $project) {
            foreach ($project->users as $user) {
                $user->makeHidden('pivot');
            }
        }

        return $this->successResponse(['projects' => $projects], ['Get All Projects']);
    }



    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/projects",
     *     tags={"Projects"},
     *     summary="Create a new project",
     *     description="Store a newly created project in storage",
     *     operationId="storeProject",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "department", "start_date", "end_date", "status"},
     *             @OA\Property(property="name", type="string", example="New Project"),
     *             @OA\Property(property="department", type="string", example="IT"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
     *             @OA\Property(property="status", type="string", example="active"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="New Project"),
     *             @OA\Property(property="department", type="string", example="IT"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string',
        ]);

        $project = Project::create($validatedData);

        return $this->successResponse(['project' => $project], ['Project Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/projects/{id}",
     *     tags={"Projects"},
     *     summary="Get a project by ID",
     *     description="Display the specified project",
     *     operationId="showProject",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the project"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="New Project"),
     *             @OA\Property(property="department", type="string", example="IT"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="Project not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function show($id)
    {
        // Eager load users and their timesheets for the specific project
        $project = Project::with(['users', 'timesheets'])->findOrFail($id);

        // Hide pivot data from users
        foreach ($project->users as $user) {
            $user->makeHidden('pivot');
        }

        return $this->successResponse(['project' => $project], ['Project Fetched Successfully']);
    }






    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/projects/update",
     *     tags={"Projects"},
     *     summary="Update an existing project",
     *     description="Update the specified project in storage",
     *     operationId="updateProject",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Updated Project"),
     *             @OA\Property(property="department", type="string", example="IT"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
     *             @OA\Property(property="status", type="string", example="active"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Updated Project"),
     *             @OA\Property(property="department", type="string", example="IT"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="Project not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:projects,id',
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string',
        ]);

        $project = Project::findOrFail($request->id);
        $project->update($request->all());

        return $this->successResponse(['project' => $project], ['Project Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Post(
     *     path="/api/projects/delete",
     *     tags={"Projects"},
     *     summary="Delete a project",
     *     description="Remove the specified project from storage",
     *     operationId="deleteProject",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Project deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Project not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:projects,id']);

        $project = Project::findOrFail($request->id);

        // Delete associated timesheets
        $project->timesheets()->delete();

        // Delete the project
        $project->delete();

        return $this->successResponse('', ['Project deleted successfully']);
    }


    /**
     * Assign a user to a project.
     *
     * @OA\Post(
     *     path="/api/projects/assign-user",
     *     tags={"Projects"},
     *     summary="Assign a user to a project",
     *     description="Assign a specified user to a project.",
     *     operationId="assignUserToProject",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1, description="The ID of the user to assign to the project"),
     *             @OA\Property(property="project_id", type="integer", example=1, description="The ID of the project to which the user will be assigned")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User assigned to project successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User assigned to project successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Project not found"),
     *     @OA\Response(response=400, description="Invalid user ID"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function assignUser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($validatedData['project_id']);
        $project->users()->attach($validatedData['user_id']);

        return $this->successResponse('', ['User assigned to project successfully.']);
    }
}
