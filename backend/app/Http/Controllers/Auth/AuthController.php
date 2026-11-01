<?php

namespace App\Http\Controllers\Auth;

use App\Constants\HttpStatusConstant;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\InvalidResetTokenException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Modules\Auth\ForgotPassword\Services\ForgotPasswordService;
use App\Modules\Auth\Login\Services\LoginService;
use App\Modules\Auth\Logout\Services\LogoutService;
use App\Modules\Auth\Register\Services\RegisterService;
use App\Modules\Auth\ResetPassword\Services\ResetPasswordService;
use App\Utils\CommonUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $registerService = app(RegisterService::class);
        $registerBo = $registerService->prepareBo($registerRequest);

        return response()->json($registerService->add($registerBo), HttpStatusConstant::CREATED);
    }

    public function login(LoginRequest $loginRequest): JsonResponse
    {
        try {
            $loginService = app(LoginService::class);
            $email = $loginRequest->input('email');
            $password = $loginRequest->input('password');

            return response()->json($loginService->add($email, $password), HttpStatusConstant::OK);
        } catch (InvalidCredentialsException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::UNAUTHORIZED,
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $logoutService = app(LogoutService::class);

        return response()->json($logoutService->logout($request), HttpStatusConstant::OK);
    }

    public function forgotPassword(ForgotPasswordRequest $forgotPasswordRequest): JsonResponse
    {
        $forgotPasswordService = app(ForgotPasswordService::class);
        $email = $forgotPasswordRequest->string('email')->toString();

        return response()->json($forgotPasswordService->send($email), HttpStatusConstant::OK);
    }

    public function resetPassword(ResetPasswordRequest $resetPasswordRequest): JsonResponse
    {
        try {
            $resetPasswordService = app(ResetPasswordService::class);
            $email = $resetPasswordRequest->string('email')->toString();
            $token = $resetPasswordRequest->string('token')->toString();
            $password = $resetPasswordRequest->string('password')->toString();

            return response()->json(
                $resetPasswordService->reset($email, $token, $password),
                HttpStatusConstant::OK,
            );
        } catch (InvalidResetTokenException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }
    }
}
