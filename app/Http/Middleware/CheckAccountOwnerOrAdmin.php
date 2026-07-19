<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAccountOwnerOrAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentUser = Auth::user();
        
        // 🎯 السحر هنا: لارافل يمسك الـ ID من الرابط تلقائياً 
        // اسم البارامتر يعتمد على اسم المتغير في الـ Resource وهو 'user'
        $userIdFromRoute = $request->route('user'); 

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 🧠 نفس الشرط الذكي الخاص بك:
        // إذا لم يكن أدمن، وفي نفس الوقت المعرف في الرابط لا يطابق معرفه الشخصي
        if ($currentUser->role?->role_name !== 'admin' && (string)$currentUser->id !== (string)$userIdFromRoute) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.'
            ], 403);
        }

        return $next($request);
    }
}