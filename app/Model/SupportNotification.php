<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;
use Phonex\User;

class SupportNotification extends Model{
    protected $table = "support_notifications";

    public function notificationType()
    {
        return $this->belongsTo('Phonex\Model\NotificationType', 'notification_type_id');
    }

    public function user()
    {
        return $this->belongsTo('Phonex\User', 'user_id');
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
