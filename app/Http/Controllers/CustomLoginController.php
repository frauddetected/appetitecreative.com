<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyLoginController;
use Laravel\Fortify\Fortify;
use App\Rules\UsernameRequiresVerifiedEmail;
use Laravel\Fortify\Http\Requests\LoginRequest;

class CustomLoginController extends FortifyLoginController
{
    /**
     * Override the store method for custom logic.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function checkUser(LoginRequest $request)
    {
        $validated = $request->validate([
            Fortify::username() => ['required',new UsernameRequiresVerifiedEmail()],
        ]);
        return parent::store($request);
    }
}
