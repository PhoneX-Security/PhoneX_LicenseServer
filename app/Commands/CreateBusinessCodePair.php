<?php namespace Phonex\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\BusinessCode;
use Phonex\BusinessCodeClMapping;
use Phonex\BusinessCodesExport;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\User;

/**
 * Class CreateBusinessCodePair
 * @package Phonex\Commands
 */
class CreateBusinessCodePair extends Command implements SelfHandling {
    private $creator;
    private $licenseType;
    private $licenseFuncType;
    private $group;
    private $export;
    private $prefix;

    private $parent;

    /**
     * @param User $creator
     * @param LicenseType $licenseType
     * @param LicenseFuncType $licenseFuncType
     * @param BusinessCodesExport $export
     * @param string $prefix
     */
    public function __construct(User $creator, LicenseType $licenseType, LicenseFuncType $licenseFuncType, BusinessCodesExport $export = null, $prefix = ''){

        $this->creator = $creator;
        $this->licenseType = $licenseType;
        $this->prefix = $prefix;
        $this->export = $export;
        $this->licenseFuncType = $licenseFuncType;
    }

    public function addParent(User $parent){
        $this->parent = $parent;
    }

    public function addGroup(Group $group){
        $this->group = $group;
    }

	public function handle(){
        $codes = [];
        // first code

        $bc1 = new BusinessCode();
        $bc1->code = BusinessCode::generateUniqueCode($this->prefix);
        $bc1->creator_id = $this->creator->id;

        if($this->group){
            $bc1->group_id = $this->group->id;
        }
        if ($this->parent){
            $bc1->parent_id = $this->parent->id;
        }

        $bc1->license_type_id = $this->licenseType->id;
        $bc1->license_func_type_id = $this->licenseFuncType->id;
        $bc1->users_limit = 1; // only one license per this code
        $bc1->is_active = 1;
        if ($this->export){
            $bc1->export_id = $this->export->id;
        }

        // second code
        $bc2 = clone $bc1;
        $bc2->code = BusinessCode::generateUniqueCode($this->prefix);

        $bc1->save();
        $bc2->save();
        event(AuditEvent::create('business_code', $bc1->id));
        event(AuditEvent::create('business_code', $bc2->id));

        $codes[] = $bc1;
        $codes[] = $bc2;

        $mapping1 = new BusinessCodeClMapping();
        $mapping1->cl_owner_bcode_id = $bc1->id;
        $mapping1->contact_bcode_id = $bc2->id;
        $mapping1->save();

        $mapping2 = new BusinessCodeClMapping();
        $mapping2->cl_owner_bcode_id = $bc2->id;
        $mapping2->contact_bcode_id = $bc1->id;
        $mapping2->save();

        return $codes;
	}

}
