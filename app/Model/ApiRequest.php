<?php namespace Phonex\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiRequest extends Model
{
    protected $table = 'api_requests';
    // table doesn't have timestamps
    public $timestamps = false;
    protected $dates = ['time'];

    public static function saveReq(Request $request, $username){
        $r = new ApiRequest();
        $r->time = Carbon::now();
        $r->user = $username;
        $r->path = $request->path();
        $r->query_string = $request->getQueryString();
        $r->ip = $request->getClientIp();
        $r->save();
    }
}