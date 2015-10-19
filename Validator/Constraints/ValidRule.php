<?php

namespace KPhoen\RulerZBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class ValidRule extends Constraint
{
    public $invalidMessage            = 'The rule « %rule% » is invalid.';
    public $accessNotAllowedMessage   = 'The variable « %access% » does not exist.';
    public $operatorNotAllowedMessage = 'The operator « %operator% » does not exist.';

    public $allowed_variables;
    public $allowed_operators;

    public function __construct($options = null)
    {
        $options = array_merge([
            'allowed_variables' => null,
            'allowed_operators' => null,
        ], $options);

        if (is_array($options['allowed_operators'])) {
            $options['allowed_operators'] = array_map('strtolower', $options['allowed_operators']);
        }

        if (is_array($options['allowed_variables'])) {
            $options['allowed_variables'] = array_map('strtolower', $options['allowed_variables']);
        }

        parent::__construct($options);

        if (!array_key_exists('allowed_variables', $options)) {
            throw new MissingOptionsException(sprintf('Option "allowed_variables" must be given for constraint %s', __CLASS__), ['allowed_variables']);
        }

        if (!array_key_exists('allowed_operators', $options)) {
            throw new MissingOptionsException(sprintf('Option "allowed_operators" must be given for constraint %s', __CLASS__), ['allowed_operators']);
        }
    }

    public function getRequiredOptions()
    {
        return [
            'allowed_variables',
            'allowed_operators',
        ];
    }

    public function validatedBy()
    {
        return 'rulerz_rule_validator';
    }
}
