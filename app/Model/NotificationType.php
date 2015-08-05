<?php namespace Phonex\Model;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;


class NotificationType extends Model{
    use Translatable;
    const TYPE_WELCOME_MESSAGE = "welcome_message";

    protected $table = "notification_types";

    public $translatedAttributes = ['text'];
    protected $fillable = ['type', 'text'];

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
