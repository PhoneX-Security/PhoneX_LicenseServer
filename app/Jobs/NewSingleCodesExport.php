<?php namespace Phonex\Jobs;

use Phonex\BusinessCode;
use Phonex\BusinessCodeClMapping;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Model\CodeExportType;
use Phonex\Utils\BusinessCodeUtils;

/**
 * Class NewSingleCodesExport
 * @package Phonex\Jobs
 */
class NewSingleCodesExport extends NewCodesExport {
    const TYPE = CodeExportType::SINGLE;

    public function __construct($pairsCount, LicenseType $licenseType, LicenseFuncType $licenseFuncType, $licenseLimitPerCode)
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

                return $bc1;
        });
    }
}