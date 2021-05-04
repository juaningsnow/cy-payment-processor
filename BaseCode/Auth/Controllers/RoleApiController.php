<?php

namespace BaseCode\Auth\Controllers;

use BaseCode\Auth\Contracts\Roles;
use BaseCode\Auth\Models\Role;
use BaseCode\Auth\Requests\StoreRole;
use BaseCode\Auth\Requests\UpdateRole;
use BaseCode\Auth\Resources\RoleResource;
use BaseCode\Auth\Resources\RoleResourceCollection;
use BaseCode\Auth\Services\RoleRecordService;
use BaseCode\Common\Controllers\ResourceApiController;

class RoleApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'roles.xlsx';


    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    protected function getResource($item)
    {
        return new RoleResource($item);
    }

    protected function getResourceCollection($items)
    {
        return new RoleResourceCollection($items);
    }

    public function store(StoreRole $request)
    {
        $baseModule = \DB::transaction(function () use ($request) {
            return RoleRecordService::create(
                $request->getName(),
                $request->getPermissions()
            );
        });
        return $this->getResource($baseModule);
    }

    public function update($id, UpdateRole $request)
    {
        $baseModule = \DB::transaction(function () use ($id, $request) {
            return RoleRecordService::update(
                Role::find($id),
                $request->getName(),
                $request->getPermissions()
            );
        });

        return $this->getResource($baseModule);
    }
}
