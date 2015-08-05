<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class SupportNotification extends Model{
    protected $table = "notification_types";

    public function notificationType()
    {
        return $this->belongsTo('Phonex\Model\NotificationType', 'notification_type_id');
    }

    public function user()
    {
        return $this->belongsTo('Phonex\User', 'user_id');
    }
}
