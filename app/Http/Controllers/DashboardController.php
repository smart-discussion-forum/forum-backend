<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            RoleEnum::Admin => view('dashboard.admin'),
            RoleEnum::Lecturer => view('dashboard.lecturer', [
                'myGroups' => Group::query()
                    ->where(function ($query) use ($user) {
                        $query->where('created_by', $user->id)
                            ->orWhereIn('id', $user->groups()->pluck('groups.id'));
                    })
                    ->orderBy('name')
                    ->get(),
            ]),
            default => view('dashboard'),
        };
    }
}
