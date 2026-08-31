<?php

namespace App\Command;

use App\Repository\AdherentRepository;
use App\Repository\SeasonRepository;
use App\Service\Configuration\AutomaticSendManager;
use App\Service\Notifier\ReEnrollmentNotifier;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:re-enrollment')]
final class ReEnrollmentCommand extends Command
{
    public function __construct(
        private readonly AutomaticSendManager $automaticSendManager,
        private readonly AdherentRepository $adherentRepository,
        private readonly SeasonRepository $seasonRepository,
        private readonly ReEnrollmentNotifier $reEnrollmentNotifier,
        private readonly int $mailerMaxPacketSize,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send re-enrollment email to adherent (use max packet size define in .env to avoid "Mails peer session limit").')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Limit on sent emails to avoid "Mails peer session limit" error (override MAILER_MAX_PACKET_SIZE from .env).')
            ->addArgument('adherent', InputArgument::OPTIONAL, 'Specific adherent email we want to sent re-enrollment email).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $adherent = null;

        if ($input->hasArgument('adherent')) {
            /** @var string|null $adherentEmail */
            $adherentEmail = $input->getArgument('adherent');

            if (!is_string($adherentEmail)) {
                $io->success('Invalid adherent email.');

                return self::FAILURE;
            }

            $adherent = $this->adherentRepository->findOneBy([
                'email' => $adherentEmail,
                'reEnrollmentToNotify' => true,
            ]);

            if (null === $adherent) {
                $io->success('Unknown adherent.');

                return self::FAILURE;
            }
        }

        if (null !== $adherent) {
            $result = $this->reEnrollmentNotifier->notifyAdherent($adherent);

            return $result ? self::SUCCESS : self::FAILURE;
        }

        if (!$this->automaticSendManager->isAutomaticSendEnable()) {
            $io->warning('Automatic enrollment notification is disabled.');

            return self::INVALID;
        }

        /** @var int $limit */
        $limit = $input->getArgument('limit') ?? $this->mailerMaxPacketSize;

        try {
            $season = $this->seasonRepository->getActiveSeason();

            if (null === $season) {
                $io->error('No active season found');

                return self::FAILURE;
            }

            $count = $this->reEnrollmentNotifier->notifyPacket($limit);

            $io->success(sprintf('%d re-enrollment emails sent!', $count));
        } catch (\Exception $e) {
            $io->error(sprintf('An error occurs : %s', $e->getMessage()));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
