<?php

namespace App\Controllers;

use App\Services\LandingPageService;
use App\Services\PublicPortalService;

class Home extends BaseController
{
    public function __construct(
        private readonly LandingPageService $landing = new LandingPageService(),
        private readonly PublicPortalService $portal = new PublicPortalService(),
    ) {}
    public function index(): string
    {
        return view('public/home', [
            'title' => 'SKSU ICT Service Portal',
            'isLandingPage' => true,
        ]);
    }

    public function id(): string
    {
        $ref = trim((string) $this->request->getGet('ref'));

        return view('public/id', [
            'title' => 'Submit Request | SKSU ICT Service Portal',
            'registrationLinks' => $this->landing->registrationLinks(),
            'ref' => $ref,
            'ticket' => $ref === '' ? null : $this->portal->trackingData($ref)['ticket'],
            'activeRequest' => (string) $this->request->getGet('request'),
        ]);
    }
}
