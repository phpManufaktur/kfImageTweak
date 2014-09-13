<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/imageTweak
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\imageTweak\Data\Setup;

use Silex\Application;
use phpManufaktur\imageTweak\Control\Configuration;

class Update
{
    protected $app = null;
    protected $Configuration = null;

    /**
     * Release 2.1.4
     */
    protected function release_2104()
    {
        $config = $this->Configuration->getConfiguration();

        if (!isset($config['embed']['lightbox2'])) {
            $default = $this->Configuration->getDefaultConfigArray();
            $config['embed']['lightbox2'] = $default['embed']['lightbox2'];
            $this->Configuration->setConfiguration($config);
            $this->Configuration->saveConfiguration();
        }
    }

    /**
     * Release 2.1.7
     */
    protected function release_2107()
    {
        $config = $this->Configuration->getConfiguration();
        if (!isset($config['gallery'])) {
            $default = $this->Configuration->getDefaultConfigArray();
            $config['gallery'] = $default['gallery'];
            $this->Configuration->setConfiguration($config);
            $this->Configuration->saveConfiguration();
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
        $this->Configuration = new Configuration($app);

        $this->release_2104();
        $this->release_2107();

        // check the config.jsoneditor.json for imageTweak entries
        $Setup = new Setup();
        $Setup->jsonEditorConfiguration($app);

        return $app['translator']->trans('Successfull updated the extension %extension%.',
            array('%extension%' => 'imageTweak'));
    }
}
