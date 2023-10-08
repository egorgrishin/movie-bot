<?php

namespace App\Classes\Telegram;

class Button
{
    public static function get(array $data): string
    {
        $keys = self::getHashKeys();
        $encoded = json_encode($data);
        $hash = md5($encoded);
        $keys = self::getHashKeys();
        $h = '';
        foreach ($keys as $key) {
            $h .= $hash[$key];
        }
        $data['h'] = $h;
        return json_encode($data);
    }

    private static function getHashKeys(): array
    {
        return array_map(
            'intval',
            explode(',', env('BUTTON_HASH_KEYS', '0,1,2,3'))
        );
    }
}
