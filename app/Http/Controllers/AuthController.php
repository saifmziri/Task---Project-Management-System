<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected UserService $userService;

    // 1. هنا يتم حقن الـ Service الجديد تلقائياً داخل الـ Controller
    public function __construct(AuthService $authService,UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
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
        $request->validate([
            'token' => 'required|string',
        ]);
    
        $result = $this->authService->verifyEmail($request->token);
    
        return response()->json([
            'message' => 'Email verified successfully',
            'token'   => $result['token'],
            'user'    => new UserResource($result['user'])
        ], 200);
    }

    public function resendVerificationEmail(ResendVerificationRequest $request): JsonResponse
    {
        // استخراج البريد الإلكتروني بعد التحقق منه
        $email = $request->validated('email');

        // استدعاء الخدمة لإعادة الإرسال
        $this->userService->resendVerificationEmail($email);

        // 🌟 إرجاع استجابة موحدة وناجحة دائماً للحماية من هجمات فحص الحسابات (Security Best Practice)
        return response()->json([
            'message' => 'إذا كان هذا البريد متاحاً وغير موثق لدينا، فقد تم إرسال رابط تفعيل جديد إليه.'
        ], Response::HTTP_OK);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        // إرسال المستخدم الحالي والبيانات الموثقة للـ Service
        $this->userService->changePassword($request->user(), $request->validated());

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح.'
        ], Response::HTTP_OK);
    }


}