<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


class ZipCodeValidator extends ConstraintValidator
{
    /** @var int */
    public $length = 5;

    /**
     * ZipCodeValidator constructor.
     */
    public function __construct(string $locale)
    {
        if('en_NZ' === $locale) {
            $this->length =  4;
        }
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ZipCode) {
            throw new UnexpectedTypeException($constraint, ZipCode::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $pattern = '/^[0-9]{' . $this->length . '}$/';
        if (!preg_match($pattern, $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
