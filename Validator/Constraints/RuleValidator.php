<?php

namespace KPhoen\RulerZBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Hoa\Compiler\Exception as CompilerException;
use Hoa\Ruler\Model as AST;
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
            $model = $this->parser->parse($rule);
        } catch (CompilerException $e) {
            $this->context
                ->buildViolation($constraint->invalidMessage)
                ->setParameter('%rule%', $rule)
                ->addViolation();
            return;
        }

        if ($constraint->allowed_variables === null) {
            return;
        }

        $accesses = array_map(function(AST\Bag\Context $element) {
            $flattenedDimensions = [$element->getId()];
            foreach ($element->getDimensions() as $dimension) {
                $flattenedDimensions[] = $dimension[1];
            }

            return implode('.', $flattenedDimensions);
        }, $model->getAccesses());

        foreach ($accesses as $access) {
            if (!in_array($access, $constraint->allowed_variables)) {
                $this->context
                    ->buildViolation($constraint->accessNotAllowedMessage)
                    ->setParameter('%access%', $access)
                    ->addViolation();
            }
        }
    }
}
