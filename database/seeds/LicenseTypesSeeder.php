<?php

use Illuminate\Database\Seeder;
use Phonex\LicenseType;

class LicenseTypesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        LicenseType::create(['name' => 'trial', 'days' => 7, 'is_trial' => 1, 'order' => 2]);
        LicenseType::create(['name' => 'year', 'days' => 365, 'is_trial' => 0, 'order' => 6]);
        LicenseType::create(['name' => 'month', 'days' => 31, 'is_trial' => 0, 'order' => 3]);
        LicenseType::create(['name' => 'infinite', 'days' => 4000, 'is_trial' => 0, 'order' => 7]);
        LicenseType::create(['name' => 'quarter', 'days' => 91, 'is_trial' => 0, 'order' => 4]);
        LicenseType::create(['name' => 'half_year', 'days' => 182, 'is_trial' => 0, 'order' => 5]);
        LicenseType::create(['name' => 'day', 'days' => 1, 'is_trial' => 0, 'order' => 1]);
        // mobil pohotovost
        LicenseType::create(['name' => 'mp_half_year', 'days' => 91, 'is_trial' => 0, 'order' => 8]);
	}

}
