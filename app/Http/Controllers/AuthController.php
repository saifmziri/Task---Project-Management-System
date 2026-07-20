<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($credentials);

        return response()->json([
            'message' => 'User logged in successfully',
            'token'   => $result['token'],
            'user'    => new UserResource($result['user'])
        ], Response::HTTP_OK);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        // لا نرجع token قبل التفعيل
        return response()->json([
            'message' => 'User registered successfully. Please check your email to verify your account.',
            'user'    => new UserResource($result['user'])
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logout successful'], Response::HTTP_OK);
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
        ], Response::HTTP_OK);
    }

    public function resendVerificationEmail(ResendVerificationRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $this->authService->resendVerificationEmail($email);

        return response()->json([
            'message' => 'إذا كان هذا البريد متاحاً وغير موثق لدينا، فقد تم إرسال رابط تفعيل جديد إليه.'
        ], Response::HTTP_OK);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword($request->user(), $request->validated());

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح.'
        ], Response::HTTP_OK);
    }
}