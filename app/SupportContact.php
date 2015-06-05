<?php namespace Phonex;
use Illuminate\Database\Eloquent\Model;

class SupportContact extends Model{
    protected $connection = 'mysql_opensips';
    protected $table = 'support_contacts';

    public $timestamps = false;
}