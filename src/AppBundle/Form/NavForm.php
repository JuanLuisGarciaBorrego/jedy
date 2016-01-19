<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NavForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Id nav',
                ]
            );
        $builder->add('contentsNav', CollectionType::class, array(
            'label' => false,

            'entry_type' => ContentsNavForm::class,
            'entry_options' => ['contentsNav' => $this->createArray($options['contentsNav'])],
            'allow_add' => true,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Nav',
                'contentsNav' => null
            )
        );
    }

    private function createArray($contentsNav)
    {
        if (!empty($contentsNav)) {
            $arrayContent = [];

            foreach ($contentsNav as $item) {
                $arrayContent[$item['name']] = $item['idElement'];
            }

            return $arrayContent;
        }else{
            return null;
        }
    }
}
