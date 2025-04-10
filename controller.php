<?php

namespace Concrete\Package\TemporaryRedirectAttribute;

use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected string $pkgHandle = 'temporary_redirect_attribute';
    protected string $pkgVersion = '0.0.1';
    protected $appVersionRequired = '9.0.0';

    public function getPackageDescription(): string
    {
        return t("An attribute type that enables temporary redirects to another page within the sitemap, offering a flexible and cache-friendly solution.");
    }

    public function getPackageName(): string
    {
        return t('Temporary Redirect Attribute');
    }

    public function on_start()
    {
        // @todo: implement me
    }
}