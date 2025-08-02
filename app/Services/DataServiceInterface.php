<?php

namespace App\Services;

interface DataServiceInterface
{
    public function produceBrackets(
        int $teamSlot,
        bool $param1,
        ?array $param2,
        $param3,
        $param4,
    );

    public function generateDefaultValues(
        bool $isOrganizer,
        array $USER_ENUMS
    ): array;

    public function getPrevValues(): array;

    public function getPagination() : ?array;
}