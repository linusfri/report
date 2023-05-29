<?php

namespace App\Helpers;

class Helper
{
    /** Simulates probability of making a choice */
    public static function randomChance(): bool
    {
        /* 50% chance */
        if (rand(0, 100) > 50) {
            return true;
        }

        return false;
    }
}
