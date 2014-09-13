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

class Setup
{
    protected $app = null;
    protected $Configuration = null;

    /**
     * Check if the entries for imageTweak exists in the config.jsoneditor.json
     *
     * @param Application $app
     */
    public function jsonEditorConfiguration(Application $app)
    {
        $jsonEditorConfiguration = new \phpManufaktur\Basic\Control\jsonEditor\Configuration($app);
        $json_config = $jsonEditorConfiguration->getConfiguration();

        if (!isset($json_config['help']['gallery.json'])) {
            $json_config['help']['gallery.json'] = 'help_gallery_json';
            $jsonEditorConfiguration->setConfiguration($json_config);
            $jsonEditorConfiguration->saveConfiguration();
        }

        if (!isset($json_config['help']['config.imagetweak.json'])) {
            $json_config['help']['config.imagetweak.json'] = 'help_config_imagetweak_json';
            $jsonEditorConfiguration->setConfiguration($json_config);
            $jsonEditorConfiguration->saveConfiguration();
        }
    }

    /**
     * Controller for the imageTweak setup
     *
     * @param Application $app
     */
    public function Controller(Application $app)
    {
        $this->app = $app;

        // create the config.imagetweak.json
        $this->Configuration = new Configuration($app);

        // check the config.jsoneditor.json for imageTweak entries
        $this->jsonEditorConfiguration($app);

        return $app['translator']->trans('Successfull installed the extension %extension%.',
            array('%extension%' => 'imageTweak'));
    }
}
