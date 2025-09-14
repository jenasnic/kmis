<?php

namespace App\Domain\Command\Front;

use App\Domain\Command\AbstractRegistrationHandler;
use App\Enum\DiscountCodeEnum;
use App\Service\Email\EmailBuilder;
use App\Service\Email\EmailSender;
use App\Service\File\FileManager;
use Doctrine\ORM\EntityManagerInterface;

final class ReEnrollmentHandler extends AbstractRegistrationHandler
{
    public function __construct(
        FileManager $fileManager,
        private readonly EntityManagerInterface $entityManager,
        private readonly EmailBuilder $emailBuilder,
        private readonly EmailSender $emailSender,
    ) {
        parent::__construct($fileManager);
    }

    public function handle(ReEnrollmentCommand $command): void
    {
        $registration = $command->registration;

        $this->processUpload($registration);

        if (null !== $command->reEnrollmentToken) {
            $this->entityManager->remove($command->reEnrollmentToken);
        }

        $this->entityManager->persist($registration);
        $this->entityManager->flush();

        if ($command->sendEmail) {
            /** @var string $adherentEmail */
            $adherentEmail = $registration->getAdherent()->getEmail();
            $discountCode = DiscountCodeEnum::getDiscountCode($registration);

            /** @var float $amountToPay */
            $amountToPay = $registration->getPriceOption()?->getAmount();
            if (null !== $discountCode) {
                $amountToPay -= DiscountCodeEnum::getDiscountAmount($discountCode);
            }

            $email = $this->emailBuilder
                ->useTemplate('email/re_enrollment_confirmed.html.twig', [
                    'registration' => $registration,
                    'discountCode' => $discountCode,
                    'amountToPay' => $amountToPay,
                    'paymentLink' => $registration->getSeason()->getPaymentLink(),
                ])
                ->fromDefault()
                ->to($adherentEmail)
                ->copy()
                ->getEmail()
            ;

            $this->emailSender->sendEmail($email);
        }
    }
}
