<?php namespace Phonex\Model;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;


class NotificationType extends Model{
    use Translatable;
    const TYPE_WELCOME_MESSAGE = "welcome_message";
    const TYPE_NON_OFFICE_HOURS_MESSAGE = "non_office_hours_message";

    protected $table = "notification_types";

    public $translatedAttributes = ['text'];
    protected $fillable = ['type', 'text'];
    protected $visible = ['type', 'translations'];

    // what relationships load eagerly on every query
    protected $with = ['translations'];

    /* Helpers */
    public static function getWelcomeNotification()
    {
        return self::findByType(self::TYPE_WELCOME_MESSAGE);
    }

    public static function findByType($type)
    {
        return NotificationType::where('type', $type)->first();
    }
}
