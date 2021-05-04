<?php

namespace BaseCode\Auth\Controllers;

use BaseCode\Auth\Resources\PermissionResource;
use BaseCode\Auth\Resources\PermissionResourceCollection;
use BaseCode\Common\Controllers\ResourceApiController;
use Spatie\Permission\Models\Permission;

class PermissionApiController extends ResourceApiController
{
    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
    }

    protected function getResource($item)
    {
        return new PermissionResource($item);
    }

    protected function getResourceCollection($items)
    {
        return new PermissionResourceCollection($items);
    }
}
