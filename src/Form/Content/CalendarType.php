<?php

namespace App\Form\Content;

use App\Entity\Content\Calendar;
use App\Entity\Content\Location;
use App\Enum\DayOfWeekEnum;
use App\Form\Type\BulmaCollectionType;
use App\Form\Type\EnumType;
use App\Repository\Content\LocationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<Calendar>
 */
class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', EnumType::class, ['enum' => DayOfWeekEnum::class])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'query_builder' => function (LocationRepository $locationRepository) {
                    return $locationRepository->createQueryBuilder('location')->orderBy('location.rank');
                },
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Calendar $calendar */
            $calendar = $event->getData();

            $event->getForm()->add('schedules', BulmaCollectionType::class, [
                'label' => false,
                'entry_type' => ScheduleType::class,
                'entry_options' => [
                    'label' => false,
                    'calendar' => $calendar,
                ],
                'block_prefix' => 'calendar_schedule_list',
                'allow_add' => true,
                'allow_delete' => true,
                'add_label_id' => 'form.calendar.addSchedule',
                'collection_css_class' => 'schedule-list',
                'remove_button_position' => 'start',
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'label_format' => 'form.calendar.%name%',
        ]);
    }
}
