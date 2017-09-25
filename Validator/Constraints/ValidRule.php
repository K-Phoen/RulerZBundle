<?php

namespace KPhoen\RulerZBundle\Validator\Constraints;

use Symfony\Bridge\RulerZ\Validator\Constraints\ValidRule as BridgeValidRule;

/**
 * @Annotation
 */
class ValidRule extends BridgeValidRule
{
    public function validatedBy()
    {
        return 'rulerz_rule_validator';
    }
}
