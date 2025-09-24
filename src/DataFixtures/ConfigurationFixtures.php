<?php

namespace App\DataFixtures;

use App\DataFixtures\Factory\HtmlContentFactory;
use App\Entity\Configuration;
use App\Service\Configuration\AutomaticSendManager;
use App\Service\Configuration\RefundHelpManager;
use App\Service\Configuration\TextManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfigurationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new Configuration(
            AutomaticSendManager::AUTOMATIC_SEND,
            'INACTIVE',
        ));

        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_CITIZEN_ENABLE,
            'ENABLED',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_CITIZEN_LABEL,
            'Pass\'Sports Citoyen',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_CITIZEN_AMOUNT,
            '15',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_CITIZEN_HELP_TEXT,
            'Éligibilité et conditions d’utilisations sur le site de l\'Oise...',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_CITIZEN_FILE_LABEL,
            'Document d\'elligibilité au Pass\'Sports Citoyen',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_SPORT_ENABLE,
            'ENABLED',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_SPORT_LABEL,
            'Utiliser le Pass\'Sports',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_SPORT_AMOUNT,
            '70',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_SPORT_HELP_TEXT,
            'Éligibilité et conditions d’utilisations sur le site sports.gouv.fr',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_PASS_SPORT_FILE_LABEL,
            'Document d\'elligibilité au Pass\'Sports',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_CCAS_ENABLE,
            'DISABLED',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_CCAS_LABEL,
            'Aide du CCAS',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_CCAS_AMOUNT,
            '10',
        ));
        $manager->persist(new Configuration(
            RefundHelpManager::REFUND_HELP_CCAS_HELP_TEXT,
            '10 € pour les - de 18 ans résidant à Villers-sous-Saint-Leu',
        ));
        $manager->persist(new Configuration(
            TextManager::TEXT_HOME_PRESENTATION,
            HtmlContentFactory::create(2, 300),
        ));
        $manager->persist(new Configuration(
            TextManager::TEXT_CONTACT,
            '<p>Pour tout renseignement, merci de nous laisser un message à l\'aide du formulaire ci-dessous.</p>',
        ));

        $manager->flush();
    }
}
