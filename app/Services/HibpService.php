<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HibpService
{
    public function checkPassword(string $password): int
    {
        $hash = strtoupper(sha1($password));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);

        $response = Http::withHeaders([
            'Add-Padding' => 'true'
        ])->get("https://api.pwnedpasswords.com/range/{$prefix}");

        $lines = explode("\n", $response->body());

        foreach ($lines as $line) {
            [$hashSuffix, $count] = explode(":", trim($line));
            if ($hashSuffix === $suffix) {
                return (int) $count;
            }
        }

        return 0;
    }
}