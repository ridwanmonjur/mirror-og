<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

class SettingsException extends \Exception
{
    public function report()
    {
        Log::error($this->getMessage());
    }
}
