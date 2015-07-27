<?php namespace Phonex\Jobs;

use Phonex\BusinessCode;
use Phonex\BusinessCodeClMapping;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Utils\BusinessCodeUtils;

/**
 * Class NewCodePairsExport
 * @package Phonex\Jobs
 */
class NewCodePairsExport extends NewCodesExport {
    public function __construct($pairsCount, LicenseType $licenseType, LicenseFuncType $licenseFuncType, $licenseLimitPerCode = 1)
    {
        parent::__construct($pairsCount,
            $licenseType,
            $licenseFuncType,
            $licenseLimitPerCode,
            function($exportId, $prefix){

                $bc1 = new BusinessCode();
                $bc1->code = BusinessCodeUtils::generateUniqueCode($prefix);
                $bc1->export_id = $exportId;
                $bc1->save();

                $bc2 = new BusinessCode();
                $bc2->code = BusinessCodeUtils::generateUniqueCode($prefix);
                $bc2->export_id = $exportId;
                $bc2->save();

                $mapping1 = new BusinessCodeClMapping();
                $mapping1->cl_owner_bcode_id = $bc1->id;
                $mapping1->contact_bcode_id = $bc2->id;
                $mapping1->save();

                $mapping2 = new BusinessCodeClMapping();
                $mapping2->cl_owner_bcode_id = $bc2->id;
                $mapping2->contact_bcode_id = $bc1->id;
                $mapping2->save();

                return [$bc1, $bc2];
        });
    }
}