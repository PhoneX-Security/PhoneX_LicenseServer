<?php namespace Phonex\Model;
use GeoIP;
use Illuminate\Database\Eloquent\Model;


class ErrorReport extends Model{
    /* Definition */
    protected $connection = 'mysql_opensips';
    protected $table = 'phxErrorReport';

    public $timestamps = false;

}