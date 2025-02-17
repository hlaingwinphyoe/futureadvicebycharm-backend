<?php

namespace App\Services;

class UserService
{
    public function updateInfo(object $user, array $paramData = [])
    {
        $user->update([
            'name' => $paramData['username'],
            'email' => $paramData['email'] ?? null,
            'phone' => $paramData['phone'] ?? null,
            'dob' => $paramData['dob'] ?? null,
            'gender_id' => $paramData['gender'] ?? null,
            'weekday_id' => $paramData['birthday'] ?? null,
        ]);

        return $user;
    }
}
