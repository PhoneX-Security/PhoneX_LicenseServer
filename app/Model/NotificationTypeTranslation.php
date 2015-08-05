<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class NotificationTypeTranslation extends Model{
    protected $table = "notification_type_translations";
    protected $fillable = ['text'];

    public $timestamps = false;
}
