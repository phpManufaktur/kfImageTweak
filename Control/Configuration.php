<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/imageTweak
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\imageTweak\Control;

use Silex\Application;

class Configuration
{
    protected $app = null;
    protected static $config = null;
    protected static $config_path = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$config_path = MANUFAKTUR_PATH.'/imageTweak/config.imagetweak.json';
        $this->readConfiguration();
    }

    /**
     * Return the default configuration array for flexContent
     *
     * @return array
     */
    public function getDefaultConfigArray()
    {
        return array(
            'enabled' => true,
            'image' => array(
                'alt' => array(
                    'default' => '- no image description available -',
                    'set' => true
                ),
                'title' => array(
                    'default' => '- no image description available -',
                    'set' => true
                )
            ),
            'embed' => array(
                'fancybox' => array(
                    'image' => array(
                        'class' => 'tweak-fancybox',
                        'remove' => true
                    ),
                    'element' => 'a',
                    'attribute' => array(
                        'href' => '{src}',
                        'title' => '{title}',
                        'class' => 'grouped_elements',
                        'rel' => 'fancybox'
                    )
                ),
                'slimbox2' => array(
                    'image' => array(
                        'class' => 'tweak-slimbox',
                        'remove' => true
                    ),
                    'element' => 'a',
                    'attribute' => array(
                        'href' => '{src}',
                        'title' => '{title}',
                        'rel' => 'lightbox'
                    )
                ),
                'lightbox2' => array(
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
                )
            ),
            'gallery' => array(
                'locale' => array(
                    'locales' => array(
                        'en',
                        'de'
                    ),
                    'fallback' => 'en'
                ),
                'image' => array(
                    'extension' => array(
                        '*.jpg',
                        '*.jpeg',
                        '*.png',
                        '*.JPG',
                        '*.JPEG',
                        '*.PNG'
                    ),
                    'fullsize' => array(
                        'max_width' => 1200,
                        'max_height' => 800
                    ),
                    'thumbnail' => array(
                        'max_width' => 250,
                        'max_height' => 200
                    ),
                    'set_title' => true,
                    'set_alt' => true
                )
            )
        );
    }

    /**
     * Read the configuration file
     */
    protected function readConfiguration()
    {
        if (!$this->app['filesystem']->exists(self::$config_path)) {
            self::$config = $this->getDefaultConfigArray();
            $this->saveConfiguration();
        }
        self::$config = $this->app['utils']->readConfiguration(self::$config_path);
    }

    /**
     * Save the configuration file
     */
    public function saveConfiguration()
    {
        // write the formatted config file to the path
        file_put_contents(self::$config_path, $this->app['utils']->JSONFormat(self::$config));
        $this->app['monolog']->addDebug('Save configuration to '.basename(self::$config_path));
    }

    /**
     * Get the configuration array
     *
     * @return array
     */
    public function getConfiguration()
    {
        return self::$config;
    }

    /**
     * Set the configuration array
     *
     * @param array $config
     */
    public function setConfiguration($config)
    {
        self::$config = $config;
    }

}
