<?php

namespace KPhoen\RulerZBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidRule extends Constraint
{
    public $message = 'The rule « %rule% » is invalid.';

    public function validatedBy()
    {
        return 'rulerz_rule_validator';
    }
}
