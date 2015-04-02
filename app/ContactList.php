<?php namespace Phonex;
use Illuminate\Database\Eloquent\Model;
use Log;
use Phonex\Exceptions\InvalidStateException;
use Queue;

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

    public static function addSupportToContactListMutually(User $user){
        $supportUser = User::getSupportUser();
        if (!$supportUser){
            throw new \Exception("ContactList; no support user exists");
        }
        return ContactList::addUsersToContactListMutually($user, $supportUser, null, "PhoneX Support");
    }

    /**
     * @param User $user1
     * @param User $user2
     * @param null $displayName1
     * @param null $displayName2
     * @return bool
     * @throws InvalidStateException
     */
    public static function addUsersToContactListMutually(User $user1, User $user2, $displayName1 = null, $displayName2 = null){
        if (!$user1->subscriber_id){
            throw new InvalidStateException("Cannot add user '" . $user1->username . "' as contact because he has no subscriber id");
        }
        if (!$user2->subscriber_id){
            throw new InvalidStateException("Cannot add user '" . $user2->username . "' as contact because he has no subscriber id");
        }

        // mutual attributes
        $record1 = new ContactList();
        $record1->entryState = "ENABLED";
        $record1->objType = "INTERNAL_USER";
        $record1->hideInContactList = 0;
        $record1->inBlacklist = 0;
        $record1->inWhitelist = 1;

        $record2 = new ContactList();
        $record2->forceFill($record1->attributes);

        // first record
        $record1->int_usr_id = $user1->subscriber_id;
        $record1->subscriber_id = $user2->subscriber_id; // owner
        $record1->displayName = ($displayName1) ? $displayName1 : $user1->username;

        // second record
        $record2->int_usr_id = $user2->subscriber_id;
        $record2->subscriber_id = $user1->subscriber_id; // owner
        $record2->displayName = ($displayName2) ? $displayName2 : $user2->username;

        if (!$record1->save()){
            Log::error("addUsersToContactListMutually; cannot create record1 in contact list");
            return false;
        } else {
            // in phpunit, this may cause stackoverflow, please mock it!
            Queue::push('ContactListUpdated', ['username'=>$user2->email], 'users');
        }

        if (!$record2->save()){
            Log::error("addUsersToContactListMutually; cannot create record2 in contact list");
            return false;
        } else {
            // in phpunit, this may cause stackoverflow, please mock it!
            Queue::push('ContactListUpdated', ['username'=>$user1->email], 'users');
        }

        Log::info("Users have been mutually added to contact list", compact($user1->email, $user2->email));
        return true;
    }
}