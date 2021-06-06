<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    //.

    private $availableFilters = [
        ['id' => 'name', 'text' => 'Name'],
        ['id' => 'username', 'text' => "Username"],
        ['id' => 'email', 'text' => 'E-mail'],
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'name',
            'sortAscending' => true,
            'baseUrl' => '/api/user-management',
            'exportBaseUrl' => '/user-management'
        ];
        return view('users.index', ['title' => 'Users', 'indexVariables' => $indexVariables]);
    }

    public function create()
    {
        return view('users.create', ['title' => "User Create", 'id' => null]);
    }

    public function edit($id)
    {
        return view('users.edit', ['title' => "User Edit", 'id' => $id]);
    }

    public function show($id)
    {
        $supplier = User::find($id);
        return view('users.show', ['title' => $supplier ? $supplier->name : "--", 'id' => $id]);
    }
}
