<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string code
 * @property  group_id
 * @property  license_type_id
 * @property int is_active
 * @property mixed id
 * @property int users_limit
 */
class BusinessCode extends Model{
	protected $table = 'business_codes';
    public static $chars = 'abcdefghjklmnpqrstuvwxyz23456789'; // i,1,0,o characters are skipped
    const MODULO = 29; // 32 characters, therefore modulo 29
    const BASE_LENGTH = 8; // base length + parity character makes business code
    const TOTAL_LENGTH = 9;

    public function group(){
        return $this->belongsTo('Phonex\Group', 'group_id');
    }

    public function licenseType(){
        return $this->belongsTo('Phonex\LicenseType', 'license_type_id');
    }

    public function users(){
        return $this->hasMany('Phonex\User', 'business_code_id');
    }

    /**
     * Contact list mappings
     * Makes sense when a single license is allowed per given business code
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clMappings(){
        return $this->belongsToMany('Phonex\BusinessCode', 'business_code_cl_mappings', 'cl_owner_bcode_id', 'contact_bcode_id');
    }

    /**
     *
     * @param null $prefix, possible le = 'business_codes';
    public static $chars = 'abcdefghjklmnpqrstuvwxyz23456789'; // i,1,0,o characters are skipped
    const MODULO = 29; // 32 characters, therefore modulo 29
    const BASE_LENGTH = 8; // base length +prefix, CANNOT contain '1','i','o' or '0' characters, max length 2 chars
     * @return string
     */
    public static function generateUniqueCode($prefix = null){
        $code = self::getCode($prefix);
        while (BusinessCode::where('code', $code)->count() > 0){
            $code = self::getCode();
        }
        return $code;
    }

    public static function parityCheck($code){
        if (!$code || strlen($code) != self::TOTAL_LENGTH){
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < strlen($code); $i++){
            $pos = strlen($code) - $i - 1;
            $value = array_search($code[$pos], str_split(self::$chars));
            if ($value === false){
                throw new \InvalidArgumentException("verifyCode; code '$code' contains invalid characters, only '" . self::$chars . "' are allowed");
            }
            $sum += $value * ($i+1); // multiply by weight and add to sum
//            echo "char $code[$pos] pos $pos value $value result "  . $value * ($i+1) . " sum $sum\n";
        }

        return ($sum % self::MODULO === 0);
    }

    public static function getCode($prefix = null){
        // 36 characters, 8 characters length, 1 modulo parity character
        $code = getRandomString(self::BASE_LENGTH, self::$chars);

        if ($prefix != null){
            if (strlen($prefix) > 2){
                throw new \LengthException("getCode; prefix can be max 2 characters long");
            }

            $code = $prefix . substr($code, strlen($prefix));
//            echo 'xxx:' . $code;
        }

        $code = $code . self::getParityCharacter($code);
        return $code;
    }

    private static function getParityCharacter($code){
//        echo "\ncode $code\n";
        if (!$code || strlen($code) != self::BASE_LENGTH){
            throw new \LengthException("To generate parity character, we require 7 characters long code.");
        }
        // ISBN-10 like algorithm, using mod 31
        $x = $code . self::$chars[0]; // first add "zero" character to compute error
        $sum = 0;
        for ($i = 0; $i < strlen($x); $i++){
            $pos = strlen($x) - $i - 1;

            $value = array_search($x[$pos], str_split(self::$chars));
            if ($value === false){
                throw new \InvalidArgumentException("getParityCharacter; code '$code' contains invalid characters, only '" . self::$chars . "' are allowed");
            }
            $sum += $value * ($i+1); // multiply by weight and add to sum

//            echo "char $x[$i] pos $i value $value result "  . $value * ($i+1) . " sum $sum\n";

        }
//        echo "sum $sum \n";

        // correct the possible error
        $error = $sum % self::MODULO;

        $parityCharacter = self::$chars[self::MODULO - $error];
//        echo "error $error char $parityCharacter \n";

        return $parityCharacter;
    }
}
