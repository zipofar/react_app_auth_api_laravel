<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    public function getProfile(Request $request, User $user)
    {
        $id = $request->only('id');
        $profile = $user->find($id)->first()->toArray();

        return response(['profile' => $profile]);
    }

    public function update(Request $request)
    {
        $input = $this->filterNullUndefined($request->all());
        $user = User::find($request->id);
        $errorsValidating = $this->validator($input)->errors()->all();

        if (!empty($errorsValidating)) {
            return response(['error' => $errorsValidating], Response::HTTP_BAD_REQUEST);
        }

        $newInput = array_map(function($item) {
            return $item === 'NONE' ? '' : $item;
        }, $input);
        $resultSave = $user->update($newInput);

        if (!$resultSave) {
            return response(['error' => ['Server could not save data']], Response::HTTP_BAD_REQUEST);
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $fileAvatar = $request->file('avatar');

            Image::make($fileAvatar)->resize(200, 200)->save();

            $file_path_avatar = $fileAvatar->store('img/avatars', 'public');

            if (!$file_path_avatar) {
                return response(['error' => ['Server could not save avatar on storage']], Response::HTTP_BAD_REQUEST);
            }

            $user->file_path_avatar = '/storage/' . $file_path_avatar;

            $resultSave2 = $user->save();
            if (!$resultSave2) {
                return response(['error' => ['Server could not save path to avatar in DataBase']], Response::HTTP_BAD_REQUEST);
            }
        }
        $profile = $user->toArray();

        return response($profile, Response::HTTP_OK);
    }

    protected function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:190',
            'email' => [
                'required',
                'string',
                'email',
                'max:190',
                Rule::unique('users')->ignore($data['id']),
            ],
            'birthday' => 'nullable|date',
            'country' => 'nullable|string|max:190',
            'sex' => 'nullable|string',
            'about' => 'nullable|string',
            'avatar' => 'nullable|image:jpg,jpeg,png',
        ]);
    }

    protected function filterNullUndefined($data)
    {
        return array_filter($data, function($item) {
            return $item !== 'undefined' && $item !== null && $item !== 'null';
        });
    }
}
