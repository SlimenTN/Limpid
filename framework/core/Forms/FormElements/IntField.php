<?php

namespace framework\core\Forms\FormElements;

/**
 * Integer
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class IntField extends NumberField
{
    /**
     * Field type
     */
    protected $type = 'number';

    /**
     * Step
     */
    protected $step = 1;

    public function check()
    {
        if (!$this->required && !$this->value) {
            return;
        }

        $error = parent::check();

        if ($error) {
            return $error;
        }

        if ((int)($this->value) != $this->value) {
            return array('integer', $this->printName());
        }

        return;
    }
}

