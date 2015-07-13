<?php namespace Phonex\Utils;

use Bus;
use Phonex\Jobs\CreateUser;
use Phonex\User;
use Queue;

trait TestUtils{

    public function createUser($username)
    {
        $oldUser = User::where('username', $username)->first();
        if ($oldUser != null){
            $oldUser->deleteWithLicenses();
        }
        $u1 = Bus::dispatch(new CreateUser($username));
        return $u1;
    }

    // temp fix for Queue push calls in test - causing failures
    public function mockQueuePush()
    {
        Queue::shouldReceive('push');
        Queue::shouldReceive('connected');
    }

    public function callAndCheckResponse($url, array $params, $expectedJsonCode, $ip = null){
        $response = null;
        if ($ip){
            $response = $this->call('POST', $url, $params, [], [], ['REMOTE_ADDR' => $ip]);
        } else {
            $response = $this->call('POST', $url, $params);
        }
//        dd($response);
        $json = json_decode($response->getContent());
        $this->assertEquals($expectedJsonCode, $json->responseCode);
        return $json;
    }

    public function deleteUsers(array $usernames){
        foreach ($usernames as $username){
            $oldUser = User::where('username', $username)->first();
            if ($oldUser != null){
                $oldUser->deleteWithLicenses();
            }
        }
    }

    public function deleteSubscribers(array $usernames)
    {
        foreach($usernames as $username){
            $user = User::findByUsername($username);
            if ($user && $user->subscriber){
                $user->subscriber->deleteWithContactListRecords();
            }
        }
    }
}
