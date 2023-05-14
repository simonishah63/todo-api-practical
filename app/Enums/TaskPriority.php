<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TaskPriority: string
{
    use EnumToArray;

    case HIGH = 'High';
    case MEDIUM = 'Medium';
    case LOW = 'Low';
}
