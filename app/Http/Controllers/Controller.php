<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    /** Authorize a resource action based on the incoming request. */
    public function __construct()
    {
        if (isset($this->model)) {
            $this->authorizeResource($this->model);
        }
    }
}
