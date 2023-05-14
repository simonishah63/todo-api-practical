<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TaskStatus: string
{
    use EnumToArray;

    case NEW = 'New';
    case INCOMPLETE = 'Incomplete';
    case COMPLETE = 'Complete';
}
