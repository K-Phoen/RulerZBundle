<?php

namespace Tests\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use RulerZ\Parser\Parser;
use KPhoen\RulerZBundle\Validator\Constraints\RuleValidator;
use KPhoen\RulerZBundle\Validator\Constraints\ValidRule;

class RuleValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validRulesProvider
     */
    public function testValidateWithValidRules($rule, Constraint $constraint)
    {
        $context = $this->getExecutionContextMock();
        $context->expects($this->never())->method('buildViolation');

        $validator = new RuleValidator(new Parser());
        $validator->initialize($context);

        $validator->validate($rule, $constraint);
    }

    /**
     * @dataProvider invalidRulesProvider
     */
    public function testValidateWithInvalidRules($rule, Constraint $constraint)
    {
        $context = $this->getExecutionContextMock();
        $context
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->getConstraintViolationBuilderMock()));

        $validator = new RuleValidator(new Parser());
        $validator->initialize($context);

        $validator->validate($rule, $constraint);
    }

    public function validRulesProvider()
    {
        $simpleRuleConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => null, // all
        ]);

        $checkOperatorsConstraint = new ValidRule([
            'allowed_operators' => ['=', 'AND'],
            'allowed_variables' => null, // all
        ]);

        $checkVariablesConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => ['foo', 'bar', 'readingTime']
        ]);

        return [
            ['foo = 42', $simpleRuleConstraint],
            ['foo = 42 AND bar = joe(foo)', $simpleRuleConstraint],

            ['foo = 42', $checkOperatorsConstraint],
            ['foo = 42 AND bar = foo', $checkOperatorsConstraint],

            ['foo = 42', $checkVariablesConstraint],
            ['readingTime <= 42', $checkVariablesConstraint],
            ['foo = 42 AND bar = foo', $checkOperatorsConstraint],
        ];
    }

    public function inValidRulesProvider()
    {
        $simpleRuleConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => null, // all
        ]);

        $checkOperatorsConstraint = new ValidRule([
            'allowed_operators' => ['=', 'AND'],
            'allowed_variables' => null, // all
        ]);

        $checkVariablesConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => ['foo', 'bar']
        ]);

        return [
            // syntax errors
            ['foo = 42 AND', $simpleRuleConstraint],
            ['foo = 42 AND bar = joe(foo', $simpleRuleConstraint],

            // invalid operator
            ['foo != 42', $checkOperatorsConstraint],
            ['foo = 42 OR bar = foo', $checkOperatorsConstraint],

            // unknown variable
            ['foo = baz', $checkVariablesConstraint],
            ['foo.bar = 42', $checkVariablesConstraint],
        ];
    }

    private function getExecutionContextMock()
    {
        return $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getConstraintViolationBuilderMock()
    {
        $builder = $this->getMock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        $builder->expects($this->any())
            ->method('setParameter')
            ->will($this->returnSelf());

        return $builder;
    }
}
