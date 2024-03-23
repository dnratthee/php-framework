<?php

use App\Libs\Auth\Auth;

//  TODO :: Add a method to check if the user is authorized to access a resource
trait Authorizable
{
    public function authorize($request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
    }
}
