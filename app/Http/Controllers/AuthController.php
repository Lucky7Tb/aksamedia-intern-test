<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            if (! Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'username or password not found',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $request->user()->createToken('auth_token');

            return response()->json([
                'status' => 'success',
                'message' => 'success login',
                'data' => [
                    'token' => $token->plainTextToken,
                    'admin' => $request->user(),
                ],
            ], Response::HTTP_OK);

        } catch (ValidationException $th) {
            throw $th;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'error login',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success logout',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'error logout',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
