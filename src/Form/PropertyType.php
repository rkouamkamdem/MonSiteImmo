<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('floor')
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('sold')
            ->add('created_at')
            ->add('price')
            ->add('heat', ChoiceType::class, [
                //La fonction array_flip() remplace les valeurs par les clés et les clés par les valeurs et inversément.
                'choices' => array_flip(Property::HEAT)
                ])
            ->add('bedrooms')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            //Ici on ajoute ce paramètre pour la traduction du formulaire,
            //Ainsi, à la racine du projet, on aura nos fichiers de traduction: translation/forms.fr.yml et translation/forms.en.yml
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices(){
        //Je récupère ma variable constante qui est un tableau
        $choices = Property::HEAT;
    }
}
