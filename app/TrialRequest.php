<?php namespace Phonex;
use Illuminate\Database\Eloquent\Model;

/**
 * @property  username
 * @property mixed id
 * @property string captcha
 * @property string imei
 * @property  ip
 * @property bool isApproved
 * @property  ip
 * @property mixed phonexUserId
 * @property mixed username
 */
class TrialRequest extends Model{
    protected $table = 'trial_requests';
    protected $fillable = ['imei', 'captcha'];

    // legacy names
    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';
}