<?php namespace Phonex\Model;
use Illuminate\Database\Eloquent\Model;

class UsageLogs extends Model{
    protected $connection = 'mysql_opensips';
    protected $table = 'usage_logs';

    // legacy - table doesn't have timestamps
    public $timestamps = false;
    protected $dates = ['lwhen'];
}