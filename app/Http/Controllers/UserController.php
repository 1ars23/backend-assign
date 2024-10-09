<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *    title="Astudio Backend Assessment (Laravel)",
 *    version="1.0.0",
 * )
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get list of users",
     *     description="Returns list of users",
     *     operationId="getUsersList",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter users by first name"
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter users by gender"
     *     ),
     *     @OA\Parameter(
     *         name="date_of_birth",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Filter users by date of birth"
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
        // Query to get users with their associated projects and timesheets
        $query = User::with(['projects.timesheets' => function ($query) {
            // This will filter timesheets to only include those related to the current user
            $query->where('user_id', $this->user()->id);
        }]);

        // Filtering based on request
        if ($request->first_name) {
            $query->where('first_name', $request->first_name);
        }
        if ($request->gender) {
            $query->where('gender', $request->gender);
        }
        if ($request->date_of_birth) {
            $query->where('date_of_birth', $request->date_of_birth);
        }

        $users = $query->get();

        // Hide pivot data for each project in the users
        foreach ($users as $user) {
            $user->projects->makeHidden('pivot');
            foreach ($user->projects as $project) {
                $project->timesheets->makeHidden('user_id');
            }
        }

        return $this->successResponse(['users' => $users], ['Get All Users']);
    }






    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Store a newly created user in storage",
     *     operationId="storeUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "date_of_birth", "gender", "email", "password"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password123!"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:8', // Minimum 8 characters
                'regex:/[A-Z]/', // At least one uppercase letter
                'regex:/[a-z]/', // At least one lowercase letter
                'regex:/[0-9]/', // At least one numeric digit
                'regex:/[@$!%*#?&]/', // At least one special character
            ],
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        return $this->successResponse(['users' => $user], ['User Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Get a user by ID",
     *     description="Display the specified user",
     *     operationId="showUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the user"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function show($id)
    {
        // Fetch user with associated projects and their timesheets
        $user = User::with(['projects.timesheets' => function ($query) use ($id) {
            // This will filter timesheets to only include those related to the current user
            $query->where('user_id', $id);
        }])->findOrFail($id);

        // Hide the pivot data from the projects
        $user->projects->makeHidden('pivot');
        foreach ($user->projects as $project) {
            $project->timesheets->makeHidden('user_id');
        }

        return $this->successResponse(['user' => $user], ['User Fetched Successfully']);
    }




    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/users/update",
     *     tags={"Users"},
     *     summary="Update an existing user",
     *     description="Update the specified user in storage",
     *     operationId="updateUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password123!"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *            @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'email' => 'required|email|max:255|unique:users,email,' . $request->id,
            'password' => [
                'required',
                'string',
                'min:8', // Minimum 8 characters
                'regex:/[A-Z]/', // At least one uppercase letter
                'regex:/[a-z]/', // At least one lowercase letter
                'regex:/[0-9]/', // At least one numeric digit
                'regex:/[@$!%*#?&]/', // At least one special character
            ],
        ]);

        $user = User::findOrFail($request->id);
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => isset($request->password) ? Hash::make($request->password) : $user->password,
        ]);

        return $this->successResponse(['users' => $user], ['User Updated Successfully']);
    }


    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Post(
     *     path="/api/users/delete",
     *     tags={"Users"},
     *     summary="Delete a user by ID",
     *     description="Remove the specified user from storage",
     *     operationId="deleteUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1, description="The ID of the user to delete")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     security={{"sanctum": {}}}
     * )
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->id);

        // Delete associated timesheets
        $user->timesheets()->delete();

        // Delete the user
        $user->delete();

        return $this->successResponse('', ['User deleted successfully']);
    }
}
