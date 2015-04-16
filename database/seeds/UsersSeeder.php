<?php

use Illuminate\Database\Seeder;
use Phonex\Commands\CreateUser;
use Phonex\LicenseType;

class UsersSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create user with access
        $command = new CreateUser('admin');
        $command->addAccess('admin');
        $user = Bus::dispatch($command);
    }

}
