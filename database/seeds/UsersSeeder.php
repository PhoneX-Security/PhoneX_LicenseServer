<?php

use Illuminate\Database\Seeder;
use Phonex\Commands\CreateUserWithLicense;
use Phonex\LicenseType;

class UsersSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $licenseType = LicenseType::find(4); // should be infinite license type

        // create user + license + subscription
        $command = new CreateUserWithLicense('admin',
            'admin', $licenseType);
        $command->addSupportContact = false;
        $user = Bus::dispatch($command);

        $user->password = bcrypt('admin');
        $user->has_access = 1;
        $user->save();
    }

}
