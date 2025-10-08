<?php

namespace App\Form;

use App\Entity\Season;
use App\Form\Payment\PriceOptionType;
use App\Form\Type\BulmaCollectionType;
use App\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<Season>
 */
class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, ['widget' => 'single_text'])
            ->add('endDate', DateType::class, ['widget' => 'single_text'])
            ->add('paymentLink', TextType::class, ['required' => false])
            ->add('licenceLink', TextType::class, ['required' => false])
            ->add('pricingNote', WysiwygType::class, [
                'required' => false,
                'size' => 'small',
                'toolbar' => 'code | bold underline link forecolor',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Season $season */
            $season = $event->getData();

            $event->getForm()->add('priceOptions', BulmaCollectionType::class, [
                'label' => false,
                'entry_type' => PriceOptionType::class,
                'entry_options' => [
                    'label' => false,
                    'season' => $season,
                ],
                'block_prefix' => 'season_price_option_list',
                'allow_add' => true,
                'allow_delete' => true,
                'add_label_id' => 'form.season.addPriceOption',
                'collection_css_class' => 'price-option-list',
                'sortable' => true,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
            'label_format' => 'form.season.%name%',
        ]);
    }
}
