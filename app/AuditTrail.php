<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;
use Phonex\Events\AuditEvent;

/**
 * @property string operation
 * @property null entity_name
 * @property null entity_id
 * @property null field_name
 * @property null old_value
 * @property null new_value
 */
class AuditTrail extends Model{
    protected $table = 'audit_trail';

    // called automatically by IoC
    public static function boot(){
        parent::boot();
        // fill in user id automatically by hooked callback
        self::creating(function($auditTrial){
            $auditTrial->user_id = \Auth::user()->id;
        });
    }

    public static function fillFrom(AuditEvent $event){
        $auditTrail = new AuditTrail();
        $auditTrail->operation = $event->operation;
        $auditTrail->entity_name = $event->entityName;
        $auditTrail->entity_id = $event->entityId;
        $auditTrail->field_name = $event->fieldName;
        $auditTrail->old_value = $event->oldValue;
        $auditTrail->new_value = $event->newValue;
        return $auditTrail;
    }
}
