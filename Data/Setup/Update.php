<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\imageTweak\Data\Setup;

use Silex\Application;
use phpManufaktur\imageTweak\Control\Configuration;

class Update
{
    protected $app = null;

    /**
     * Release 2.1.4
     */
    protected function release_2104()
    {
        $Configuration = new Configuration($this->app);
        $config = $Configuration->getConfiguration();

        if (!isset($config['embed']['lightbox2'])) {
            $config['embed']['lightbox2'] = array(
                'image' => array(
                    'class' => 'tweak-lightbox',
                    'remove' => true,
                ),
                'element' => 'a',
                'attribute' => array(
                    'href' => '{src}',
                    'data-title' => '{title}',
                    'data-lightbox' => 'lightbox'
                )
            );
            $Configuration->setConfiguration($config);
            $Configuration->saveConfiguration();
        }
    }

    /**
     * Controller to execute the update for imageTweak
     *
     * @param Application $app
     */
    public function ControllerUpdate(Application $app)
    {
        $this->app = $app;

        $this->release_2104();

        return $app['translator']->trans('Successfull updated the extension %extension%.',
            array('%extension%' => 'imageTweak'));
    }
}
