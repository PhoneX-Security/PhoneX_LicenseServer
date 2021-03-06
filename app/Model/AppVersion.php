<?php namespace Phonex\Model;


/**
 * Android example:
 * {"ac":"1.1.5","v":1,"pid":19,"rc":2277,"info":"PhoneX","p":"android", "locales":["en_EN"]}
 *
 * iOS version:
 * {"p":"iOS","oscd":"8.1.3","info":"PhoneX","v":1,"ac":"1.0.3"}
 *
 */
class AppVersion {

    public $platform;
    public $platformId;
    public $oscd;
    public $versionCode;
    public $versionName;
    public $appName;
    public $device;
    public $locales = [];


    function __construct($json)
    {
        $obj = json_decode($json);

        $this->platform = isset($obj->p) ? $obj->p : null; // android
        $this->oscd = isset($obj->oscd) ? $obj->oscd : null; //  OS code, e.g. iOS 8.1.3
        $this->platformId = isset($obj->pid) ? $obj->pid : null; // e.g. kitkat
        $this->versionCode = isset($obj->rc) ? $obj->rc : null; //release code aka version code (android naming)
        $this->versionName = isset($obj->ac) ? $obj->ac : null; // e.g. 1.0.4
        $this->appName = isset($obj->info) ? $obj->info : null;
        $this->device = isset($obj->dev) ? $obj->dev : null;
        if (isset($obj->locales) && is_array($obj->locales)){ // preferred locales
            $this->locales = $obj->locales;
        }
    }

    public function platformDesc()
    {
        $desc = ucfirst($this->platform);
        switch ($this->platform){
            case "iOS":
                return "{$desc} {$this->oscd}";
                break;
            default: // android and such
//                return $desc . " " . $this->platformId;
                return "{$desc} {$this->platformId}";
                break;
        }
    }

    public function device()
    {
        return ($this->device ? " ({$this->device})": "unknown");
    }

    public function versionDesc()
    {
        switch ($this->platform){
            case "iOS":
                return $this->versionName;
                break;
            default: // android and such
                return $this->versionName . " (" .$this->versionCode . ")";
                break;
        }
    }
}