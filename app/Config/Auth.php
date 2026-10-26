<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Authentication\Passwords\CompositionValidator;
use CodeIgniter\Shield\Config\Auth as ShieldAuth;

class Auth extends ShieldAuth
{
    public bool $allowRegistration = false;

    /**
     * Staff password creation only requires a minimum length.
     *
     * @var list<class-string<\CodeIgniter\Shield\Authentication\Passwords\ValidatorInterface>>
     */
    public array $passwordValidators = [CompositionValidator::class];
    public int $minimumPasswordLength = 8;

    /**
     * Keep users signed in with a persistent, database-backed token.
     */
    public array $sessionConfig = [
        'field'              => 'user',
        'allowRemembering'   => true,
        'rememberCookieName' => 'remember',
        'rememberLength'     => 30 * DAY,
    ];

    public array $views = [
        'login' => 'auth/login',
        'register' => '\\CodeIgniter\\Shield\\Views\\register',
        'layout' => '\\CodeIgniter\\Shield\\Views\\layout',
        'action_email_2fa' => '\\CodeIgniter\\Shield\\Views\\email_2fa_show',
        'action_email_2fa_verify' => '\\CodeIgniter\\Shield\\Views\\email_2fa_verify',
        'action_email_2fa_email' => '\\CodeIgniter\\Shield\\Views\\Email\\email_2fa_email',
        'action_email_activate_show' => '\\CodeIgniter\\Shield\\Views\\email_activate_show',
        'action_email_activate_email' => '\\CodeIgniter\\Shield\\Views\\Email\\email_activate_email',
        'magic-link-login' => '\\CodeIgniter\\Shield\\Views\\magic_link_form',
        'magic-link-message' => '\\CodeIgniter\\Shield\\Views\\magic_link_message',
        'magic-link-email' => '\\CodeIgniter\\Shield\\Views\\Email\\magic_link_email',
    ];

    /**
     * Password resets are handled through the ICT verification queue.
     * Keep Shield's email magic-link UI disabled until SMTP is configured.
     */
    public bool $allowMagicLinkLogins = false;

    public array $redirects = [
        'register' => '/', 'login' => 'staff', 'logout' => 'login',
        'force_reset' => 'staff', 'permission_denied' => 'staff', 'group_denied' => 'staff',
    ];
}
