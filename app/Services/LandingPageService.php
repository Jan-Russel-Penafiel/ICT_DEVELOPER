<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RegistrationLinkModel;

final class LandingPageService
{
    public function __construct(private readonly RegistrationLinkModel $links = new RegistrationLinkModel()) {}
    public function registrationLinks(): array { return $this->links->activeForLandingPage(); }
}
