<?php namespace Phonex;
use Illuminate\Database\Eloquent\Model;
use Phonex\Exceptions\InvalidStateException;

/**
 * @property  username
 * @property mixed id
 * @property mixed email_address
 * @property string turnPasswd
 */
class Subscriber extends Model{
    protected $connection = 'mysql_opensips';
    protected $table = 'subscriber';

    // legacy - Subscriber table doesn't have timestamps
    public $timestamps = false;

    public function subscribersInContactList(){
        return $this->belongsToMany('Phonex\Subscriber', 'contactlist', 'subscriber_id', 'int_usr_id');
    }

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

        $subscriber = new Subscriber();
        $subscriber->username = $username;
        $subscriber->domain = $domain;
//        $sipUser->password =
        $subscriber->email_address = $sip;

        $subscriber->ha1 = $ha1;
        $subscriber->ha1b = $ha1b;
        $subscriber->rpid = 0;
        $subscriber->isAdmin = 0;
        $subscriber->primaryGroup = 0;
        $subscriber->canSignNewCert = 1;
        $subscriber->forcePasswordChange = 1;
        $subscriber->issued_on = $startsAt;
        $subscriber->expires_on = $expiresAt;

        // LS-5 Initialize TURN password for new users.
        $subscriber->turnPasswd = getRandomString(24);

        return $subscriber;
    }


}