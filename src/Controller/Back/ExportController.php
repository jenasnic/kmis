<?php

namespace App\Controller\Back;

use App\Entity\Season;
use App\Service\Export\AdherentXlsxExport;
use App\Service\Export\PaymentXlsxExport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExportController extends AbstractController
{
    #[Route('/exporter-les-inscrits', name: 'bo_export_adherent', methods: ['GET'])]
    public function exportAdherent(AdherentXlsxExport $adherentXlsxExport): Response
    {
        return $adherentXlsxExport->export();
    }

    #[Route('/exporter-les-paiements/{season}', name: 'bo_export_payment', methods: ['GET'])]
    public function exportPayment(PaymentXlsxExport $paymentXlsxExport, Season $season): Response
    {
        return $paymentXlsxExport->export($season);
    }
}
