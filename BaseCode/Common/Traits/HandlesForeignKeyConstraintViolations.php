<?php

namespace BaseCode\Common\Traits;

use Illuminate\Http\Request;

trait HandlesForeignKeyConstraintViolations
{
    protected function getMessages()
    {
        return config('foreign_key_constraint_violations', []);
    }

    public function handleForeignKeyConstraintViolations(Request $request, $exception)
    {
        if ($exception instanceof \Illuminate\Database\QueryException
            && \Str::contains($exception->getMessage(), 'a foreign key constraint fails')) {
            $message = \Arr::first($this->getMessages(), function ($value, $key) use ($exception) {
                return \Str::contains($exception->getMessage(), $key);
            }) ?? null;

            if ($message) {
                throw new \Exception($message);
            }
        }
    }
}
