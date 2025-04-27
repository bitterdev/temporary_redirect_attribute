<?php

namespace Concrete\Package\TemporaryRedirectAttribute;

use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Controller extends Package
{
    protected string $pkgHandle = 'temporary_redirect_attribute';
    protected string $pkgVersion = '0.0.3';
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
        /** @var EventDispatcherInterface $eventDispatcher */
        /** @noinspection PhpUnhandledExceptionInspection */
        $eventDispatcher = $this->app->make(EventDispatcherInterface::class);

        $eventDispatcher->addListener('on_start', function () {
            /** @var ResponseFactoryInterface $responseFactory */
            $responseFactory = $this->app->make(ResponseFactoryInterface::class);
            /** @var Navigation $navigationHelper */
            $navigationHelper = $this->app->make(Navigation::class);

            $page = Page::getCurrentPage();

            if ($page instanceof Page && !$page->isError()) {
                $targetPageId = (int)$page->getAttribute('page_selector_redirect');

                if ($targetPageId > 0) {
                    $targetPage = Page::getByID($targetPageId);

                    if ($targetPage instanceof Page && !$targetPage->isError()) {
                        if ($targetPage->isExternalLink()) {
                            $targetPageUrl = $targetPage->getCollectionPointerExternalLink();
                        } else {
                            $targetPageUrl = $navigationHelper->getLinkToCollection($targetPage);
                        }

                        $responseFactory->redirect($targetPageUrl, Response::HTTP_TEMPORARY_REDIRECT)->send();
                        $this->app->shutdown();
                    }
                }
            }
        });
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installContentFile("data.xml");
        return $pkg;
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("data.xml");
    }
}