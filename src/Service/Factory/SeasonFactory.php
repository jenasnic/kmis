<?php

namespace App\Service\Factory;

use App\Entity\Payment\PriceOption;
use App\Entity\Season;
use App\Exception\SeasonAlreadyDefinedException;
use App\Repository\SeasonRepository;

class SeasonFactory
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
    ) {
    }

    /**
     * @throws SeasonAlreadyDefinedException
     */
    public function createNew(): Season
    {
        $currentYear = (new \DateTime())->format('Y');

        if ($this->seasonRepository->existForYear($currentYear)) {
            throw new SeasonAlreadyDefinedException(sprintf('A season is already set for year %s', $currentYear));
        }

        $season = new Season($currentYear);

        $season->setStartDate(new \DateTime(sprintf('%s-09-01', $currentYear)));
        $season->setEndDate(new \DateTime(sprintf('%s-08-31', (int) $currentYear + 1)));

        // Recover data from current active season
        $activeSeason = $this->seasonRepository->getActiveSeason();
        if (null !== $activeSeason) {
            $season->setPricingNote($activeSeason->getPricingNote());
            foreach ($activeSeason->getPriceOptions() as $option) {
                $newOption = new PriceOption($option->getLabel(), $option->getAmount(), $season);
                $newOption->setRank($option->getRank());

                $season->addPriceOption($newOption);
            }
        }

        return $season;
    }
}
