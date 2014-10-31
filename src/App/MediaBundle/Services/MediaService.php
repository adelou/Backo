<?php
namespace App\MediaBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

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
        $parameters['destcrop'] = $this->get('kernel')->getRootDir() . '/../web/uploads/medias/'.$parameters['path'];
        $filesystem = new Filesystem();
        if($filesystem->exists( $this->get('kernel')->getRootDir() . '/../web/uploads/medias/' . $parameters['slug']) === false) {
            try {
                $filesystem->mkdir($this->get('kernel')->getRootDir() . '/../web/uploads/medias/' . $parameters['slug'], 0700);
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
        imagecopyresampled($dst_r, $img_r, 0, 0, $parameters['x'], $parameters['y'], $parameters['w_new'], $parameters['h_new'], $parameters['w'], $parameters['h']);

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

        return true;
    }
}