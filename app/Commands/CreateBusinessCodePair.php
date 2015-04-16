<?php namespace Phonex\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\BusinessCode;
use Phonex\BusinessCodeClMapping;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\User;

/**
 * Class CreateBusinessCodePair - command primarily for MobilPohotovost business codes
 * @package Phonex\Commands
 */
class CreateBusinessCodePair extends Command implements SelfHandling {

    /**
     * @var User
     */
    private $creator;
    /**
     * @var LicenseType
     */
    private $licenseType;
    private $licenseFuncType;
    /**
     * @var Group
     */
    private $group;
    /**
     * @var int
     */
    private $exported;
    /**
     * @var string
     */
    private $prefix;

    /**
     * @param User $creator
     * @param LicenseType $licenseType
     * @param LicenseFuncType $licenseFuncType
     * @param Group $group
     * @param int $exported
     * @param string $prefix
     */
    public function __construct(User $creator, LicenseType $licenseType, LicenseFuncType $licenseFuncType, Group $group, $exported = 0, $prefix = ''){

        $this->creator = $creator;
        $this->licenseType = $licenseType;
        $this->group = $group;
        $this->exported = $exported;
        $this->prefix = $prefix;
        $this->licenseFuncType = $licenseFuncType;
    }

	public function handle(){
        $codes = [];
        // first code

        $bc1 = new BusinessCode();
        $bc1->code = BusinessCode::generateUniqueCode($this->prefix);
        $bc1->group_id = $this->group->id;
        $bc1->creator_id = $this->creator->id;

        $bc1->license_type_id = $this->licenseType->id;
        $bc1->license_func_type_id = $this->licenseFuncType->id;
        $bc1->users_limit = 1; // only one license per this code
        $bc1->is_active = 1;
        $bc1->exported = $this->exported;

        // second code
        $bc2 = clone $bc1;
        $bc2->code = BusinessCode::generateUniqueCode($this->prefix);

        $bc1->save();
        $bc2->save();
        if (!\Auth::guest()){
            event(AuditEvent::create('business_code', $bc1->id));
            event(AuditEvent::create('business_code', $bc2->id));
        }

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
