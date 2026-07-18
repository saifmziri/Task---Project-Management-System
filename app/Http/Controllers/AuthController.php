<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if(!Auth::attempt($request->only('email', 'password')))
        return response()->json(['message' => 'Invalid login credentials'], 401);

        $user=User::where('email', $request->email)->firstOrFail();

        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
        'message' => 'User logged in successfully',
        'token' => $token,
        'user' => new UserResource($user) 
    ], 200);
    }

    public function register(RegisterRequest $request)
    {
        // 1. جلب البيانات التي مرت من الفحص بنجاح
        $validatedData = $request->validated();
    
        // 2. تشفير الباسورد يدوياً لضمان الأمان والتوافق مع الـ Login
        $validatedData['password'] = Hash::make($validatedData['password']);
    
        $user = User::create($validatedData);
    
        // 4. توليد التوكن لإدخال المستخدم مباشرة بعد التسجيل
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // 5. إرجاع النتيجة مع كود 201 (Created)
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }
}