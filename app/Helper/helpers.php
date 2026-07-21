<?php

if (!function_exists('hash_id')) {
    /**
     * Encrypt an ID for use in URLs.
     * Usage: {{ hash_id($child->id) }}
     */
    function hash_id(int $id): string
    {
        return \App\Helper\KioskHelper::hashId($id);
    }
}

if (!function_exists('unhash_id')) {
    /**
     * Decrypt a hashed ID from a URL.
     */
    function unhash_id(string $hashed): ?int
    {
        return \App\Helper\KioskHelper::decodeId($hashed);
    }
}
