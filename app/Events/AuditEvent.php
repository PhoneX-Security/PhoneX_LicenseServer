<?php namespace Phonex\Events;

use Phonex\Events\Event;

use Illuminate\Queue\SerializesModels;

class AuditEvent extends Event {

	use SerializesModels;
    /**
     * @var
     */
    public $operation;
    /**
     * @var null
     */
    public $entityName;
    /**
     * @var null
     */
    public $entityId;
    /**
     * @var null
     */
    public $fieldName;
    /**
     * @var null
     */
    public $oldValue;
    /**
     * @var null
     */
    public $newValue;

    /**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($operation,
                                $entityName = null,
                                $entityId = null,
                                $fieldName = null,
                                $oldValue = null,
                                $newValue = null){


        $this->operation = $operation;
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->fieldName = $fieldName;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }

}
