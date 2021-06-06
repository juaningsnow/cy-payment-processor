<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use BaseCode\Auth\Resources\UserResource;
use BaseCode\Auth\Resources\UserResourceCollection;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class UserApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'users.xlsx';

    protected $users;

    public function __construct(User $user)
    {
        $this->middleware('auth:api');
        parent::__construct($user);
    }

    public function getResource($item)
    {
        return new UserResource($item);
    }

    public function getResourceCollection($items)
    {
        return new UserResourceCollection($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'companyId' => 'required',
            'isAdmin' => 'required',
            'email' => 'required|email',
        ]);
        $user = new User;
        $company = Company::find($request->input('companyId'));
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->is_admin = $request->input("isAdmin");
        $user->company()->associate($company);
        $user->company_id = $request->input('companyId');
        $user->save();
        return $this->getResource($user);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'companyId' => 'required',
            'isAdmin' => 'required',
            'email' => 'required|email',
        ]);
        $user = User::find($id);
        $company = Company::find($request->input('companyId'));
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->is_admin = $request->input("isAdmin");
        $user->company()->associate($company);
        $user->company_id = $request->input('companyId');
        $user->save();
        return $this->getResource($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response('success', 200);
    }
}
