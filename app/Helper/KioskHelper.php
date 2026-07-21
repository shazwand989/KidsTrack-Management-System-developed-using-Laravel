<?php

namespace App\Helper;

use Illuminate\Support\Facades\Crypt;

class KioskHelper
{
    /**
     * Encrypt a child ID for use in URLs.
     */
    public static function hashId(int $id): string
    {
        return base64_encode(Crypt::encrypt($id));
    }

    /**
     * Decrypt a hashed child ID from a URL.
     */
    public static function decodeId(string $hashed): ?int
    {
        try {
            $decoded = base64_decode($hashed, true);
            if ($decoded === false) return null;
            return Crypt::decrypt($decoded);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate a hashed route URL for kiosk child pages.
     */
    public static function route(string $name, int $childId, array $params = []): string
    {
        $hashed = self::hashId($childId);
        $params['hash'] = $hashed;
        return route($name, $params);
    }
}
