<?php namespace Phonex\Http\Controllers\Api;

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
        Log::info("SupportNotifications - retrieving batch");
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

        Log::info("SupportNotifications - notifications to be sent.", [$notifications]);

        return json_encode($jsonObj);
    }

    public function postBatch(Request $request)
    {
        // temporarily until oauth
        $ids = $request->get('id');
        if (!is_array($ids)){
            // bad request
            abort(400);
        }

        // Update processing to sent
        $idsToUpdate = SupportNotification::whereIn('id', $ids)->where('state', SupportNotificationState::PROCESSING)->get()->pluck('id')->toArray();
        SupportNotification::whereIn('id', $idsToUpdate)->update(['state' => SupportNotificationState::SENT]);
    }
}
