<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    use IsAdmin;

    /**
     * Get all users (admin only)
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $role = $request->get('role');
        $status = $request->get('status');

        $users = User::with('role')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->whereHas('role', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate($perPage);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ]);
    }

    /**
     * Get single user details (admin only)
     */
    public function show($id)
    {
        $this->authorizeAdmin();

        $user = User::with('role', 'orders', 'cart')->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user,
        ]);
    }

    /**
     * Create new user (admin only)
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'status' => 'in:active,inactive',
            'address' => 'nullable|string',
            'profile' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user->load('role'),
        ], 201);
    }

    /**
     * Update user (admin only)
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => ['string', 'email', Rule::unique('users')->ignore($id)],
            'phone' => 'string|max:20',
            'role_id' => 'exists:roles,id',
            'status' => 'in:active,inactive',
            'address' => 'nullable|string',
            'profile' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Delete user (admin only)
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();

        if (auth()->id() === (int) $id) {
            return response()->json([
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Update user role (admin only)
     */
    public function updateRole(Request $request, $id)
    {
        $this->authorizeAdmin();

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => $user->load('role'),
        ]);
    }

    /**
     * Update user status (admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeAdmin();

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:enable,disable',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User status updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Reset user password (admin only)
     */
    public function resetPassword(Request $request, $id)
    {
        $this->authorizeAdmin();

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'User password reset successfully',
        ]);
    }
}
