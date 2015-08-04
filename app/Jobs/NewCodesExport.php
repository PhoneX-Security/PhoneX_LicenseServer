<?php namespace Phonex\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\BusinessCodesExport;
use Phonex\Group;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Model\CodeExportType;
use Phonex\User;

/**
 * Class CreateBusinessCodePair
 * @package Phonex\Jobs
 */
abstract class NewCodesExport extends Command implements SelfHandling {
    const TYPE = CodeExportType::SINGLE;// default type single, rewrite it if required

    private $licenseType;
    private $licenseFuncType;
    private $group;
    private $prefix;
    private $expiresAt;
    private $parent;
    private $licenseLimitPerCode;
    private $count;
    private $comment;

    public function __construct($pairsCount, LicenseType $licenseType, LicenseFuncType $licenseFuncType, $licenseLimitPerCode, callable $callback = null)
    {
        $this->count = $pairsCount;
        $this->licenseLimitPerCode = $licenseLimitPerCode;
        $this->licenseType = $licenseType;
        $this->licenseFuncType = $licenseFuncType;
        $this->callback = $callback;
    }

    public function addPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function addParent(User $parent){
        $this->parent = $parent;
    }

    public function addGroup(Group $group){
        $this->group = $group;
    }

    public function addExpiration(Carbon $expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    public function addComment($comment)
    {
        $this->comment = $comment;
    }

	public function handle(){
        $export = new BusinessCodesExport();
        $export->creator_id = \Auth::user()->id;

        $export->license_type_id = $this->licenseType->id;
        $export->license_func_type_id = $this->licenseFuncType->id;
        $export->license_limit_per_code = $this->licenseLimitPerCode;
        $export->type = static::TYPE; // late static binding

        if ($this->parent){
            $export->parent_id = $this->parent->id;
        }
        if($this->expiresAt){
            $export->expires_at = $this->expiresAt;
        }
        if($this->group){
            $export->group_id = $this->group->id;
        }
        if($this->comment){
            $export->comment = $this->comment;
        }
        $export->save();

        $codes = [];
        for($i = 0; $i < $this->count; $i++){
            $codes[] = call_user_func($this->callback, $export->id, $this->prefix);
        }
        return [$export, $codes];
	}
}
