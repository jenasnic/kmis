<?php

namespace App\Form\Content;

use App\Entity\Content\Location;
use App\Form\Type\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<Location>
 */
class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('address', AddressType::class, ['with_street_2' => true])
            ->add('latitude', TextType::class, ['required' => false])
            ->add('longitude', TextType::class, ['required' => false])
            ->add('localization', TextType::class, ['required' => false])
            ->add('active', CheckboxType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'label_format' => 'form.location.%name%',
        ]);
    }
}
