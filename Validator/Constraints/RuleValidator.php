<?php

namespace KPhoen\RulerZBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Hoa\Compiler\Exception as CompilerException;
use Hoa\Ruler\Model as AST;
use RulerZ\Model\Rule;
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

        $this->validateVariableAccesses($model, $constraint);
        $this->validateOperators($model, $constraint);
    }

    private function validateOperators(Rule $model, Constraint $constraint)
    {
        if ($constraint->allowed_operators === null) {
            return;
        }

        $operators = array_map(function (AST\Operator $element) {
            return strtolower($element->getName());
        }, $model->getOperators());

        foreach ($operators as $operator) {
            if (!in_array($operator, $constraint->allowed_operators)) {
                $this->context
                    ->buildViolation($constraint->operatorNotAllowedMessage)
                    ->setParameter('%operator%', $operator)
                    ->addViolation();
            }
        }
    }

    private function validateVariableAccesses(Rule $model, Constraint $constraint)
    {
        if ($constraint->allowed_variables === null) {
            return;
        }

        $accesses = array_map(function (AST\Bag\Context $element) {
            $flattenedDimensions = [$element->getId()];
            foreach ($element->getDimensions() as $dimension) {
                $flattenedDimensions[] = $dimension[1];
            }

            return strtolower(implode('.', $flattenedDimensions));
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
