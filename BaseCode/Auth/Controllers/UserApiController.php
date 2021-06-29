<?php

namespace BaseCode\Auth\Controllers;

use App\Http\Resources\UserBankResource;
use App\Models\Bank;
use App\Models\UserBank;
use BaseCode\Auth\Models\User;
use BaseCode\Auth\Requests\StoreUser;
use BaseCode\Auth\Requests\UpdateUser;
use BaseCode\Auth\Requests\UpdateUserPassword;
use BaseCode\Auth\Requests\UpdateUserProfile;
use BaseCode\Auth\Resources\UserResource;
use BaseCode\Auth\Resources\UserResourceCollection;
use BaseCode\Auth\Services\UserRecordService;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class UserApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'users.xlsx';


    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    protected function getResource($item)
    {
        return new UserResource($item);
    }

    protected function getResourceCollection($items)
    {
        return new UserResourceCollection($items);
    }

    public function loggedIn(Request $request)
    {
        $userId = auth()->user()->id;
        $this->filter($request)->sort($request)->include($request);
        return $this->getResource($this->query->find($userId));
    }
    
    public function updateSettings($id, Request $request)
    {
        $user = \DB::transaction(function () use ($id, $request) {
            return UserRecordService::updateSettings(
                User::find($id),
                $request->input('settings')
            );
        });

        return $this->getResource($user);
    }

    public function store(StoreUser $request)
    {
        $user = \DB::transaction(function () use ($request) {
            return UserRecordService::create(
                $request->getName(),
                $request->getEmail(),
                $request->getPassword(),
                $request->getRoles()
            );
        });
        return $this->getResource($user);
    }

    public function update($id, UpdateUser $request)
    {
        $user = \DB::transaction(function () use ($id, $request) {
            return UserRecordService::update(
                User::find($id),
                $request->getName(),
                $request->getEmail(),
                $request->getUsername(),
                $request->getBank()
            );
        });

        return $this->getResource($user);
    }

    public function updateProfile($id, UpdateUserProfile $request)
    {
        $user = \DB::transaction(function () use ($id, $request) {
            return UserRecordService::update(
                User::find($id),
                $request->getName(),
                $request->getEmail(),
                $request->getUsername(),
                $request->getBank()
            );
        });

        return $this->getResource($user);
    }

    public function updatePassword($id, UpdateUserPassword $request)
    {
        $user = \DB::transaction(function () use ($id, $request) {
            return UserRecordService::updatePassword(
                User::find($id),
                $request->getPassword()
            );
        });

        return $this->getResource($user);
    }

    public function destroy($id)
    {
        $user = \DB::transaction(function () use ($id) {
            $user = User::find($id);
            return UserRecordService::delete($user);
        });
        return response('Success', 200);
    }
}
