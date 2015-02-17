<?php namespace Phonex;
use Illuminate\Database\Eloquent\Model;
use Phonex\Exceptions\InvalidStateException;

/**
 * @property  username
 * @property mixed id
 */
class SipUser extends Model{
    protected $connection = 'mysql_opensips';
    protected $table = 'subscriber';

    // legacy - Subscriber table doesn't have timestamps
    public $timestamps = false;

    public function setPasswordFields($password){
        if (empty($this->username) || empty($this->domain)){
            throw new InvalidStateException('Cannot set password fields (HA1_1, HA1_2) because username or domain is empty in this object.');
        }

        $sip = $this->username . "@" . $this->domain;

        $ha1 = getHA1_1($sip, $password);
        $ha1b = getHA1_2($sip, $this->domain, $password);

        $this->ha1 = $ha1;
        $this->ha1b = $ha1b;
    }

    public static function createSubscriber($username, $password, $startsAt, $expiresAt, $domain = 'phone-x.net'){
        $sip = $username . "@" . $domain;
        $ha1 = getHA1_1($sip, $password);
        $ha1b = getHA1_2($sip, $domain, $password);

        $sipUser = new SipUser();
        $sipUser->username = $username;
        $sipUser->domain = $domain;
//        $sipUser->password =
        $sipUser->email_address = $sip;

        $sipUser->ha1 = $ha1;
        $sipUser->ha1b = $ha1b;
        $sipUser->rpid = 0;
        $sipUser->isAdmin = 0;
        $sipUser->primaryGroup = 0;
        $sipUser->canSignNewCert = 1;
        $sipUser->forcePasswordChange = 1;
        $sipUser->issued_on = $startsAt;
        $sipUser->expires_on = $expiresAt;
        return $sipUser;
    }


}