<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as AuthFacade;
use App\User;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApiLoginController extends Controller
{
    public function login(Request $request)
    {

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];

        $errors = $this->validator($credentials)->errors()->all();

        if (!empty($errors)) {
            return response(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (AuthFacade::attempt($credentials)) {
            $user = AuthFacade::user()->only('id', 'email','name','api_token');
            return response($user, Response::HTTP_OK);
        }
 
        return response([
            'status' => Response::HTTP_BAD_REQUEST,
            'error' => 'Wrong email or password',
            'response_time' => microtime(true),
            'request' => $request->all(),
        ], Response::HTTP_BAD_REQUEST);

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:3',
        ]);
    }

}
