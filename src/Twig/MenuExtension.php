<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MenuExtension extends AbstractExtension
{
    private const MENU_TREE = [
        'bo_dashboard' => [
            'bo_dashboard',
        ],
        'bo_season_list' => [
            'bo_season_list',
            'bo_season_new',
            'bo_season_edit',
        ],
        'bo_adherent_list' => [
            'bo_adherent_list',
            'bo_adherent_full_list',
            'bo_adherent_edit',
            'bo_registration_new',
            'bo_registration_edit',
            'bo_payment_list_for_adherent',
            'bo_payment_new',
            'bo_payment_edit',
        ],
        'bo_adherent_gallery' => [
            'bo_adherent_gallery',
        ],
        'bo_payment_list_for_season' => [
            'bo_payment_list_for_season',
            'bo_payment_edit_for_season',
            'bo_payment_view_for_season',
        ],
        'bo_news_list' => [
            'bo_news_list',
            'bo_news_new',
            'bo_news_edit',
        ],
        'bo_text' => [
            'bo_text',
        ],
        'bo_sporting_list' => [
            'bo_sporting_list',
            'bo_sporting_new',
            'bo_sporting_edit',
        ],
        'bo_location_list' => [
            'bo_location_list',
            'bo_location_new',
            'bo_location_edit',
        ],
        'bo_calendar_list' => [
            'bo_calendar_list',
            'bo_calendar_new',
            'bo_calendar_edit',
        ],
        'bo_refund_help' => [
            'bo_refund_help',
        ],
        'bo_discount_code' => [
            'bo_discount_code',
        ],
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('matchCurrentRoute', [$this, 'matchCurrentRoute']),
        ];
    }

    public function matchCurrentRoute(string $route): bool
    {
        if (!array_key_exists($route, self::MENU_TREE)) {
            return false;
        }

        $mainRequest = $this->requestStack->getMainRequest();
        if (null === $mainRequest) {
            return false;
        }

        $currentRoute = $mainRequest->attributes->get('_route');

        return in_array($currentRoute, self::MENU_TREE[$route]);
    }
}
