<?php namespace Phonex\Model;

use Phonex\Utils\BasicEnum;

abstract class StatusNotificationState extends BasicEnum
{
    const CREATED = 1;
    const PROCESSING = 2;
    const SENT = 3;
}