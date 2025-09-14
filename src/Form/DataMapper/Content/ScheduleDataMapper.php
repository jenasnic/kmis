<?php

namespace App\Form\DataMapper\Content;

use App\Entity\Content\Calendar;
use App\Entity\Content\Schedule;
use App\Entity\Content\Sporting;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormInterface;

class ScheduleDataMapper implements DataMapperInterface
{
    public function __construct(private readonly Calendar $calendar)
    {
    }

    /**
     * @param Schedule|null $viewData
     * @param \Traversable<FormInterface<mixed>> $forms
     */
    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof Schedule) {
            //            dd('aaa', $viewData);
            return;
        }

        $forms = iterator_to_array($forms);

        $forms['start']->setData($viewData->getStart());
        $forms['end']->setData($viewData->getEnd());
        $forms['detail']->setData($viewData->getDetail());
        $forms['sporting']->setData($viewData->getSporting());
    }

    /**
     * @param \Traversable<FormInterface<mixed>> $forms
     * @param Schedule|null $viewData
     */
    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        try {
            /** @var string|null $start */
            $start = $forms['start']->getData();
            /** @var string|null $end */
            $end = $forms['end']->getData();
            /** @var string|null $detail */
            $detail = $forms['detail']->getData();
            /** @var Sporting|null $sporting */
            $sporting = $forms['sporting']->getData();

            if (null === $viewData) {
                $viewData = new Schedule($this->calendar);
            }

            $viewData->setStart($start);
            $viewData->setEnd($end);
            $viewData->setDetail($detail);
            $viewData->setSporting($sporting);
        } catch (\Exception $e) {
            throw new TransformationFailedException('Unable to map data for schedule', 0, $e);
        }
    }
}
