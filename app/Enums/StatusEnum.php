<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Active= 'Active';
    case Blacklisted= 'Blacklisted';
    case Suspended= 'Suspended';
}
