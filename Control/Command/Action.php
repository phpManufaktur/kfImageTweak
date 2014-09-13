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
use phpManufaktur\Basic\Control\kitCommand\Basic;

class Action extends Basic
{
    protected static $parameter = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

        self::$parameter = $this->getCommandParameters();

        // grant that the 'action' value is a lower string
        self::$parameter['action'] = isset(self::$parameter['action']) ? strtolower(self::$parameter['action']) : 'none';

        // grant the 'type' parameter to specify the gallery to use - default is FlexSlider
        self::$parameter['type'] = isset(self::$parameter['type']) ? strtolower(self::$parameter['type']) : 'flexslider';
    }

    /**
     * Action handler for the kitCommand ~~ imagetweak ~~
     *
     * @param Application $app
     */
    public function ControllerAction(Application $app)
    {
        $this->initParameters($app);

        switch (self::$parameter['action']) {
            case 'gallery':
                // show a gallery
                switch (self::$parameter['type']) {
                    case 'flexslider':
                        return $this->createIFrame('/imagetweak/gallery/flexslider');
                    case 'sandbox':
                        return $this->createIFrame('/imagetweak/gallery/sandbox');
                    default:
                        // unknown gallery type!
                        $this->setAlert('The gallery type <b>%type%</b> is unknown, please check the parameters for the kitCommand!',
                            array('%type%' => self::$parameter['type']), self::ALERT_TYPE_WARNING);
                        return $this->createIFrame('/basic/alert/'.base64_encode($this->getAlert()));
                }
            case 'none':
                // missing the action parameter, show the welcome page!
                return $this->createIFrame('/basic/help/imagetweak/welcome');
            default:
                // unknown action parameter!
                $this->setAlert('The action <b>%action%</b> is unknown, please check the parameters for the kitCommand!',
                    array('%action%' => self::$parameter['action']), self::ALERT_TYPE_WARNING);
                return $this->createIFrame('/basic/alert/'.base64_encode($this->getAlert()));
        }
    }

}
