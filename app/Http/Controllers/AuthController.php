<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register User",
     *     description="Register User",
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "date_of_birth", "gender", "email", "password", "password_confirmation"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", example="Male"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="secret"),
     *             @OA\Property(property="password_confirmation", type="string", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully.")
     *         )
     *     ),
     * )
     */
    public function register(Request $request)
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
                'confirmed' // Must match the 'password_confirmation' field
            ],
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);
        return $this->successResponse(['user' => $user], ['User registered successfully.']);

        // return response()->json(['message' => 'User registered successfully.'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="User Login",
     *     description="Logs a user into the application",
     *     operationId="loginUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="secret"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your_access_token_here"),
     *             @OA\Property(property="message", type="string", example="Login successful.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function login(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Retrieve the user by email
        $user = User::where('email', $validatedData['email'])->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        // Create a new token for the user
        // $token = $user->createToken('YourAppName')->plainTextToken;
        // After successful authentication
        $token = Str::random(256); // You can set your desired length here

        // Save the token associated with the user
        $user->tokens()->create([
            'name' => 'YourAppName',
            'token' => hash('sha256', $token),
        ]);

        // return response()->json(['token' => $token, 'message' => 'Login successful.'], 200);
        return $this->successResponse(['token' => $token], 'Login successful.');
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Logout User",
     *     description="Logout User",
     *     operationId="ogout",
     *     @OA\Response(
     *         response=201,
     *         description="User Logout successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Logout successfully.")
     *         )
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->successResponse('', ['User Logout successfully.']);
    }
}
