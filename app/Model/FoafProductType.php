<?php
/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 12.1.16
 * Time: 12:29
 */

namespace Phonex\Model;

use Phonex\License;
use Phonex\Utils\BasicEnum;

class FoafProductType extends BasicEnum
{
    // group license products into few categories for FOAF graph
    const TRIAL_WEEK_AND_LESS = 0;
    const TRIAL_UP_TO_YEAR = 1;
    const INFINITE = 2;
    const FULL_B2B = 3;
    const INAPP_B2C = 4;
    const NO_PRODUCT_AND_TESTS = 5;

    public static $mapping = [
        self::NO_PRODUCT_AND_TESTS => ['test.calls.min1',
            'test.files.outgoing5',
            'test.messages.outgoing5',
            'test.messages.outgoing_day_limit5',
            'default'
        ],
        self::INAPP_B2C => [
            'phonex.text.subs.basic2e.m.v1',
            'phonex.text.cons.min30.v1',
            'inapp.subs.basic.month',
            'inapp.subs.premium.e20',
            'inapp.cons.call30',
            'inapp.cons.call60',
            'inapp.cons.files25',
            'inapp.cons.files50',
            'inapp.subs.premium.e10',
            'android.subs.basic2.month',
            'ios.subs.basic3.month',
            'ios.subs.business.month',
            'android.subs.business.month',
            'business.extended.3days'
        ],
        self::FULL_B2B => [
            'full',
            'full_quarter',
            'full_half_year',
            'full_month',
            'full_year',
            'full_month_one_plus_one',
            'full_year_one_plus_one',
            // companies - side licenses
            'company_client_year',
            'company_client_quarter',
            'company_client_month'

        ],
        self::INFINITE => [
            'full_infinite', 'trial_infinite'
        ],
        self::TRIAL_UP_TO_YEAR => [
            'trial',
            'trial_quarter',
            'trial_half_year',
            'trial_month',
            'trial_year'
        ],
        self::TRIAL_WEEK_AND_LESS => [
            'trial_day',
            'trial_week',
        ]
    ];


    /**
     * @param $license License|null
     * @return int|string
     */
    public static function getTypeByLicenseProduct($license)
    {
        if ($license == null || !$license->product){
            return self::NO_PRODUCT_AND_TESTS;
        }
        $productName = $license->product->name;

        foreach (self::$mapping as $type => $productNames){
            if (in_array($productName, $productNames)){
                return $type;
            }
        }
        // if nothing was found, return this
        return self::NO_PRODUCT_AND_TESTS;
    }
}