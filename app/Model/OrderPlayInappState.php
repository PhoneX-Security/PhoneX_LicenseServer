<?php namespace Phonex\Model;

use Phonex\Utils\BasicEnum;

class OrderPlayInappState extends BasicEnum
{
	const PURCHASE_PURCHASED = 0;
	const PURCHASE_CANCELED = 1;
	const PURCHASE_REFUNDED = 2;
}
