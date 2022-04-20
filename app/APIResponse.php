<?php namespace App;

class APIResponse {

    /**
     * Accepts API call.
     * @param array $response Response
     * @return array Formatted route response
     */
    public static function success(array $response = []) {
        return ['response' => $response];
    }

    /**
     * Rejects API call.
     * @param int $code Unique to caller function error code
     * @param string $description Error description. It's not visible for the user, but should be visible in console.
     * @return array Formatted route response
     */
    public static function reject(int $code, string $description = 'Unknown error description') {
        return ['error' => [$code, $description]];
    }

    public static function reject2FA() {
        return self::reject(-1024, 'Invalid 2FA session');
    }

}
