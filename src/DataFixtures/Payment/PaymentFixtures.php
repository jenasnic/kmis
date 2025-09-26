<?php

namespace App\DataFixtures\Payment;

use App\DataFixtures\ConfigurationFixtures;
use App\DataFixtures\Factory\Payment\AncvPaymentFactory;
use App\DataFixtures\Factory\Payment\CashPaymentFactory;
use App\DataFixtures\Factory\Payment\CheckPaymentFactory;
use App\DataFixtures\Factory\Payment\HelloAssoPaymentFactory;
use App\DataFixtures\Factory\Payment\RefundHelpPaymentFactory;
use App\DataFixtures\Factory\Payment\TransferPaymentFactory;
use App\DataFixtures\Factory\RegistrationFactory;
use App\DataFixtures\Factory\SeasonFactory;
use App\DataFixtures\RegistrationFixtures;
use App\DataFixtures\SeasonFixtures;
use App\Domain\Model\Content\RefundHelpConfiguration;
use App\Entity\Payment\PriceOption;
use App\Entity\Registration;
use App\Entity\Season;
use App\Enum\RefundHelpEnum;
use App\Helper\FloatHelper;
use App\Service\Configuration\RefundHelpManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Zenstruck\Foundry\Persistence\Proxy;

class PaymentFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(
        private readonly RefundHelpManager $refundHelpManager,
    ) {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $refundHelpConfiguration = $this->refundHelpManager->getRefundHelpConfiguration();

        /** @var array<Proxy<Registration>> $registrations */
        $registrations = RegistrationFactory::all();
        /** @var array<Proxy<Season>> $seasons */
        $seasons = SeasonFactory::all();

        foreach ($registrations as $registration) {
            $registration = $registration->_real();

            foreach ($seasons as $season) {
                $season = $season->_real();

                if ($season->getId() === $registration->getSeason()->getId()) {
                    $soldPayment = !$season->isActive() || $this->faker->boolean(60);
                    $this->createPaymentsForSeason($registration, $season, $soldPayment, $refundHelpConfiguration);
                } elseif (
                    $registration->isReEnrollment()
                    && (int) $season->getLabel() === (int) $registration->getSeason()->getLabel() - 1
                ) {
                    $this->createPaymentsForSeason($registration, $season, true, $refundHelpConfiguration);
                }
            }
        }
    }

    /**
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return [
            ConfigurationFixtures::class,
            RegistrationFixtures::class,
            SeasonFixtures::class,
        ];
    }

    protected function createPaymentsForSeason(Registration $registration, Season $season, bool $sold, RefundHelpConfiguration $refundHelpConfiguration): void
    {
        /** @var PriceOption $priceOption */
        $priceOption = $registration->getPriceOption();
        $amount = $priceOption->getAmount();

        /** @var \DateTime $startDate */
        $startDate = $season->getStartDate();
        $paymentDate = $startDate->add(\DateInterval::createFromDateString('+1 month'));

        $paymentAttributes = [
            'adherent' => $registration->getAdherent(),
            'season' => $season,
            'date' => $paymentDate,
        ];

        if ($registration->isUsePassCitizen()) {
            $discountAmount = $refundHelpConfiguration->passCitizenAmount;
            $amount -= $discountAmount;
            $paymentAttributes['amount'] = $discountAmount;
            RefundHelpPaymentFactory::createOne(array_merge(
                $paymentAttributes,
                [
                    'refundHelp' => RefundHelpEnum::PASS_CITIZEN,
                    'reference' => 'PASS-'.$this->faker->numberBetween(1000, 9999),
                ],
            ));
        }

        if ($registration->isUsePassSport()) {
            $discountAmount = $refundHelpConfiguration->passSportAmount;
            $amount -= $discountAmount;
            $paymentAttributes['amount'] = $discountAmount;
            RefundHelpPaymentFactory::createOne(array_merge(
                $paymentAttributes,
                [
                    'refundHelp' => RefundHelpEnum::PASS_SPORT,
                    'reference' => 'PASS-'.$this->faker->numberBetween(1000, 9999),
                ],
            ));
        }

        if ($registration->isUseCCAS()) {
            $discountAmount = $refundHelpConfiguration->ccasAmount;
            $amount -= $discountAmount;
            $paymentAttributes['amount'] = $discountAmount;
            RefundHelpPaymentFactory::createOne(array_merge(
                $paymentAttributes,
                [
                    'refundHelp' => RefundHelpEnum::CCAS,
                    'reference' => 'CCAS-'.$this->faker->numberBetween(1000, 9999),
                ],
            ));
        }

        if ($this->faker->boolean(20)) {
            $paymentAttributes['amount'] = $amount;
            HelloAssoPaymentFactory::createOne($paymentAttributes);
        } else {
            do {
                $newAmount = ($amount < 100 || $this->faker->boolean()) ? $amount : 60;
                $paymentDate = $paymentDate->add(\DateInterval::createFromDateString('+1 month'));

                if (!$sold && FloatHelper::equals($amount, $newAmount)) {
                    break;
                }

                $paymentAttributes['amount'] = $newAmount;
                $paymentAttributes['date'] = $paymentDate;
                $this->createPayment($paymentAttributes);

                $amount -= $newAmount;
            } while (FloatHelper::greater($amount, 0));
        }
    }

    /**
     * @param array<string, mixed> $attributes
     */
    protected function createPayment(array $attributes): void
    {
        $type = $this->faker->randomElement(['ancv', 'cash', 'check', 'transfer']);

        match ($type) {
            'ancv' => AncvPaymentFactory::createOne($attributes),
            'cash' => CashPaymentFactory::createOne($attributes),
            'check' => CheckPaymentFactory::createOne($attributes),
            'transfer' => TransferPaymentFactory::createOne($attributes),
            default => throw new \LogicException('invalid type')
        };
    }
}
