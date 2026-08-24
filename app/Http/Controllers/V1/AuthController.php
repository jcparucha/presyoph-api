<?php

namespace App\Http\Controllers\V1;

use App\Contracts\AuthServiceInterface;
use App\Enums\CredentialStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthServiceInterface $authService) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        if ($this->authService->register($request->validated())) {
            return response()->json(['message' => __('auth.register_success')]);
        }

        return response()->json(['message' => __('auth.register_fail')], 500);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        if ($request->user()) {
            return response()->json(['message' => __('auth.is_auth')]);
        }

        [$data, $statusCode] = $this->_generateLoginResponse($this->authService->login($request->all()));

        return response()->json($data, $statusCode);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => 'success']);
    }

    private function _generateLoginResponse(string $status): array
    {
        $statusCode = 200;
        $data['messages'] = CredentialStatus::VALID->message();

        if ($status !== CredentialStatus::VALID->name) {
            $statusCode = 422;
            $message =
                $status === CredentialStatus::INVALID->name
                    ? CredentialStatus::INVALID->message()
                    : CredentialStatus::NON_EXISTENT->message();

            $data['messages'] = $message;
            $data['errors']['system'][] = $message;
        }

        return [$data, $statusCode];
    }
}
