<?php

namespace App\Services;

interface DataServiceInterface
{
    public function produceBrackets(
        int $teamSlot,
        bool $param1,
        ?array $param2,
        ?array $param3
    );
}