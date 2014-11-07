<?php

namespace App\MediaBundle\Lib;


class GlobalsMedia {

    protected static $uploadDir;
    protected static $mediaDir;

    public static function setUploadDir($dir)
    {
        self::$uploadDir = $dir;
    }

    public static function getUploadDir()
    {
        return self::$uploadDir;
    }

    public static function setMediaDir($dir)
    {
        self::$mediaDir = $dir;
    }

    public static function getMediaDir()
    {
        return self::$mediaDir;
    }

} 