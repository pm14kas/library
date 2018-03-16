<?php
namespace App\Twig;

use Twig_Extension;
use \Twig_SimpleFunction;

class ThumbnailExtension extends Twig_Extension {

    public function getName()
    {
        return 'thumbnail_extension';
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'show_image',
                array($this, 'ShowImage'),
                array(
                    'is_safe' => array('html'),
                    'needs_environment' => false,
                )
            ),
        );
    }

    public function ShowImage($source, $width, $height)
    {
        //return ("<img style=\"width: ".$width."px; height: ".$height."px;\" src=\"".$source."\">");
        return ("<img width=\"".$width."\" height=\"".$height."\" src=\"".$source."\">");
    }

}