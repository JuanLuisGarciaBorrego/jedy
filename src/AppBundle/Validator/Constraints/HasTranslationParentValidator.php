<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Category;
use AppBundle\Util\Locales;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class HasTranslationParentValidator
 *
 * Make sure the selected category have all translations.
 * This is necessary because the translations of the object are automatically associated.
 *
 * @package AppBundle\Validator\Constraints
 */
class HasTranslationParentValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Locales
     */
    private $locales;

    /**
     * @param EntityManager $em
     * @param Locales       $locales
     */
    public function __construct(EntityManager $em, Locales $locales)
    {
        $this->em = $em;
        $this->locales = $locales;
    }

    /**
     * @param mixed $category
     * @param Constraint $constraint
     */
    public function validate($category, Constraint $constraint)
    {
        if ($category && $category->getLocale() == $this->locales->getLocaleActive()) {
            if ($this->selectCategoryParentMultilangue($category->getId()) != (count(
                        $this->locales->getLocales()
                    ) - 1)
            ) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%string%', $category->getName())
                    ->addViolation();
            }
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    private function selectCategoryParentMultilangue($id)
    {
        return $this->em->getRepository('AppBundle:Category')
            ->selectCategoryParentMultilangue($id, $this->locales->getLocaleActive());
    }
}
