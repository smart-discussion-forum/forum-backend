<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Student= 'Student';
    case Lecturer= 'Lecturer';
    case Admin= 'Admin';
}
