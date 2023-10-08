<?php

namespace App\Classes\Helpers;

class Emoji
{
    private const NUMS = [
        '0️⃣',
        '1️⃣',
        '2️⃣',
        '3️⃣',
        '4️⃣',
        '5️⃣',
        '6️⃣',
        '7️⃣',
        '8️⃣',
        '9️⃣',
    ];

    /**
     * Преобразует число в Emoji строку
     */
    public static function getNumber(int $number): string
    {
        $string = '';
        while ($number > 0) {
            $digit = $number % 10;
            $number = intdiv($number, 10);
            $string = self::NUMS[$digit] . $string;
        }
        return $string;
    }
}
