<?php

namespace App\Http\Controllers;

use App\Contracts\AuthServiceInterface;
use App\Enums\CredentialStatus;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private AuthServiceInterface $authService) {}

    public function register(AuthRequest $request): JsonResponse
    {
        $this->authService->register($request->validated());

        return response()->json(['message' => 'success']);
    }

    public function login(AuthRequest $request): JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'message' => "You're already authenticated.",
            ]);
        }

        $status = $this->authService->login($request->all());

        $statusCode = 200;
        $data['messages'] = CredentialStatus::VALID->message();

        if ($status !== CredentialStatus::VALID->name) {
            $statusCode = 422;

            $data['messages'] =
                $status === CredentialStatus::INVALID->name
                    ? CredentialStatus::INVALID->message()
                    : CredentialStatus::NON_EXISTENT->message();

            $data['errors']['system'][] =
                $status === CredentialStatus::INVALID->name
                    ? CredentialStatus::INVALID->message()
                    : CredentialStatus::NON_EXISTENT->message();
        }

        return response()->json($data, $statusCode);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => 'success']);
    }
}
