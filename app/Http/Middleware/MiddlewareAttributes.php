<?php
/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 20.10.15
 * Time: 15:20
 */

namespace Phonex\Http\Middleware;


use Phonex\Utils\BasicEnum;

class MiddlewareAttributes extends BasicEnum
{
    /**
     * User instance
     */
    const CLIENT_CERT_AUTH_USER = "client_cert_authenticated_user";

}