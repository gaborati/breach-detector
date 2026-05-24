<?php

namespace App\Http\Controllers;

use App\Services\HibpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PasswordController extends Controller
{
    public function __construct(
        private HibpService $hibpService
    ) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:1'
        ]);

        $count = $this->hibpService->checkPassword($request->password);

        return response()->json([
            'breached' => $count > 0,
            'count' => $count,
            'message' => $count > 0
                ? "This password has been exposed {$count} times!"
                : "This password has not been found in any breach."
        ]);
    }
}