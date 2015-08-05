<?php namespace Phonex\Model;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;


class NotificationType extends Model{
    use Translatable;

    protected $table = "notification_types";

    public $translatedAttributes = ['text'];
    protected $fillable = ['type', 'text'];
}
