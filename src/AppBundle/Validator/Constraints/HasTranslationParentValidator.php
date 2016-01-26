<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Category;
use AppBundle\Util\Locales;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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

    private function selectCategoryParentMultilangue($id)
    {
        return $this->em->getRepository('AppBundle:Category')
            ->selectCategoryParentMultilangue($id, $this->locales->getLocaleActive());
    }
}
