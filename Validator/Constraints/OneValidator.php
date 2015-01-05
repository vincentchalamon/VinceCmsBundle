<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <http://www.vincent-chalamon.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\ConstraintValidator;

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

        /** @var One $constraint */
        foreach ($constraint->constraints as $constr) {
            /** @var Regex|Url $constr */
            $this->context->validateValue($value, $constr, '', $group);
        }

        // One constraint at least is valid
        $count = $this->context->getViolations()->count();
        for ($i = $count < count($constraint->constraints) ? 0 : 1; $i < $count; $i++) {
            $this->context->getViolations()->remove($i);
        }
    }
}
