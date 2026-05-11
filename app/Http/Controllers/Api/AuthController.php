<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6',
            // 'address'  => 'nullable|string|max:255',
            // 'profile'  => 'nullable|string',
            'role_id'  => 'required|exists:roles,id',
            // 'status'   => 'required|in:enable,disable'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            // 'address'  => $request->address,
            // 'profile'  => $request->profile,
            'role_id'  => $request->role_id,
            // 'status'   => $request->status,
        ]);

        // dd($user);

        // Create token
        $token = $user->createToken('token')->plainTextToken;

        $user->load('role');

        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }
    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        // ignore space
        $phone = preg_replace('/\s+/', '', $validated['phone']);
        // $phone = $validated['phone'];

        $user = User::with('role')->where('phone', $phone)->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid credentials.'],
            ]);
        }

        if ($user->status === 'disable') {
            throw ValidationException::withMessages([
                'status' => ['Your account has been disabled.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('AUTH-TOKEN')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Return the authenticated user.
     */
    public function me(){
        $user = User::with('role')
        ->where('id', Auth::id())
        ->first();

        if(!$user){
            return $this->errorResponse('User not found', 404);
        }

        return $this->successResponse($user);
    }
}
