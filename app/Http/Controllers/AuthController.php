<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    // 1. هنا يتم حقن الـ Service الجديد تلقائياً داخل الـ Controller
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * تسجيل الدخول
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // استدعاء دالة الـ login من الـ Service
        $result = $this->authService->login($credentials);

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $result['token'],
            'user' => new UserResource($result['user']) 
        ], 200);
    }

    /**
     * التسجيل (هنا السحر! بمجرد استدعاء الدالة، سيقوم الـ Service بإرسال الإيميل تلقائياً)
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
    
        return response()->json([
            'message' => 'User registered successfully. Please check your email to verify your account.',
            'token' => $result['token'],
            'user' => new UserResource($result['user'])
        ], 201);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request): JsonResponse
    {
        // نمرر كائن المستخدم الحالي المتصل $request->user() إلى الـ Service ليقوم بحذف التوكن
        $this->authService->logout($request->user());
        
        return response()->json(['message' => 'Logout successful'], 200); 
    }
    public function verifyEmail(Request $request): JsonResponse
    {
    // 1. التحقق من أن التوكن تم إرساله في الطلب
    $request->validate([
        'token' => 'required|string'
    ]);

    // 2. تمرير التوكن إلى الـ Service ليقوم بالتفعيل في قاعدة البيانات
    $result = $this->authService->verifyEmail($request->token);

    // 3. إرجاع النتيجة وتوكن الدخول الجديد للـ React
    return response()->json([
        'message' => 'Email verified successfully',
        'token' => $result['token'],
        'user' => new UserResource($result['user'])
    ], 200);
    }
}