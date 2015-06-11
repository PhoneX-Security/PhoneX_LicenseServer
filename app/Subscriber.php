<?php namespace Phonex;
use Illuminate\Database\Eloquent\Model;
use Phonex\Exceptions\InvalidStateException;
use Phonex\Exceptions\SubscriberAlreadyInCLException;

/**
 * @property  username
 * @property mixed id
 * @property mixed email_address
 * @property string turnPasswd
 * @property  license_type
 */
class Subscriber extends Model{
    protected $connection = 'mysql_opensips';
    protected $table = 'subscriber';

    // legacy - Subscriber table doesn't have timestamps
    public $timestamps = false;
    protected $dates = ['expires_on', 'issued_on',
        'date_last_activity',
        'date_first_login',
        'date_first_user_added',
        'date_first_authCheck',
        'date_last_activity',
        'date_last_authCheck',
        'date_last_pass_change'
    ];

    public function subscribersInContactList(){
        return $this
            ->belongsToMany('Phonex\Subscriber', 'contactlist', 'subscriber_id', 'int_usr_id')
            ->withPivot('displayName');
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

    public function user(){
        return $this->hasOne('Phonex\User'); // looks for subscriber_id in User table
    }

    public function addToContactList(Subscriber $subscriber, $displayName){
        $count = ContactList::whereRaw('subscriber_id=? and int_usr_id=?', [$this->id, $subscriber->id])->count();
        if ($count > 0){
            throw new SubscriberAlreadyInCLException($this, $subscriber);
        }
        // mutual attributes
        $record1 = new ContactList();
        $record1->entryState = "ENABLED";
        $record1->objType = "INTERNAL_USER";
        $record1->hideInContactList = 0;
        $record1->inBlacklist = 0;
        $record1->inWhitelist = 1;

        $record1->int_usr_id = $subscriber->id;
        $record1->subscriber_id = $this->id; // owner
        $record1->displayName = $displayName;

        return $record1->save();
    }

    public function removeFromContactList(Subscriber $subscriber){
        $this->subscribersInContactList()->detach($subscriber->id);
    }

    public static function createSubscriber($username, $password, $startsAt, $expiresAt, $licenseType, $domain = 'phone-x.net'){
        $sip = $username . "@" . $domain;
        $ha1 = getHA1_1($sip, $password);
        $ha1b = getHA1_2($sip, $domain, $password);

        $subscriber = new Subscriber();
        $subscriber->username = $username;
        $subscriber->domain = $domain;
//        $sipUser->password =
        $subscriber->email_address = $sip;

        $subscriber->license_type = $licenseType;

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