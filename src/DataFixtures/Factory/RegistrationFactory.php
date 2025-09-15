<?php

namespace App\DataFixtures\Factory;

use App\Entity\Payment\PriceOption;
use App\Entity\Registration;
use App\Enum\RegistrationTypeEnum;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Registration>
 */
final class RegistrationFactory extends PersistentProxyObjectFactory
{
    private string $fileModel;

    private Generator $faker;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $uploadPath,
    ) {
        parent::__construct();

        $this->fileModel = __DIR__.'/data/test.pdf';

        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        $medicalCertificateFileName = str_replace('.', '', uniqid('', true)).'.pdf';
        $medicalCertificatePath = $this->uploadPath.Registration::DOCUMENT_FOLDER.DIRECTORY_SEPARATOR.$medicalCertificateFileName;
        $this->filesystem->copy($this->fileModel, $medicalCertificatePath);

        $licenceFormFileName = str_replace('.', '', uniqid('', true)).'.pdf';
        $licenceFormPath = $this->uploadPath.Registration::DOCUMENT_FOLDER.DIRECTORY_SEPARATOR.$licenceFormFileName;
        $this->filesystem->copy($this->fileModel, $licenceFormPath);

        $registeredAt = $this->faker->dateTimeBetween('-2 months', '-1 week');

        $withLegalRepresentative = $this->faker->boolean(30);
        $adherentAttributes = [];
        if ($withLegalRepresentative) {
            $adherentAttributes['birthDate'] = $this->faker->dateTimeBetween('-18 years', '-11 years');
        }

        $usePassCitizen = $this->faker->boolean(30);
        $usePassSport = $this->faker->boolean(30);

        if ($usePassCitizen) {
            $passCitizenFileName = str_replace('.', '', uniqid('', true)).'.pdf';
            $passCitizenPath = $this->uploadPath.Registration::DOCUMENT_FOLDER.DIRECTORY_SEPARATOR.$passCitizenFileName;
            $this->filesystem->copy($this->fileModel, $passCitizenPath);
        }
        if ($usePassSport) {
            $passSportFileName = str_replace('.', '', uniqid('', true)).'.pdf';
            $passSportPath = $this->uploadPath.Registration::DOCUMENT_FOLDER.DIRECTORY_SEPARATOR.$passSportFileName;
            $this->filesystem->copy($this->fileModel, $passSportPath);
        }

        return [
            'adherent' => AdherentFactory::new($adherentAttributes),
            'comment' => $this->faker->text(),
            'copyrightAuthorization' => $this->faker->boolean(80),
            'emergency' => EmergencyFactory::new(),
            'legalRepresentative' => $withLegalRepresentative ? LegalRepresentativeFactory::new() : null,
            'licenceDate' => $registeredAt,
            'licenceFormUrl' => $licenceFormFileName,
            'licenceNumber' => $this->faker->numberBetween(100000, 999999),
            'medicalCertificateUrl' => $medicalCertificateFileName,
            'passCitizenUrl' => $usePassCitizen ? $passCitizenFileName : null,
            'passSportUrl' => $usePassSport ? $passSportFileName : null,
            'privateNote' => $this->faker->text(),
            'purpose' => PurposeFactory::random()->_real(),
            'registeredAt' => $registeredAt,
            'registrationType' => $this->faker->randomElement(RegistrationTypeEnum::cases()),
            'usePassCitizen' => $usePassCitizen,
            'usePassSport' => $usePassSport,
            'verified' => $this->faker->boolean(80),
            'withLegalRepresentative' => $withLegalRepresentative,
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Registration $registration, array $attributes) {
            if (!array_key_exists('priceOption', $attributes)) {
                /** @var PriceOption $priceOption */
                $priceOption = $this->faker->randomElement($registration->getSeason()->getPriceOptions()->toArray());
                $registration->setPriceOption($priceOption);
            }
        });
    }

    public static function class(): string
    {
        return Registration::class;
    }
}
