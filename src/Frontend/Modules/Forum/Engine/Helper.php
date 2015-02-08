<?php

namespace Frontend\Modules\Forum\Engine;

/**
 * 
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */

use Symfony\Component\Filesystem\Filesystem;

use Frontend\Core\Engine\Theme as FrontendTheme;

class Helper {

    /**
     * Generate a correct path
     *
     * @return string
     */
    public static function getPathJS($file, $module)
    {

        // variables
        $fs = new Filesystem();
        $file = (string) $file;
        $module = (string) $module;
        $theme = FrontendTheme::getTheme();

        // get theme
        $themePath = '/src/Frontend/Themes/' . $theme . '/Core/Js';
        $filePath = $themePath . $file;

        // check for existence
        if($fs->exists((PATH_WWW . str_replace(PATH_WWW, '', $filePath)))) return $filePath;

        return '/src/Frontend/Modules/' . $module . '/Js' . $file;
    }

}