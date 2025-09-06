<?php

namespace App\Form\Content;

use App\Entity\Content\Calendar;
use App\Entity\Content\Schedule;
use App\Entity\Content\Sporting;
use App\Form\DataMapper\Content\ScheduleDataMapper;
use App\Form\Type\MaskedType;
use App\Repository\Content\SportingRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<Schedule>
 */
class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Calendar $calendar */
        $calendar = $options['calendar'];

        $builder
            ->add('start', MaskedType::class, ['mask' => MaskedType::TIME_MASK])
            ->add('end', MaskedType::class, ['mask' => MaskedType::TIME_MASK])
            ->add('detail', TextType::class, ['required' => false])
            ->add('sporting', EntityType::class, [
                'required' => false,
                'class' => Sporting::class,
                'choice_label' => 'name',
                'query_builder' => function (SportingRepository $sportingRepository) {
                    return $sportingRepository->createQueryBuilder('sporting')->orderBy('sporting.rank');
                },
            ])
            ->setDataMapper(new ScheduleDataMapper($calendar))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('calendar');
        $resolver->setAllowedTypes('calendar', Calendar::class);

        $resolver->setDefaults([
            'data_class' => Schedule::class,
            'label_format' => 'form.schedule.%name%',
            'empty_data' => null,
        ]);
    }
}
