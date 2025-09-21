<?php

namespace App\Form\Content;

use App\Entity\Content\Sporting;
use App\Form\Type\BulmaFileType;
use App\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 * @template-extends AbstractType<Sporting>
 */
class SportingType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('tagline', TextType::class)
            ->add('content', WysiwygType::class, ['help' => 'form.wysiwyg.help'])
            ->add('active', CheckboxType::class, ['required' => false])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Sporting|null $sporting */
            $sporting = $event->getData();

            $fieldOptions = [
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                    ]),
                ],
            ];

            if (null !== $sporting?->getPictureUrl()) {
                $fieldOptions['download_uri'] = $this->router->generate('app_sporting_picture', ['sporting' => $sporting->getId()]);
            }

            $form->add('pictureFile', BulmaFileType::class, $fieldOptions);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sporting::class,
            'label_format' => 'form.sporting.%name%',
        ]);
    }
}
