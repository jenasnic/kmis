<?php

namespace App\Form\Content;

use App\Entity\Content\Location;
use App\Form\Type\AddressType;
use Symfony\Component\Form\AbstractType;
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
            ->add('address', AddressType::class)
            ->add('latitude', TextType::class)
            ->add('longitude', TextType::class)
            ->add('localization', TextType::class)
            ->add('active', TextType::class)
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
