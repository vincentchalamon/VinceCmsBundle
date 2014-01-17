<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @api
 */
class OneValidator extends ConstraintValidator
{

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $group = $this->context->getGroup();

        foreach ($constraint->constraints as $constr) {
            /** @var Regex|Url $constr */
            $this->context->validateValue($value, $constr, '', $group);
        }

        // One constraint at least is valid
        if ($this->context->getViolations()->count() < count($constraint->constraints)) {
            for ($i = 0; $i < $this->context->getViolations()->count(); $i++) {
                $this->context->getViolations()->remove($i);
            }
        } else {
            for ($i = 1; $i < $this->context->getViolations()->count(); $i++) {
                $this->context->getViolations()->remove($i);
            }
        }
    }
}
