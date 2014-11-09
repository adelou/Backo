<?php
namespace App\MediaBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\MediaBundle\Lib\GlobalsMedia;
use Symfony\Component\Filesystem\Exception\IOException;

class MediaService
{
    /*
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    public function getCrop($parameters) {

        $parameters['src'] = GlobalsMedia::getUploadDir() . $parameters['path'];
        $parameters['destcrop'] = GlobalsMedia::getUploadDir() . $parameters['slug'] .'/'.$parameters['path'];

        $filesystem = new Filesystem();
        if($filesystem->exists(GlobalsMedia::getUploadDir() . $parameters['slug']) === false) {
            try {
                $filesystem->mkdir(GlobalsMedia::getUploadDir() . $parameters['slug'], 0700);
            } catch (IOException $e) {
                echo "An error occured while creating your directory";
            }
        }

        switch ($parameters['extension']) {
            case 'jpg':
                $img_r = imagecreatefromjpeg($parameters['src']);
                break;
            case 'jpeg':
                $img_r = imagecreatefromjpeg($parameters['src']);
                break;
            case 'gif':
                $img_r = imagecreatefromgif($parameters['src']);
                break;
            case 'png':
                $img_r = imagecreatefrompng($parameters['src']);
                break;
            default:
                echo "L'image n'est pas dans un format reconnu. Extensions autorisÃ©es : jpg, jpeg, gif, png";
                break;
        }
        $dst_r = imagecreatetruecolor($parameters['w_new'], $parameters['h_new']);
        if($parameters['extension'] == "gif" || $parameters['extension'] == "png"){
            imagecolortransparent($dst_r, imagecolorallocatealpha($dst_r, 0, 0, 0, 127));
            imagealphablending($dst_r, false);
            imagesavealpha($dst_r, true);
        }

        $this->fastimagecopyresampled($dst_r, $img_r, 0, 0, $parameters['x'], $parameters['y'], $parameters['w_new'], $parameters['h_new'], $parameters['w'], $parameters['h']);

        switch ($parameters['extension']) {
            case 'jpg':
                imagejpeg($dst_r, $parameters['destcrop'] , $parameters['quality']);
                break;
            case 'jpeg':
                imagejpeg($dst_r, $parameters['destcrop'] , $parameters['quality']);
                break;
            case 'gif':
                imagegif($dst_r, $parameters['destcrop']);
                break;
            case 'png':
                imagepng($dst_r, $parameters['destcrop']);
                break;
            default:
                echo "L'image n'est pas dans un format reconnu. Extensions autorisÃ©es : jpg, gif, png";
                break;
        }
        @chmod($parameters['destcrop'], 0777);

        return $parameters['src'];
    }

    function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3) {
        // Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
        // Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
        // Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
        // Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 - Project: FreeRingers.net - Freely distributable - These comments must remain.
        //
        // Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5. Must be greater than zero.
        // Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
        // 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
        // 2 = Up to 95 times faster.  Images appear a little sharp, some prefer this over a quality of 3.
        // 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled, just faster.
        // 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
        // 5 = No speedup. Just uses imagecopyresampled, no advantage over imagecopyresampled.

        if (empty($src_image) || empty($dst_image) || $quality <= 0) { return false; }
        if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
            $temp = imagecreatetruecolor ($dst_w * $quality + 1, $dst_h * $quality + 1);
            imagecopyresized ($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1, $src_w, $src_h);
            imagecopyresampled ($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality, $dst_h * $quality);
            imagedestroy ($temp);
        } else imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        return true;
    }

}
