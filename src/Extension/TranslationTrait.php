<?php

namespace Bolt\Extension;

use Pimple as Container;

/**
 * Translation helpers.
 *
 * @author Niklas Goebel <git@niklas-goebel.de>
 */
trait TranslationTrait {

    /**
     * Call this in register method.
     *
     * @internal
     */
    final protected function extendTranslation() {
        $app = $this->getContainer();
        
//        $app['translator.resources'] = $app->extend(
//            'translator.resources',
//            function (array $resources, $app) {
//                $locale = $app['locale'];
//
//                $resources = array_merge($resources, $this->addTranslationResource($locale));
//
//                foreach ($app['locale_fallbacks'] as $fallback) {
//                    if ($locale !== $fallback) {
//                        $resources = array_merge($resources, $this->addTranslationResource($fallback));
//                    }
//                }
//
//                return $resources;
//            }
//        );
        
        $app['translator.paths'] = $app->extend(
            'translator.paths',
            function (array $paths) {
                $baseDir = $this->getBaseDirectory();
                $paths[] = $baseDir->getMountPoint() . '/'. $baseDir->getPath() . '/' . $this->registerTranslationPath();                 
                return $paths;
            }
        );
    }
    
    private function addTranslationResource($locale) {
        $path = '/locales/' . $locale;
        $transDir = $this->getBaseDirectory()->getDir($path);
        $resources = [];

        if (!$transDir->exists()) {
            return [];
        }
        
        foreach ($transDir->getContents() as $handler) {
            if (!$handler->isFile()) {
                continue;
            }

            $ext = $handler->getExtension();

            if (!in_array($ext, ['yml', 'xlf'], true)) {
                continue;
            }

            list($domain) = explode('.', $handler->getFilename());

            $ressource = $handler->getMountPoint() . '/' . $handler->getPath();
            $resources[] = [$ext, $ressource, $locale, $domain];
        }
        
        return $resources;
    }
    
    /**
     * Returns relative paths to translation ressources.
     *
     * @return string Path to translation files
     */
    protected function registerTranslationPath()
    {
        return 'locales';
    }

    /** @return Container */
    abstract protected function getContainer();

    /** @return DirectoryInterface */
    abstract protected function getBaseDirectory();

}
