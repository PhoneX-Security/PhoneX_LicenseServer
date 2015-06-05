<?php namespace Phonex;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string entryState
 * @property string objType
 * @property int hideInContactList
 * @property int inBlacklist
 * @property int inWhitelist
 * @property mixed subscriber_id
 * @property bool|mixed displayName
 * @property mixed int_usr_id
 * @property mixed id
 */
class ContactList extends Model{
    protected $connection = 'mysql_opensips';
    protected $table = 'contactlist';

    // legacy names
    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateLastEdit';

    public static function addSupportToContactListMutually(User $user, User $supportUser = null){
        if ($supportUser == null){
            $supportUser = User::getSupportUser();
        }

        if (!$supportUser){
            throw new \Exception("ContactList; no support user exists");
        }

        ContactList::addUsersToContactListMutually($user, $supportUser, null, "Support");

        // push support information onto sip server
        $supportContact = new SupportContact();
        $supportContact->owner_sip = $user->email;
        $supportContact->support_sip = $supportUser->email;
        $supportContact->order = 1;
        $supportContact->save();

    }

    /**
     * @param User $user1
     * @param User $user2
     * @param null $displayName1
     * @param null $displayName2
     */
    public static function addUsersToContactListMutually(User $user1, User $user2, $displayName1 = null, $displayName2 = null){
        $user1->addToContactList($user2, $displayName2);
        $user2->addToContactList($user1, $displayName1);
    }
}