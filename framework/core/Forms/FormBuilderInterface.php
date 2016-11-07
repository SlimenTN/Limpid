<?php
/**
 * Created by PhpStorm.
 * User: Slimen-PC
 * Date: 02/11/2016
 * Time: 14:33
 */

namespace framework\core\Forms;

/**
 * Interface FormBuilderInterface
 * @package framework\core\Forms
 * 
 */
interface FormBuilderInterface
{
    /**
     * @param FormBuilder $builder
     * @return FormBuilder
     */
    public function buildFormPrototype(FormBuilder $builder);
}