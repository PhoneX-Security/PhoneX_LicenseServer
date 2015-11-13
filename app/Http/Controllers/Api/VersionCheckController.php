<?php namespace Phonex\Http\Controllers\Api;

use Illuminate\Http\Request;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;
use Phonex\Model\PhonexRelease;


class VersionCheckController extends Controller {
    const nonExistingVersionJson = '{"responseCode":402}';
    const unknownErrorJson = '{"responseCode":403}';

    public function __construct()
    {
    }

    // old api
    public function versionCheck(Request $request) {
        $action = $request->get('action');
        switch ($action) {
            case "getNewestVersion":
                return $this->getNewestVersion($request);
            case "getVersion":
                return $this->getVersion($request);
            default:
                return '';
        }
    }


    public function getNewestVersion(Request $request){
        $type = $request->get('type');

        if (!$type){
//            Log::error("VersionCheck; Missing some of the request fields");
            return $this->responseMissingFields(array("type"));
        }

        $versionCode =  PhonexRelease::where(['type'=>$type])->max('version_code');

        if (!$versionCode){
//            Log::error("VersionCheck; No version code found");
            return self::unknownErrorJson;
        }

        return $this->responseOk(array('versionCode' => intval($versionCode)));
    }

    public function getVersion(Request $request){
        $type = $request->get('type');
        $versionCode = intval($request->get('versionCode'));
        $locale = $this->getSupportedLocale($request->get('locale'));

        $missingFields = array();
        if (!$type) { $missingFields[] = "type"; }
        if (!$versionCode) { $missingFields[] = "versionCode"; }
        if (count($missingFields) >= 1){
            return $this->responseMissingFields($missingFields);
        }

        $version = PhonexRelease::where(['type' => $type, 'version_code' => $versionCode])->first();

        if (!$version) {
//            Log::error("VersionCheck; No version code found");
            return self::nonExistingVersionJson;
        }
        $releaseNotesVarName = "release_notes_" . $locale;

        return $this->responseOk(array(
            "versionCode" => intval($version->version_code),
            "versionName" => $version->version_name,
            "releaseNotes" => $version->$releaseNotesVarName,
            "availableAtMarket" => $version->available_at_market == 0 ? false : true
        ));
    }

    private function getSupportedLocale($requestedLocale){
        if (!$requestedLocale){
            return "en";
        }

        $supportedLocales = array("en", "cs", "sk");
        if (in_array($requestedLocale, $supportedLocales)){
            return $requestedLocale;
        } else {
            return "en"; // default locale
        }

    }

    private function responseOk($array) {
        $array['responseCode'] = 200;
        return json_encode($array);
    }

    private function responseMissingFields($fields) {
        $resp = array('fields' => $fields, 'responseCode' => 401);
        return json_encode($resp);
    }
}
