<?php namespace Phonex\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;

class Product extends Model{
	protected $table = 'products';
    protected $visible = ['name', 'description', 'id', 'productPrices', 'platform', 'priority', 'appPermissions'];
    protected $casts = ['id'=>'integer', 'priority' => 'integer'];

    public function appPermissions()
    {
        return $this->belongsToMany(AppPermission::class, 'product_app_permission', 'product_id', 'app_permission_id')->withPivot(['value']);
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class, 'license_type_id');
    }

    public function licenseFuncType()
    {
        return $this->belongsTo(LicenseFuncType::class, 'license_func_type_id');
    }

    /**
     * Products can inherit permissions from their variant parents
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permissionParent()
    {
        return $this->belongsTo(self::class, 'permission_parent_id');
    }

    /* Accessors */
    public function getUcNameAttribute()
    {
        return ucfirst($this->name);
    }

    /* Helpers */
    /**
     * todo refactor this and also database design (separate table instead of parent product)
     */
    public function loadPermissionsFromParentIfMissing()
    {
        if (!$this->appPermission && $this->permissionParent){
            // rewrite originally loaded relation
            $this->setRelation('appPermissions', $this->permissionParent->appPermissions);
        }
    }

    public function isConsumable(){
        return $this->licenseType && $this->licenseType->name == LicenseType::EXPIRATION_CONSUMABLE;
    }

    public function computeExpirationTime(Carbon $startsAt){
        // compute new expiration
        $expiresAt = null;
        // if product has no days value set, it's probably consumable therefore we do not set license expiration
        if ($this->licenseType->days){
            // take end of a day for the new license
            $expiresAt = $startsAt->copy();
            $expiresAt =$expiresAt->addDays($this->licenseType->days)->endOfDay();
        }
        return $expiresAt;
    }

    // these basic licenses are used in test, should be always present in DB
    public static function getTrialWeek(){
        return self::findByName("trial_week");
    }
    public static function getTrialMonth(){
        return self::findByName("trial_month");
    }
    public static function getTrialYear(){
        return self::findByName("trial_year");
    }
    public static function getFullMonth(){
        return self::findByName("full_month");
    }

    public static function findByName($name){
        return Product::where('name', $name)->first();
    }

    public static function allForDirectSalePlatform()
    {
        return Product::where(['platform' => 'direct', 'available'=>1])->get();
    }

    public static function allForApplePlatform()
    {
        return Product::where(['platform' => 'apple', 'available'=>1])->get();
    }

    public static function allForGooglePlatform()
    {
        return Product::where(['platform' => 'google', 'available'=>1])->get();
    }


    public static function allAvailable()
    {
        return Product::where(['available'=>1])->get();
    }





}