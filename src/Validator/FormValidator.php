<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class FormValidator extends ConstraintValidator
{
    private const LV_DOMAIN = '@link-value.fr';

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint Form */
        if (!$constraint instanceof Form) {
            throw new UnexpectedTypeException($constraint, Form::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!strpos($value, self::LV_DOMAIN)) {
            $this->context->buildViolation($constraint->notLinkValue)
                ->addViolation();
        }
    }
}
