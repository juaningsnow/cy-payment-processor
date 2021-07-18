<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\UserCompany;
use BaseCode\Auth\Resources\UserResource;
use BaseCode\Auth\Resources\UserResourceCollection;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
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
            'isAdmin' => 'required',
            'email' => 'required|email',
        ]);
        $userCompanies = $this->assembleUserCompanies($request);
        $userCompanies2 = $this->setFirstAsActive($userCompanies);
        $user = new User;
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->is_admin = $request->input("isAdmin");
        $user->save();
        $user->userCompanies()->sync($userCompanies2);
        return $this->getResource($user);
    }

    private function setFirstAsActive(array $userCompanies)
    {
        $array = [];
        foreach ($userCompanies as $key => $userCompany) {
            if ($key < 1) {
                $userCompany->is_active = true;
            }
            $array[] = $userCompany;
        }
        return $array;
    }

    private function assembleUserCompanies(Request $request)
    {
        $array = array_map(function ($item) {
            if (!isset($item['id'])) {
                $detail = new UserCompany;
            } else {
                $detail = UserCompany::find($item['id']);
            }
            $company = Company::find($item['companyId']);
            $detail->setCompany($company);
            return $detail;
        }, $request->input('userCompanies.data'));

        $this->checkIfDuplicateCompany($array);

        return $array;
    }

    private function checkIfDuplicateCompany(array $array)
    {
        $companyIds = [];
        foreach ($array as $item) {
            $companyIds[] = $item->company_id;
        }

        $hasDuplicate = count($companyIds) !== count(array_unique($companyIds));
        if ($hasDuplicate) {
            throw new GeneralApiException('Please Remove Duplicate Company Entries');
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'isAdmin' => 'required',
            'email' => 'required|email',
        ]);
        $userCompanies = $this->assembleUserCompanies($request);
        $user = User::find($id);
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->is_admin = $request->input("isAdmin");
        $user->save();
        $user->userCompanies()->sync($userCompanies);
        return $this->getResource($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response('success', 200);
    }
}
