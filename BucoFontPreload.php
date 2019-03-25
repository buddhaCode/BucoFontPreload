<?php

namespace BucoFontPreload;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class BucoFontPreload extends Plugin
{
    const CACHE_LIST = [
        InstallContext::CACHE_TAG_TEMPLATE,
        InstallContext::CACHE_TAG_HTTP,
        InstallContext::CACHE_TAG_CONFIG
    ];

    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(self::CACHE_LIST);
    }

    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(self::CACHE_LIST);
    }

    public function uninstall(UninstallContext $context)
    {
        $context->scheduleClearCache(self::CACHE_LIST);
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'preloadFonts',
        ];
    }

    public function preloadFonts(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $subject */
        $subject = $args->getSubject();

        $pluginDir = $this->container->getParameter($this->getContainerPrefix() . '.plugin_dir');
        $subject->View()->addTemplateDir($pluginDir . '/Resources/views/');

        // Add Shopware version information to template
        $shopwareRelease = $this->container->get('shopware.release');
        $subject->View()->assign('SHOPWARE_VERSION', $shopwareRelease->getVersion());
        $subject->View()->assign('SHOPWARE_VERSION_TEXT', $shopwareRelease->getVersionText());
        $subject->View()->assign('SHOPWARE_REVISION', $shopwareRelease->getRevision());

        $configReader = $this->container->get('shopware.plugin.cached_config_reader');
        $pluginName = $this->container->getParameter($this->getContainerPrefix() . '.plugin_name');
        $shop = $this->container->get('shop');
        $config = $configReader->getByPluginName($pluginName, $shop);

        $preloadFonts = [];

        // Add standard fonts
        $addRev = $this->doesFroshPerformanceRemoveFontRevision() ? '' : '?{$SHOPWARE_REVISION}';
        if(in_array('opensans', $config['standardFonts'])) {
            $preloadFonts[] = ['type' => 'font/woff2', 'url' => '{link file=\'frontend/_public/vendors/fonts/open-sans-fontface/Regular/OpenSans-Regular.woff2\'}' . $addRev];
            $preloadFonts[] = ['type' => 'font/woff', 'url' => '{link file=\'frontend/_public/vendors/fonts/open-sans-fontface/Regular/OpenSans-Regular.woff\'}' . $addRev];
            $preloadFonts[] = ['type' => 'font/woff2', 'url' => '{link file=\'frontend/_public/vendors/fonts/open-sans-fontface/Semibold/OpenSans-Semibold.woff2\'}' . $addRev];
            $preloadFonts[] = ['type' => 'font/woff', 'url' => '{link file=\'frontend/_public/vendors/fonts/open-sans-fontface/Semibold/OpenSans-Semibold.woff\'}' . $addRev];
            $preloadFonts[] = ['type' => 'font/woff2',  'url' => '{link file=\'frontend/_public/vendors/fonts/open-sans-fontface/Bold/OpenSans-Bold.woff2\'}' . $addRev];
            $preloadFonts[] = ['type' => 'font/woff', 'url' => '{link file=\'frontend/_public/vendors/fonts/open-sans-fontface/Bold/OpenSans-Bold.woff\'}' . $addRev];
        }

        if(in_array('shopware', $config['standardFonts'])) {
            $preloadFonts[] = ['type' => 'font/woff', 'url' => '{link file=\'frontend/_public/src/fonts/shopware.woff\'}' . $addRev];
        }

        // Custom fonts
        $customFonts = explode(PHP_EOL, $config['customFonts']);
        $customFonts = array_filter($customFonts);
        foreach($customFonts as $customFont) {
            $mimeType = $this->guessMineType($customFont);

            if(!$mimeType)
                continue;

            $preloadFonts[] = [
                'type' => $mimeType,
                'url' => $customFont
            ];
        }

        $subject->View()->assign('bucoFontPreload', $preloadFonts);
    }

    private function guessMineType(string $customFont) : string
    {
        if(strrpos($customFont, '.woff2') !== false) {
            return 'font/woff2';
        }
        elseif(strrpos($customFont, '.woff') !== false) {
            return 'font/woff';
        }
        elseif(strrpos($customFont, '.ttf') !== false) {
            return 'font/ttf';
        }
        elseif(strrpos($customFont, '.eot') !== false) {
            return 'application/vnd.ms-fontobject';
        }
        elseif(strrpos($customFont, '.svg') !== false) {
            return 'image/svg+xml';
        }

        return '';
    }

    private function doesFroshPerformanceRemoveFontRevision() : bool
    {
        $pluginService = $this->container->get('shopware.plugin_manager');
        try {
            $froshPerformancePlugin = $pluginService->getPluginByName('FroshPerformance');
            $config = $pluginService->getPluginConfig($froshPerformancePlugin);
            return (bool) $config['removeShopwareRevisionFromFont'];
        } catch (\Exception $e) {}
        
        return false;
    }
}