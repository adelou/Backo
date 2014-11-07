<?php

namespace App\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\MediaBundle\Lib\GlobalsMedia;

class AppMediaBundle extends Bundle
{
    public function boot()
    {
        GlobalsMedia::setUploadDir($this->container->getParameter('app_media.upload_dir'));
        GlobalsMedia::setMediaDir($this->container->getParameter('app_media.media_dir'));
    }
}
