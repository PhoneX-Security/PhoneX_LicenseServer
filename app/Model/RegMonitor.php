<?php namespace Phonex\Model;
use Illuminate\Database\Eloquent\Model;

class RegMonitor extends Model
{
    protected $connection = 'mysql_opensips';
    protected $table = 'phx_reg_mon';
    protected $casts = [
        'id' => 'integer',
        'port'=>'integer',
        'expires'=>'integer',
        'cseq'=>'integer',
        'reg_idx'=>'integer',
        'num_registractions'=>'integer'
    ];

    // table doesn't have timestamps
    public $timestamps = false;
    protected $dates = ['created_at'];
}