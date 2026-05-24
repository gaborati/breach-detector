<?php
namespace App\Http\Controllers;

use App\Services\HibpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PasswordCheck;

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

        PasswordCheck::create([
            'ip_address' => $request->ip(),
            'breached' => $count > 0,
            'breach_count' => $count,
        ]);

        return response()->json([
            'breached' => $count > 0,
            'count' => $count,
            'message' => $count > 0
                ? "This password has been exposed {$count} times!"
                : "This password has not been found in any breach."
        ]);
    }
}