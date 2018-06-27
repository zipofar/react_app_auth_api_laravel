<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApiRegisterController extends Controller
{

    public function register(Request $request)
    {
        Log::channel('daily')->notice('Try register from IP:'.request()->ip());

        $validator = $this->validator($request->input());
        $errors = $validator->errors()->all();
        if (!empty($errors)) {
            return response(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->create($request->input());
        $profile = $user->only(['email', 'name', 'id', 'api_token']);
        Log::channel('daily')->notice('Success register from IP:'.request()->ip());
        return response($profile, Response::HTTP_OK);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:190',
            'email' => 'required|string|email|max:190|unique:users',
            'password' => 'required|string|min:3|max:20',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = new User;

        $user->password = Hash::make($data['password']);
        $user->api_token = str_random(40);
        $user->file_path_avatar = '/img/user_def_avatar.png';
        $user->name = $data['name'];
        $user->email = $data['email'];

        $user->save();
        return $user;
        /*return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'api_token' => str_random(40),
            'file_path_avatar' => '/img/user_def_avatar.png',
        ]);*/
    }
}
