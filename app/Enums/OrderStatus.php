<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PROGRESS = 'PROGRESS';
    case FINISHED = 'FINISHED';
    case CANCELLED = 'CANCELLED';
}
