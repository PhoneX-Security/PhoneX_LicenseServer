<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;
use Phonex\User;

class SupportNotification extends Model{
    protected $table = "support_notifications";
    protected $visible = ['id', 'sip', 'locale'];
    protected $dates = ['sent_at'];

    public function notificationType()
    {
        return $this->belongsTo('Phonex\Model\NotificationType', 'notification_type_id');
    }

    public function user()
    {
        return $this->belongsTo('Phonex\User', 'user_id');
    }

    // we manually want to include 'type' and 'text' accessors in array/json
    public function toArray()
    {
        $array = parent::toArray();
        $array['type'] = $this->type;
        $array['text'] = $this->text;
        return $array;
    }

    /* Accessors */
    public function getTypeAttribute()
    {
        return $this->notificationType->type;
    }

    public function getTextAttribute()
    {
        if ($this->locale && $this->notificationType){
            return $this->notificationType->translate($this->locale)->text;
        } else {
            return null;
        }
    }

    /* Helpers */
    public static function dispatchWelcomeNotification(User $user)
    {
        $notificationType = NotificationType::findByType(NotificationType::TYPE_WELCOME_MESSAGE);
        $notification = new SupportNotification();
        $notification->user_id = $user->id;
        $notification->sip = $user->email;
        $notification->notification_type_id = $notificationType->id;
        $notification->state = SupportNotificationState::CREATED;

        // at this moment, we do not know locale
        $notification->save();
        return $notification;
    }
}
