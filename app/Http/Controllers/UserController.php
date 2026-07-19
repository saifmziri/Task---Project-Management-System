<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ChangeUserStatusRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    // حقن الـ Service داخل الـ Controller عبر الـ Constructor
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role')->get();
        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user->load('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        // 🎯 تم تنظيف المتغيرات الزائدة لأن الـ Middleware تولى الحماية مسبقاً
        $userToUpdate = User::findOrFail($id); 

        $updatedUser = $this->userService->updateUser($userToUpdate, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => new UserResource($updatedUser->load('role'))
        ], 200);
    }    

    /**
     * Change user account status (Active/Inactive)
     */
    public function changeStatus(ChangeUserStatusRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $updatedUser = $this->userService->changeStatus($user, $request->validated()['status']);

        // إذا عادت السيرفس بـ null، فهذا يعني أن الحالة متطابقة بالفعل
        if (!$updatedUser) {
            return response()->json([
                'message' => "User is already {$request->status}."
            ], 400);
        }

        return response()->json([
            'message' => "User status changed to {$request->status} successfully",
            'user'    => new UserResource($updatedUser->load('role'))
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 🎯 تم تنظيف الكود وحذف الـ $request و $currentUser غير المستخدمين
        $userToDelete = User::findOrFail($id);
    
        $this->userService->deleteUser($userToDelete);
    
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}