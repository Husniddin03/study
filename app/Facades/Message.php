<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Message extends Facade
{
    /**
     * Bu metod Container ichidagi qaysi kalit so'zni 
     * chaqirish kerakligini Fasad tizimiga aytib turadi.
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\MessageInterface::class;
    }
}