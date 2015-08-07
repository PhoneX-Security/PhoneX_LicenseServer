<?php namespace Phonex\Http\Controllers\Api;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;
use Phonex\Model\NotificationType;
use Phonex\Model\SupportNotification;
use Phonex\Model\SupportNotificationState;

class SupportNotificationsController extends Controller {

    const SHARED_SECRET = "ovual3ohshiChai5EiPeeP4ma";

	public function __construct(){
	}

    public function getBatch(Request $request)
    {
        // temporarily until oauth
        $key = $request->get('k');
        if ($key !== self::SHARED_SECRET){
            abort(401);
        }

        $notifications = null;
        DB::beginTransaction();
        try
        {
            // Load all notifications in CREATED state - switch them to PROCESSING state and return them to processing for phonex-support
            // Also return all message types
            $notifications = SupportNotification::with('notificationType')->where(['state' => SupportNotificationState::CREATED])->get();
            $ids = $notifications->pluck('id')->toArray();
            SupportNotification::whereIn('id', $ids)->update(['state' => SupportNotificationState::PROCESSING]);

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Log::error('SupportNotificationsController@batch - cannot load messages batch', [$e]);
            return json_encode([]);
        }
        $types = $notifications->pluck('type')->toArray();
        $notificationTypes = NotificationType::whereIn('type', $types)->get();

        $jsonObj = new \stdClass();
        $jsonObj->notifications = $notifications;
        $jsonObj->notification_types = $notificationTypes;

        return json_encode($jsonObj);
    }

    public function postBatch(Request $request)
    {
        // temporarily until oauth
        $key = $request->get('k');
        if ($key !== self::SHARED_SECRET){
            abort(401);
        }

        $ack = $request->get('ack');
        if (!$ack || !in_array($ack, ['pos', 'neg'])){
            abort(400);
        }

        $ids = $request->get('id');
        if (!is_array($ids)){
//            $actualLink = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//            Log::warning("postBatch - bad request", [$actualLink, $request]);
            // bad request
            abort(400);
        }

        // Update processing to sent
        $idsToUpdate = SupportNotification::whereIn('id', $ids)->where('state', SupportNotificationState::PROCESSING)->get()->pluck('id')->toArray();
        if ($ack == 'pos'){
            SupportNotification::whereIn('id', $idsToUpdate)->update(['state' => SupportNotificationState::SENT, 'sent_at' => Carbon::now()]);
        } else {
            SupportNotification::whereIn('id', $idsToUpdate)->update(['state' => SupportNotificationState::CREATED]);
        }
    }
}
