<?php

use Illuminate\Database\Seeder;
use Phonex\Group;
use Phonex\LicenseType;

class GroupsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        Group::create(['name' => 'Mobil Pohotovost']);
	}

}
