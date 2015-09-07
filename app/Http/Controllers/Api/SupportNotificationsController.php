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
            $notifications = SupportNotification::with('notificationType','user.subscriber')->where(['state' => SupportNotificationState::CREATED])->get();
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

        // update locales - get only those notifications we can figure out locale for, otherwise put them back to CREATED state
        $notifications = $this->notificationsWithLocales($notifications);

        $jsonObj = new \stdClass();
        $jsonObj->notifications = $notifications;
        $json = json_encode($jsonObj);
//        Log::info("Retrieving notification batch", [$json]);
        return $json;
    }

    // update locale according to subscriber->app_version object
    private function notificationsWithLocales($notifications)
    {
        $toReturn = [];
        foreach($notifications as $notification){
            $appVersionObj = null;
            if ($notification->user->subscriber && $notification->user->subscriber->app_version_obj){
                $appVersionObj = $notification->user->subscriber->app_version_obj;
            }
            if (!$appVersionObj || !$appVersionObj->locales){
                // if we cannot figure locale out from app_version, put notification back to created state
                SupportNotification::where('id', $notification->id)->update(['state'=> SupportNotificationState::CREATED]);
            } else {
                // locales are order by priority
                // locale can be in format "en_US", split string by '_' and take the first part
                $locale = explode('_', $appVersionObj->locales[0])[0];

                if (!$notification->notificationType->hasTranslation($locale)){
                    // if such translation does not exist, put default locale as 'en'
                    $locale = 'en';
                }

                $notification->locale = $locale;
                $notification->save();

                $toReturn[] = $notification;
            }
        }
        return $toReturn;
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

        Log::debug("postBatch", [$ack, $ids]);

        // Update processing to sent
        $idsToUpdate = SupportNotification::whereIn('id', $ids)->where('state', SupportNotificationState::PROCESSING)->get()->pluck('id')->toArray();
        if ($ack == 'pos'){
            SupportNotification::whereIn('id', $idsToUpdate)->update(['state' => SupportNotificationState::SENT, 'sent_at' => Carbon::now()]);
        } else {
            SupportNotification::whereIn('id', $idsToUpdate)->update(['state' => SupportNotificationState::CREATED]);
        }
    }
}
