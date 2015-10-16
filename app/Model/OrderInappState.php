<?php namespace Phonex\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Utils\BasicEnum;

class OrderInappState extends BasicEnum
{
	const PURCHASE_CREATED = 11;
	const PURCHASE_VERIFIED = 12;
}
