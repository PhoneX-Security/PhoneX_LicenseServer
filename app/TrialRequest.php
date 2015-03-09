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
 */
class TrialRequest extends Model{
    protected $table = 'phonex_trial_requests';

    // legacy names
    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';
}