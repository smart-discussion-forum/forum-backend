<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Student= 'student';
    case Lecturer= 'lecturer';
    case Admin= 'admin';
}
