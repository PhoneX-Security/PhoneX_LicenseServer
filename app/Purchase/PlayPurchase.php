<?php
/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 12.11.15
 * Time: 16:18
 */

namespace Phonex\Purchase;


use Carbon\Carbon;
use Phonex\Model\OrderPlayInapp;
use Phonex\User;

/**
 */
class PlayPurchase
{
    public $orderId;
    public $productId;
    /* Purchase time is received in milliseconds from epoch */
    public $purchaseTime;
    /* Purchase state corresponds to OrderPlayInappState values */
    public $purchaseState;
    public $purchaseToken;
    public $autoRenewing;
    public $signature;
    public $developerPayload;
    public $appVersion;
    public $itemType;

    public static function fromJson($json)
    {
        $jsonObject = json_decode($json, true);
        if ($jsonObject ===null){
            return null;
        }
        $purchaseJson = $jsonObject['INAPP_PURCHASE_DATA'];
        $signature = valueOrNull($jsonObject, 'INAPP_DATA_SIGNATURE');
        $itemType = valueOrNull($jsonObject, 'INAPP_ITEM_TYPE');
        $appVersion = valueOrNull($jsonObject, 'INAPP_APP_VERSION');
        $detailsObj = json_decode($purchaseJson, true);
        if($detailsObj === null){
            return null;
        }

        $result = new PlayPurchase();

        $result->orderId = valueOrNull($detailsObj, 'orderId');
        $result->productId = valueOrNull($detailsObj, 'productId');
        // purchase time is received in milliseconds from epoch
        $result->purchaseTime = valueOrNull($detailsObj, 'purchaseTime');
        $result->purchaseState = valueOrNull($detailsObj, 'purchaseState');
        $result->purchaseToken = valueOrNull($detailsObj, 'purchaseToken');
        $result->autoRenewing = valueOrNull($detailsObj, 'autoRenewing');
        $result->developerPayload = valueOrNull($detailsObj, 'developerPayload');
        $result->appVersion = $appVersion;
        $result->signature = $signature;
        $result->itemType = $itemType;

        return $result;
    }

    public function verifySignature()
    {
        // TODO
        return true;
    }

    /**
     * Create eloquent model from the object
     * @param User $user
     * @return OrderPlayInapp
     */
    public function createDatabaseModel(User $user)
    {
        $model = new OrderPlayInapp();
        $model->play_order_id = $this->orderId;
        $model->play_product_id = $this->productId;
        $model->purchase_time = $this->getCarbonPurchaseTime();
        $model->purchase_state = $this->purchaseState;
        $model->purchase_token = $this->purchaseToken;
        $model->auto_renewing = $this->autoRenewing;
        $model->app_version = $this->appVersion;
        $model->developer_payload = $this->developerPayload;
        $model->item_type = $this->itemType;
        $model->user_id = $user->id;
        return $model;
    }

    /**
     * @return Carbon instance of timestamp
     */
    public function getCarbonPurchaseTime()
    {
        if ($this->purchaseTime == null){
            return null;
        }
        return Carbon::createFromTimestampUTC($this->purchaseTime / 1000);
    }


}