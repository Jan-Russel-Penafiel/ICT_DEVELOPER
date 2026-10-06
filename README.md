# ICT Service Portal

CI4 workflow portal for campus ID issuance, lost-ID payments, institutional-account CSV preparation, password-reset verification, and hardware repair tracking.

## First deployment

1. Set `app.baseURL` and the MySQL connection in `.env`. Point Apache's document root to `public/`.
2. Install dependencies with `composer install --no-dev --optimize-autoloader`.
3. Run `php spark migrate --all` and `php spark db:seed IctFoundationSeeder`.
4. Create the first staff user without putting a password in source control:
   `php spark shield:user create -n admin -e admin@your-school.edu -g admin`
5. Sign in as an administrator and use **Staff workspace -> Staff accounts** to create additional users in the `admin`, `ict`, `registrar`, or `cashier` group. Registrar and Cashier accounts require a campus assignment.

Public routes are `/register/{token}`, `/track`, `/reset-request`, and `/repair-request`. Staff routes begin at `/staff`. Institutional accounts are imported as a student-number CSV or XLSX roster for one selected campus and section; every listed student must already be registered in that same campus and section.

Photos are deliberately saved beneath `writable/private/`, never under `public/`. Configure SMTP before enabling any email notices. The repair form uses a print stylesheet instead of Dompdf because the currently available Dompdf release is blocked by Composer security advisories.

## Test notes

Run `composer test`. The included CI4 example database tests require PHP's `sqlite3` extension; production uses MySQL. Confirm `gd`, `mbstring`, `zip`, `xml`, and `intl` are enabled for uploads, QR codes, and spreadsheet files.

# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
