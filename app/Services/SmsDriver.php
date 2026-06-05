<?php

namespace App\Services;

class SmsDriver implements MessageInterface
{
    public function send(string $to, string $text): bool
    {
        // Real loyihada bu yerda Telegram API ga so'rov yuboriladigan kod bo'ladi.
        // Hozircha biz terminal logiga yozib simulyatsiya qilamiz.
        logger("SMS xabar ketdi to: {$to}. Matn: {$text}");

        return true;
    }
}