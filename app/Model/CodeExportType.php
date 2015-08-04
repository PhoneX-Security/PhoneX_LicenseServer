<?php namespace Phonex\Model;

use Phonex\Utils\BasicEnum;

abstract class CodeExportType extends BasicEnum
{
    const SINGLE = "single";
    const PAIRS = "pairs";
}