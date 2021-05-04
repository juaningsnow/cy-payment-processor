<?php

namespace BaseCode\Auth\Controllers;

use BaseCode\Auth\Models\User;
use BaseCode\Auth\Requests\UpdateUser;
use BaseCode\Auth\Resources\UserResource;
use BaseCode\Auth\Resources\UserResourceCollection;
use BaseCode\Auth\Services\UserRecordService;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class ProfileApiController extends ResourceApiController
{
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
    
    public function update($id, UpdateUser $request)
    {
        $user = \DB::transaction(function () use ($id, $request) {
            return UserRecordService::update(
                User::find($id),
                $request->getName(),
                $request->getUsername(),
                $request->getRoles()
            );
        });

        return $this->getResource($user);
    }

    public function updatePassword($id, UpdateUser $request)
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
            return $this->repository->delete($user);
        });
        return response('Success', 200);
    }
}
