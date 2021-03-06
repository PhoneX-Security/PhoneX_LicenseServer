<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('LicenseTypesSeeder');
        $this->command->info('License Types table seeded!');

        $this->call('GroupsSeeder');
        $this->command->info('Groups table seeded!');

        $this->call('UsersSeeder');
        $this->command->info('Users + Licenses + Subscriber tables seeded!');
	}

}
