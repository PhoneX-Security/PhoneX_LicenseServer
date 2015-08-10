<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;
use Phonex\User;

class SupportNotification extends Model{
    protected $table = "support_notifications";
    protected $visible = ['id', 'sip'];
    protected $dates = ['sent_at'];

    public function notificationType()
    {
        return $this->belongsTo('Phonex\Model\NotificationType', 'notification_type_id');
    }

    public function user()
    {
        return $this->belongsTo('Phonex\User', 'user_id');
    }

    // we manually want to include 'type' accessor in array/json
    public function toArray()
    {
        $array = parent::toArray();
        $array['type'] = $this->type;
        return $array;
    }

    /* Accessors */
    public function getTypeAttribute()
    {
        return $this->notificationType->type;
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
        $notification->save();
        return $notification;
    }
}
