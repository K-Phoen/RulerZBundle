<?php

namespace KPhoen\RulerZBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Hoa\Compiler\Exception as CompilerException;
use RulerZ\Parser\Parser;

class RuleValidator extends ConstraintValidator
{
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function validate($rule, Constraint $constraint)
    {
        try {
            $this->parser->parse($rule);
        } catch (CompilerException $e) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('%rule%', $rule)
                ->addViolation();
        }
    }
}
