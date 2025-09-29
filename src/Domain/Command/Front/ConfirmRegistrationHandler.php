<?php

namespace App\Domain\Command\Front;

use App\Enum\RefundHelpEnum;
use App\Service\Configuration\DiscountManager;
use App\Service\Configuration\RefundHelpManager;
use App\Service\Email\EmailBuilder;
use App\Service\Email\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class ConfirmRegistrationHandler
{
    public function __construct(
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly EntityManagerInterface $entityManager,
        private readonly RefundHelpManager $refundHelpManager,
        private readonly DiscountManager $discountManager,
        private readonly TranslatorInterface $translator,
        private readonly EmailBuilder $emailBuilder,
        private readonly EmailSender $emailSender,
    ) {
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handle(ConfirmRegistrationCommand $command): void
    {
        $registration = $command->registration;

        if (null === $registration->getId() || null === $registration->getAdherent()->getEmail()) {
            throw new \LogicException('invalid registration');
        }

        $this->verifyEmailHelper->validateEmailConfirmation(
            $command->request->getUri(),
            (string) $registration->getId(),
            $registration->getAdherent()->getEmail()
        );

        $registration->setVerified(true);

        if (null === $command->registration->getAdherent()->getEmail()) {
            throw new \LogicException('invalid registration');
        }

        $this->entityManager->flush();

        $discountCode = $this->discountManager->getDiscountCode($registration);
        $refundHelpAmount = $this->refundHelpManager->getRefundHelpAmount($registration);

        /** @var float $amountToPay */
        $amountToPay = $registration->getPriceOption()?->getAmount();
        $amountToPay -= $refundHelpAmount;

        /** @var string $adherentEmail */
        $adherentEmail = $registration->getAdherent()->getEmail();

        $refundHelps = array_map(fn (RefundHelpEnum $refundHelp) => $refundHelp->trans($this->translator), $registration->getRefundHelps());

        $email = $this->emailBuilder
            ->useTemplate('email/registration_confirmed.html.twig', [
                'registration' => $registration,
                'refundHelps' => implode('+', $refundHelps),
                'discountCode' => $discountCode?->getCode(),
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
