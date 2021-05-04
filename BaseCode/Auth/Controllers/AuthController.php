<?php

namespace BaseCode\Auth\Controllers;

use BaseCode\Auth\Resources\UserResource;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function user()
    {
        return new UserResource(auth()->user()->load('roles.permissions'));
    }
}
