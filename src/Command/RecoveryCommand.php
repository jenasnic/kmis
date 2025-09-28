<?php

namespace App\Command;

use App\Entity\Adherent;
use App\Entity\Content\News;
use App\Entity\Content\Sporting;
use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[AsCommand('app:recovery-data', 'Recovery for new KMIS version with CMS...')]
class RecoveryCommand extends Command
{
    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly EntityManagerInterface $entityManager,
        private readonly Filesystem $filesystem,
        private readonly string $uploadPath,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->updateSchema($io);
        $this->updateData($io);
        $this->fixFilePath($io);
        $this->processFiles($io);

        $this->fixMigration($io);

        $io->success('Recovery done!');

        return Command::SUCCESS;
    }

    private function updateSchema(SymfonyStyle $io): void
    {
        $io->info('Update database schema');

        $sql = <<<SQL
                CREATE TABLE discount_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(25) NOT NULL, refund_helps JSON NOT NULL COMMENT '(DC2Type:json)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
                CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, sporting_id INT DEFAULT NULL, calendar_id INT NOT NULL, start VARCHAR(15) NOT NULL, end VARCHAR(15) NOT NULL, detail VARCHAR(255) DEFAULT NULL, INDEX IDX_5A3811FBB98E6800 (sporting_id), INDEX IDX_5A3811FBA40A2C8 (calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
                CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, latitude NUMERIC(10, 0) DEFAULT NULL, longitude NUMERIC(10, 0) DEFAULT NULL, localization VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, rank INT NOT NULL, address_street VARCHAR(255) DEFAULT NULL, address_street2 VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(25) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
                CREATE TABLE sporting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(55) NOT NULL, tagline VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, rank INT NOT NULL, picture_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
                CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, day INT NOT NULL, INDEX IDX_6EA9A14664D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
                ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBB98E6800 FOREIGN KEY (sporting_id) REFERENCES sporting (id) ON DELETE CASCADE;
                ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON DELETE CASCADE;
                ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A14664D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE;
                ALTER TABLE adherent ADD address_street2 VARCHAR(255) DEFAULT NULL;
                ALTER TABLE season ADD payment_link VARCHAR(255) DEFAULT NULL, ADD licence_link VARCHAR(255) DEFAULT NULL, ADD pricing_note LONGTEXT DEFAULT NULL;
                ALTER TABLE configuration CHANGE value value LONGTEXT DEFAULT NULL;
                ALTER TABLE payment_pass DROP FOREIGN KEY FK_EFF34350BF396750;
                RENAME TABLE payment_pass TO payment_refund_help;
                ALTER TABLE payment_refund_help RENAME COLUMN number TO reference;
                ALTER TABLE payment_refund_help CHANGE reference reference VARCHAR(255) DEFAULT NULL;
                ALTER TABLE payment_refund_help ADD refund_help VARCHAR(55) NOT NULL;
                ALTER TABLE payment_refund_help ADD CONSTRAINT FK_1C2E83B4BF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE;
                ALTER TABLE payment_ancv CHANGE number number VARCHAR(255) NOT NULL;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Update refund help payment');

        $sql = <<<SQL
                UPDATE payment SET type='refund_help' WHERE type='pass';
                UPDATE payment_refund_help AS _refund_help
                INNER JOIN payment AS _payment ON _payment.id = _refund_help.id
                SET _refund_help.refund_help = 'PASS_CITIZEN'
                WHERE _payment.amount in (15, 20);
                UPDATE payment_refund_help AS _refund_help
                INNER JOIN payment AS _payment ON _payment.id = _refund_help.id
                SET _refund_help.refund_help = 'PASS_SPORT'
                WHERE _payment.amount in (50, 70);
                UPDATE payment_refund_help AS _refund_help
                INNER JOIN payment AS _payment ON _payment.id = _refund_help.id
                SET _refund_help.refund_help = 'CCAS'
                WHERE _payment.amount in (10);
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();
    }

    private function updateData(SymfonyStyle $io): void
    {
        $io->info('Create sporting data');

        $sql = <<<SQL
                INSERT INTO sporting (id, name, active, rank, tagline, content) VALUES
                (1, 'Krav Maga Adultes', 1, 1, 'Pour augmenter sa confiance en soi et parfaire son bagage technique.', '<p>Le Krav Maga Adultes n’est pas un sport de combat comme les autres : c’est une méthode de défense réaliste, rapide à maîtriser et conçue pour fonctionner quand ça compte vraiment – dans la vraie vie.</p>
                <p>Ici, pas de chorégraphies. Pas de fioritures. Juste des gestes simples, puissants et instinctifs pour se protéger et protéger ceux qu’on aime.</p>
                <p><b>Pourquoi le Krav Maga ?</b></p>
                <ul>
                <li>Parce que chaque seconde compte dans une situation critique.</li>
                <li>Parce que vous voulez des réflexes fiables et une confiance totale en vous.</li>
                <li>Parce qu’il est temps de développer votre force physique et mentale en même temps.</li>
                </ul>
                <p><b>Au programme</b></p>
                <ul>
                <li>Techniques de défense : frappes variées, saisies, menaces, armes, sol… tout est couvert.</li>
                <li>Réactivité immédiate : savoir analyser, agir et finir la situation vite.</li>
                <li>Condition physique et mental d’acier : endurance, sang-froid, détermination.</li>
                </ul>'),
                (2, 'KMix-MMA', 1, 2, 'Pour augmenter ses aptitudes de défense.', '<p>Le but ici n’est pas uniquement de former des combattants à entrer dans une cage, mais de préparer et donner des armes complémentaires pour faire face à une agression, notamment lorsque les premières actions ne donnent pas l’effet escompté.</p>
                <p>Il faut alors pouvoir passer en mode combat : disposer d’une boxe pieds-poings efficace pour décourager ou mettre KO rapidement, d’une lutte performante pour amener au sol un individu plus fort en boxe, ou éviter d’être soi-même projeté dans une situation délicate, et enfin d’un grappling complet pour contrôler ou mettre un terme au problème une fois au sol.</p>
                <p>Cette approche, en plus d’être utile en situation réelle, constitue également une excellente base pour ceux qui souhaitent s’engager dans la compétition. Le panel des techniques est extrêmement riche, varié et complémentaire aux disciplines de défense.</p>
                <p>Entraînez-vous comme vous voulez vous défendre... ou combattre.</p>'),
                (3, 'Krav Maga Ados', 1, 3, 'Pour améliorer sa condition physique et mentale.', '
                <p>La self defense s’adresse aussi aux adolescents. Au collège, au lycée ou à l’extérieur, les agressions peuvent être rencontrées, parfois même à l''origine de camarades. Réagir face aux différents degrés d’agressions en adaptant son comportement et ses gestes est nécessaire.</p>
                <p>Adaptée aux problématiques des plus jeunes, la discipline accompagnera leur évolution. Travail de la condition physique et mentale, mise en confiance, amélioration des réflexes et réactions, les ingrédients sont réunis pour apprendre à se défendre.</p>
                <p>On s''adresse à toi : Si tu es adolescent et que cette discipline t’intrigue ou te motive, tu sais à quelle porte frapper : rejoins-nous. Le KMIS est à tes côtés pour te faire progresser.</p>
                <p>Entraînez-vous comme vous voulez vous défendre.</p>'),
                (4, 'Cours élite', 1, 4, 'Pour performer dans le combat et/ou la compétition.', '
                <p>Ce cours s’adresse aux pratiquants expérimentés qui souhaitent franchir un cap dans leur pratique du MMA. L’objectif est clair : approfondir les connaissances techniques, développer des stratégies de combat avancées, et préparer efficacement les compétitions.</p>
                <p><b>Au programme</b></p>
                <ul>
                <li>Travail technique ciblé en boxe, lutte et grappling, avec un focus sur les enchaînements spécifiques au MMA.</li>
                <li>Études stratégiques : analyse de styles, adaptation en fonction des adversaires, gestion du rythme et du temps de combat.</li>
                <li>Sparring intensif et encadré, avec des mises en situation réalistes pour tester les acquis et affiner les réflexes.</li>
                <li>Préparation physique et mentale, indispensable pour performer en compétition.</li>
                </ul>
                <p>Ce cours est pensé comme un véritable laboratoire du combattant, où chaque séance vise à renforcer les points forts, corriger les faiblesses et construire une approche cohérente du combat.</p>
                <p>Rejoignez le cours élite et entrez dans une dynamique de performance.</p>'),
                (5, 'Cardio Fit', 1, 5, 'Pour se doter de la condition physique souhaitée.', '
                <p>Un cours dédié à l’amélioration de la condition physique et au dépassement de soi qui s’adapte à tous pour réaliser la répétition de plus, la seconde d’exercice de plus, celle qui peut vous sauver. Dans la rue, la condition physique et la volonté de ne pas lâcher  sont primordiales : pour courir plus vite, frapper plus fort, combattre ou contrôler plus longtemps, elle est une véritable alliée. Renforcement musculaire, cardiovasculaire et mental, la formule est complète.</p>
                <p>Sous forme d’ateliers ou de circuits, cette activité promet sueur, dépassement et bonne humeur sur une playlist musicale conçue pour vous motiver.</p>
                <p>Entraînez-vous comme vous voulez vous défendre.</p>')
                ;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Create location data');

        $sql = <<<SQL
                INSERT INTO location (id, name, active, rank, address_street, address_street2, address_zip_code, address_city, latitude, longitude, localization) VALUES
                (1, 'Liancourt', 1, 1, 'Gymnase - Collège La Rochefoucauld', 'Rue du général De Gaulle', 60140, 'Liancourt', 49.3262507, 2.4592501, 'https://www.google.com/maps/place/Coll%C3%A8ge+La+Rochefoucauld/@49.3262507,2.4592501,16z/data=!3m1!4b1!4m6!3m5!1s0x47e64ac4d94d7355:0xb2ec60327646476a!8m2!3d49.3262473!4d2.464121!16s%2Fg%2F1ttdm910'),
                (2, 'Précy-sur-Oise', 1, 2, 'Dojo', '34 sente Sorel', 60460, 'Précy-sur-Oise', 49.212344, 2.3721153, 'https://www.google.com/maps/place/34+Sente+Sorel,+60460+Pr%C3%A9cy-sur-Oise/@49.212344,2.3721153,17z/data=!3m1!4b1!4m6!3m5!1s0x47e64f23b2c84ab9:0x601a1dd1c86a36f4!8m2!3d49.2123406!4d2.3769862!16s%2Fg%2F11c4nbmb5q'),
                (3, 'Villers-Sous-Saint-Leu', 1, 3, '23 rue du Castel', null, 60340, 'Villers-Sous-Saint-Leu', 49.2142549, 2.396175, 'https://www.google.com/maps/place/23+Rue+du+Castel,+60340+Villers-Sous-Saint-Leu/@49.2142549,2.396175,17z/data=!3m1!4b1!4m5!3m4!1s0x47e648d4a31f8c93:0x7abe632340c2a373!8m2!3d49.2142549!4d2.3983637')
                ;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Create calendar/schedule data');

        $sql = <<<SQL
                INSERT INTO calendar (id, location_id, day) VALUES
                (1, 1, 0),
                (2, 1, 2),
                (3, 2, 3),
                (4, 3, 4),
                (5, 3, 5)
                ;
                INSERT INTO schedule (calendar_id, sporting_id, start, end, detail) VALUES
                (1, 3, '19h00', '20h00', null),
                (1, 2, '19h00', '21h00', null),
                (1, 1, '20h00', '21h30', null),
                (2, 5, '19h00', '20h00', null),
                (2, 4, '20h00', '22h00', null),
                (3, 3, '19h00', '20h00', null),
                (3, 1, '20h00', '21h30', null),
                (4, 2, '19h30', '21h30', null),
                (5, null, '14h00', '17h00', '<p>Stages thématiques <i>(informations pratiques communiquées par mail)</i></p>')
                ;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Create discount code data');

        $sql = <<<SQL
                INSERT INTO discount_code (code, refund_helps) VALUES
                ('10', '["CCAS"]'),
                ('15', '["PASS_CITIZEN"]'),
                ('70', '["PASS_SPORT"]'),
                ('15+10', '["PASS_CITIZEN","CCAS"]'),
                ('70+15', '["PASS_CITIZEN","PASS_SPORT"]'),
                ('70', '["PASS_SPORT","CCAS"]'),
                ('70+15+10', '["PASS_CITIZEN","PASS_SPORT","CCAS"]')
                ;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Create configuration data');

        $sql = <<<SQL
                INSERT INTO configuration (code, value) VALUE
                ('REFUND_HELP_CCAS_AMOUNT', '10'),
                ('REFUND_HELP_CCAS_ENABLE', 'ENABLED'),
                ('REFUND_HELP_CCAS_HELP_TEXT', '<p>10 € pour les - de 18 ans résidant à Villers-sous-Saint-Leu</p>'),
                ('REFUND_HELP_PASS_CITIZEN_AMOUNT', '15'),
                ('REFUND_HELP_PASS_CITIZEN_ENABLE', 'ENABLED'),
                ('REFUND_HELP_PASS_CITIZEN_HELP_TEXT', '<p>Éligibilité et conditions d’utilisations <a href="https://www.oise.fr/les-pass-citoyens-du-conseil-departemental-de-loise/le-passsports-citoyen" target="_blank">ici</a> (sélectionner discipline "Karaté" et commune du club "Mogneville").</p>'),
                ('REFUND_HELP_PASS_SPORT_AMOUNT', '70'),
                ('REFUND_HELP_PASS_SPORT_ENABLE', 'ENABLED'),
                ('REFUND_HELP_PASS_SPORT_HELP_TEXT', '<p>Éligibilité et conditions d’utilisations <a href="https://www.sports.gouv.fr/pratiques-sportives/sports-pour-tous/pass-sport/" target="_blank">ici</a>.</p>'),
                ('TEXT_CONTACT', '<p>Pour tout renseignement, vous pouvez nous contacter au <a href="tel:0783237428" itemprop="telephone">07.83.23.74.28</a> ou nous laisser un message à l''aide du formulaire ci-dessous.</p>'),
                ('TEXT_HOME_PRESENTATION', '<p>Le <span itemprop="name">KMIS</span> offre tout un panel de disciplines complémentaires toutes axées autour de la défense. Que ce soit l''amélioration de la condition physique qui permettra par exemple une meilleure gestion des effets du stress, de l''apprentissage de techniques de combat ou de défense ou bien plus spécifiquement LA Self Defense, vous trouverez la ou les disciplines qui vous permettront de vous épanouir avec une équipe à votre écoute et mobilisée pour la réussite de chacun.</p>
                <p>Chacun trouvera une ou plusieurs activités lui permettant de sortir de sa zone de confort et progresser efficacement.</p>')
                ;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Update active saison with new data');

        $sql = <<<SQL
                UPDATE season SET
                payment_link = 'https://www.helloasso.com/associations/krav-maga-impact-system/adhesions/adhesion-kmis-2025-2026',
                licence_link = 'https://www.ffkarate.fr/wp-content/uploads/2025/07/DEMANDE_LICENCE_INTERNET_2025-2026_MD.pdf',
                pricing_note = '<p>Ados : 10-14/15 ans / Adultes : à partir de 15/16 ans<br/>(Licence FFKDA 39 € incluse)</p>'
                WHERE id = 4;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();
    }

    private function fixFilePath(SymfonyStyle $io): void
    {
        $io->info('Fix path for adherent picture');

        $sql = <<<SQL
                UPDATE adherent SET picture_url = SUBSTRING_INDEX(picture_url, '/', -1) WHERE 1;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Fix path for news picture');

        $sql = <<<SQL
                UPDATE news SET picture_url = SUBSTRING_INDEX(picture_url, '/', -1) WHERE 1;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();

        $io->info('Fix path for registration documents');

        $sql = <<<SQL
                UPDATE registration
                SET medical_certificate_url = SUBSTRING_INDEX(medical_certificate_url, '/', -1),
                licence_form_url = SUBSTRING_INDEX(licence_form_url, '/', -1),
                pass_citizen_url = SUBSTRING_INDEX(pass_citizen_url, '/', -1),
                pass_sport_url = SUBSTRING_INDEX(pass_sport_url, '/', -1)
                WHERE 1;
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();
    }

    private function processFiles(SymfonyStyle $io): void
    {
        $io->info('Process sporting files');

        $this->copySportingFile(1, __DIR__.'/data/krav_maga_adulte.webp');
        $this->copySportingFile(2, __DIR__.'/data/kmix_mma.webp');
        $this->copySportingFile(3, __DIR__.'/data/krav_maga_ado.webp');
        $this->copySportingFile(4, __DIR__.'/data/cours_elite.webp');
        $this->copySportingFile(5, __DIR__.'/data/cardio_fit.webp');

        $io->info('Process adherent files');

        $adherents = $this->entityManager->getRepository(Adherent::class)->findAll();
        foreach ($adherents as $adherent) {
            $this->recoverEntityFile($io, $adherent, 'pictureUrl', Adherent::PICTURE_FOLDER);
        }

        $io->info('Process registration files');

        $registrations = $this->entityManager->getRepository(Registration::class)->findAll();
        foreach ($registrations as $registration) {
            $this->recoverEntityFile($io, $registration, 'medicalCertificateUrl', Registration::DOCUMENT_FOLDER);
            $this->recoverEntityFile($io, $registration, 'licenceFormUrl', Registration::DOCUMENT_FOLDER);
            $this->recoverEntityFile($io, $registration, 'passCitizenUrl', Registration::DOCUMENT_FOLDER);
            $this->recoverEntityFile($io, $registration, 'passSportUrl', Registration::DOCUMENT_FOLDER);
        }

        $io->info('Process news files');

        $newsList = $this->entityManager->getRepository(News::class)->findAll();
        foreach ($newsList as $news) {
            $this->recoverEntityFile($io, $news, 'pictureUrl', News::PICTURE_FOLDER);
        }
    }

    private function fixMigration(SymfonyStyle $io): void
    {
        $io->info('Fix migration');

        $sql = <<<SQL
                TRUNCATE TABLE doctrine_migration_versions;
                INSERT INTO doctrine_migration_versions (version, executed_at, execution_time)
                VALUES ('DoctrineMigrations\\Version20251007000000', '2025-10-07 00:00:00', 666);
            SQL;

        $this->entityManager->createNativeQuery($sql, new ResultSetMapping())->execute();
    }

    private function copySportingFile(int $id, string $sourcePath): void
    {
        /** @var Sporting $sporting */
        $sporting = $this->entityManager->getReference(Sporting::class, $id);

        $pathInfo = pathinfo($sourcePath);

        $fileName = sprintf(
            '%s.%s',
            str_replace('.', '', uniqid('', true)),
            $pathInfo['extension'] ?? '',
        );

        $this->filesystem->copy($sourcePath, $this->uploadPath.Sporting::PICTURE_FOLDER.DIRECTORY_SEPARATOR.$fileName);

        $sporting->setPictureUrl($fileName);
        $this->entityManager->flush();
    }

    /**
     * @param Adherent|News|Registration $entity
     */
    private function recoverEntityFile(SymfonyStyle $io, object $entity, string $property, string $folderName): void
    {
        /** @var string|null $fileName */
        $fileName = $this->propertyAccessor->getValue($entity, $property);

        if (empty($fileName)) {
            return;
        }

        $sourcePath = $this->uploadPath.$fileName;
        if (!file_exists($sourcePath)) {
            $io->warning(sprintf('File %s does not exist for registration %d', $fileName, $entity->getId()));

            $this->propertyAccessor->setValue($entity, $property, null);

            $this->entityManager->flush();

            return;
        }

        $targetPath = $this->uploadPath.$folderName.DIRECTORY_SEPARATOR.$fileName;

        $this->filesystem->rename($sourcePath, $targetPath);
    }
}
