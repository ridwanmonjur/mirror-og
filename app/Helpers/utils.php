<?php

use Illuminate\Support\Str;

function generateToken(?int $number = 64): string
{
    return Str::random($number);
}
