<?php

namespace App\Http\Transformer;

use League\Fractal;
use App\User;

class UserTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'img'                => $user->profile_img,
            'gender'             => $user->gender,
            'interestedInGender' => $user->interested_in_gender,
            'birthDate'          => $user->birth_date,
            'bio'                => $user->bio
        ];
    }
}
