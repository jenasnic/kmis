<?php

namespace App\Form\Content;

use App\Entity\Content\Schedule;
use App\Entity\Content\Sporting;
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
        $builder
            ->add('start', MaskedType::class, ['mask' => MaskedType::TIME_MASK])
            ->add('end', MaskedType::class, ['mask' => MaskedType::TIME_MASK])
            ->add('detail', TextType::class)
            ->add('sporting', EntityType::class, [
                'class' => Sporting::class,
                'choice_label' => 'name',
                'query_builder' => function (SportingRepository $sportingRepository) {
                    return $sportingRepository->createQueryBuilder('sporting')->orderBy('sporting.rank');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
            'label_format' => 'form.schedule.%name%',
        ]);
    }
}
