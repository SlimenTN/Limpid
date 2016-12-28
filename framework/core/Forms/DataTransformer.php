<?php
namespace framework\core\Forms;

/**
 * Interface DataTransformer
 * Transform data type while setting it in the form or extracting it
 * @package framework\core\Forms
 * 
 * Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
interface DataTransformer
{
    /**
     * @param object $object
     * @return object
     */
    public function in($object);

    /**
     * @param object $object
     * @return object
     */
    public function out($object);
}