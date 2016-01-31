<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasTranslationParent extends Constraint
{
    public $message = 'You must create the translations of the category "%string%" first.';

    public function validatedBy()
    {
        return 'has_translation_parent';
    }
}
