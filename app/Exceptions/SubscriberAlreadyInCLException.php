<?php namespace Phonex\Exceptions;
use Exception;
use Phonex\Subscriber;

/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 16.2.15
 * Time: 10:47
 */

class SubscriberAlreadyInCLException extends Exception {
    public $clOwner;
    public $subscriberToBeAdded;

    function __construct(Subscriber $clOwner, Subscriber $subscriberToBeAdded)
    {
        $this->clOwner = $clOwner;
        $this->subscriberToBeAdded = $subscriberToBeAdded;
    }
}