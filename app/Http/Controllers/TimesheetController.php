<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the timesheets.
     *
     * @OA\Get(
     *     path="/api/timesheets",
     *     tags={"Timesheets"},
     *     summary="Get list of timesheets",
     *     description="Returns list of timesheets",
     *     operationId="getTimesheetsList",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filter timesheets by user ID"
     *     ),
     *     @OA\Parameter(
     *         name="project_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filter timesheets by project ID"
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
        $query = Timesheet::with(['user', 'project']); // Load associated user and project data

        // Filtering based on request
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $timesheets = $query->get();

        return $this->successResponse(['timesheets' => $timesheets], ['Get All Timesheets']);
    }

    /**
     * Store a newly created timesheet in storage.
     *
     * @OA\Post(
     *     path="/api/timesheets",
     *     tags={"Timesheets"},
     *     summary="Create a new timesheet",
     *     description="Store a newly created timesheet in storage",
     *     operationId="storeTimesheet",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"task_name", "date", "hours", "user_id", "project_id"},
     *             @OA\Property(property="task_name", type="string", example="Development Task"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-10-10"),
     *             @OA\Property(property="hours", type="number", format="float", example=5),
     *             @OA\Property(property="user_id", type="integer", example=1, description="The ID of the user logging the timesheet"),
     *             @OA\Property(property="project_id", type="integer", example=1, description="The ID of the project related to the timesheet")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Timesheet created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="task_name", type="string", example="Development Task"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="hours", type="number", format="float"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="project_id", type="integer"),
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
            'task_name' => 'required|string|max:255',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        // Check if the user has already logged a timesheet for this project on the same date
        $existingTimesheet = Timesheet::where('user_id', $validatedData['user_id'])
            ->where('project_id', $validatedData['project_id'])
            // ->where('date', $validatedData['date'])
            ->first();

        if ($existingTimesheet) {
            return $this->errorResponse('This user has already logged a timesheet for this project', 400);
        }

        // Create the timesheet if validation passes
        $timesheet = Timesheet::create($validatedData);

        return $this->successResponse(['timesheet' => $timesheet], ['Timesheet Created Successfully']);
    }

    /**
     * Display the specified timesheet.
     *
     * @OA\Get(
     *     path="/api/timesheets/{id}",
     *     tags={"Timesheets"},
     *     summary="Get a timesheet by ID",
     *     description="Display the specified timesheet",
     *     operationId="showTimesheet",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the timesheet"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Timesheet retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="task_name", type="string", example="Development Task"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="hours", type="number", format="float"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="project_id", type="integer"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="Timesheet not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function show($id)
    {
        // Fetch the timesheet with associated user and project
        $timesheet = Timesheet::with(['user', 'project'])->findOrFail($id);

        return $this->successResponse(['timesheet' => $timesheet], ['Timesheet Fetched Successfully']);
    }

    /**
     * Update the specified timesheet in storage.
     *
     * @OA\Put(
     *     path="/api/timesheets/update",
     *     tags={"Timesheets"},
     *     summary="Update an existing timesheet",
     *     description="Update the specified timesheet in storage",
     *     operationId="updateTimesheet",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="task_name", type="string", example="Updated Task"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-10-10"),
     *             @OA\Property(property="hours", type="number", format="float", example=4),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="project_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Timesheet updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="task_name", type="string", example="Updated Task"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="hours", type="number", format="float"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="project_id", type="integer"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="Timesheet not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:timesheets,id',
            'task_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'hours' => 'sometimes|required|numeric|min:0',
            'user_id' => 'sometimes|required|exists:users,id',
            'project_id' => 'sometimes|required|exists:projects,id',
        ]);

        $timesheet = Timesheet::findOrFail($validatedData['id']);
        $timesheet->update($validatedData);

        return $this->successResponse(['timesheet' => $timesheet], ['Timesheet Updated Successfully']);
    }

    /**
     * Remove the specified timesheet from storage.
     *
     * @OA\Delete(
     *     path="/api/timesheets/delete",
     *     tags={"Timesheets"},
     *     summary="Delete a timesheet",
     *     description="Remove the specified timesheet from storage",
     *     operationId="deleteTimesheet",
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
    public function destroy($id)
    {
        $timesheet = Timesheet::findOrFail($id);
        $timesheet->delete();

        return $this->successResponse('', ['Timesheet Deleted Successfully']);
    }
}
