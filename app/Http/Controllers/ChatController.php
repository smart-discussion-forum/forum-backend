<?php

namespace App\Http\Controllers;

class ChatController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups()->orderBy('name')->get();

        return view('chat.index', compact('groups'));
    }
}
