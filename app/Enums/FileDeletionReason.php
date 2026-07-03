<?php

namespace App\Enums;

enum FileDeletionReason: string
{
    case Manual = 'manual';
    case Expired = 'expired';
}
