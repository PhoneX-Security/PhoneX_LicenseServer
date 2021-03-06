<?php namespace Phonex\Utils;

use Phonex\User;
use Queue;

trait TestUtils{

    // temp fix for Queue push calls in test - causing failures
    public function mockQueuePush()
    {
        Queue::shouldReceive('push');
        Queue::shouldReceive('connected');
    }

    public function callAndCheckResponse($url, array $params, $expectedJsonCode, $ip = null, $message = null){
        $response = null;
        if ($ip){
            $response = $this->call('POST', $url, $params, [], [], ['REMOTE_ADDR' => $ip]);
        } else {
            $response = $this->call('POST', $url, $params);
        }
//        dd($response);
        $json = json_decode($response->getContent());
        $this->assertEquals($expectedJsonCode, $json->responseCode, $message);
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
