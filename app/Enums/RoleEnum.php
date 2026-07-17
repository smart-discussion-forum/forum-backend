<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Student= 'student';
    case Lecturer= 'Lecturer';
    case Admin= 'Admin';
}
