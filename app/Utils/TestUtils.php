<?php namespace Phonex\Utils;

use Bus;
use Phonex\Commands\CreateUser;
use Phonex\User;

trait TestUtils{

    public function createUser($username){
        $oldUser = User::where('username', $username)->first();
        if ($oldUser != null){
            $oldUser->deleteWithLicenses();
        }
        $u1 = Bus::dispatch(new CreateUser($username));
        return $u1;
    }
}
