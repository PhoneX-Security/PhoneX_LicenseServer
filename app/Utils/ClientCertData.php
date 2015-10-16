<?php
/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 15.10.15
 * Time: 13:59
 */

namespace Phonex\Utils;


use Illuminate\Http\Request;

class ClientCertData
{
    public $sip;
    public $username;
    public $domain;

    public static function parseFromRequest(Request $request)
    {
        $clientCommonName = $request->server('SSL_CLIENT_S_DN_CN');
        if(!filter_var($clientCommonName, FILTER_VALIDATE_EMAIL)) {
            // non-email format, this should never happen
            throw new \Exception("Non-email format, this should not happen");
        }

        $sipParams = explode('@', $clientCommonName);
        $username = $sipParams[0];
        $domain = $sipParams[1];

        $clientCertData = new ClientCertData();
        $clientCertData->sip = $clientCommonName;
        $clientCertData->username = $username;
        $clientCertData->domain = $domain;

        return $clientCertData;
    }
}