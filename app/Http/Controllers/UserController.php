<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCompany;

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
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
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
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('users.create', ['title' => "User Create", 'id' => null]);
    }

    public function edit($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('users.edit', ['title' => "User Edit", 'id' => $id]);
    }

    public function show($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $user = User::find($id);
        return view('users.show', ['title' => $user ? $user->name : "--", 'id' => $id]);
    }

    public function setActive($userCompanyId)
    {
        auth()->user()->userCompanies->each(function ($userCompany) {
            $userCompany->is_active = false;
            $userCompany->save();
        });
        $userCompanyToSetActive = UserCompany::find($userCompanyId);
        $userCompanyToSetActive->is_active = true;
        $userCompanyToSetActive->save();
        return back();
    }
}
