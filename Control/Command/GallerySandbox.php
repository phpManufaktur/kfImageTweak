<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/imageTweak
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\imageTweak\Control\Command;

use Silex\Application;

class GallerySandbox extends Gallery
{

    /**
     * Controller for the imageTweak Gallery in Sandbox mode
     *
     * @param Application $app
     */
    public function ControllerGallery(Application $app)
    {
        $this->initParameters($app);

        if (is_null(self::$parameter['base']) || is_null(self::$parameter['directory'])) {
            // invalid BASE and/or DIRECTORY
            $this->setAlert('Please check the parameters for the kitCommand and specify a valid <i>base</i> and <i>directory</i>',
                array(), self::ALERT_TYPE_WARNING);
            return $this->createIFrame('/basic/alert/'.base64_encode($this->getAlert()));
        }

        $gallery = array();
        $this->createImageArray($gallery);

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/imageTweak/Template', 'sandbox.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'gallery' => $gallery,
                'parameter' => self::$parameter,
                'config' => self::$config
            ));
    }
}
