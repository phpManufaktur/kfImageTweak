<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\imageTweak\Control\Command;

use Silex\Application;
use phpManufaktur\Basic\Control\kitCommand\Basic;
use Symfony\Component\Finder\Finder;
use phpManufaktur\imageTweak\Control\Configuration;

class GalleryFlexSlider extends Basic
{
    protected static $parameter = null;
    protected static $base_array = array('cms_media', 'media', 'media_protected', 'path');
    protected static $image_path = null;
    protected static $image_url = null;
    protected static $config = null;

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

        // grant the 'type' parameter to specify the gallery to use
        self::$parameter['type'] = isset(self::$parameter['type']) ? strtolower(self::$parameter['type']) : 'flexslider';

        // grant the 'mode' parameter for the gallery
        self::$parameter['mode'] = isset(self::$parameter['mode']) ? strtolower(self::$parameter['mode']) : 'basic';

        // check the parameter 'base'
        if (!isset(self::$parameter['base'])) {
            self::$parameter['base'] = 'cms_media';
        }
        else {
            self::$parameter['base'] = strtolower(self::$parameter['base']);
            if (!in_array(self::$parameter['base'], self::$base_array)) {
                self::$parameter['base'] = null;
            }
        }

        // we need a directory where to load the images from
        self::$parameter['directory'] = isset(self::$parameter['directory']) ? trim(trim(self::$parameter['directory']), '/') : null;

        if (!is_null(self::$parameter['base']) && !is_null(self::$parameter['directory'])) {
            switch (self::$parameter['base']) {
                case 'cms_media':
                    self::$image_path = CMS_MEDIA_PATH.'/'.self::$parameter['directory'];
                    self::$image_url = CMS_MEDIA_URL.'/'.self::$parameter['directory'];
                    break;
                case 'media':
                    self::$image_path = FRAMEWORK_MEDIA_PATH.'/'.self::$parameter['directory'];
                    self::$image_url = FRAMEWORK_MEDIA_URL.'/'.self::$parameter['directory'];
                    break;
                case 'media_protected':
                    self::$image_path = FRAMEWORK_MEDIA_PROTECTED_PATH.'/'.self::$parameter['directory'];
                    self::$image_url = FRAMEWORK_MEDIA_PROTECTED_URL.'/'.self::$parameter['directory'];
                    break;
                case 'path':
                    self::$image_path = self::$parameter['directory'];
                    $path = substr(self::$parameter['directory'], strlen(CMS_URL));
                    self::$image_url = CMS_URL.$path;
                    break;
            }
        }

        $Configuration = new Configuration($app);
        self::$config = $Configuration->getConfiguration();
    }

    /**
     * Create the gallery information file, create the fullsize and thumbnail
     * images, load and save the gallery.json file
     *
     * @param array reference $gallery_info
     * @return boolean
     */
    protected function createImageArray(&$gallery_info=array())
    {
        if (is_null(self::$image_path)) {
            $this->setAlert('Invalid image path!', array(), self::ALERT_TYPE_DANGER);
            return false;
        }

        $origin_files = array();
        $images = new Finder();
        foreach (self::$config['gallery']['image']['extension'] as $extension) {
            // add all specified image extensions
            $images->name($extension);
        }
        $images->in(self::$image_path)->sortByName()->depth('== 0');

        $directories = array('/gallery', '/gallery/thumbnail', '/gallery/fullsize');
        foreach ($directories as $directory) {
            if (!$this->app['filesystem']->exists(self::$image_path.$directory)) {
                $this->app['filesystem']->mkdir(self::$image_path.$directory);
            }
        }

        $gallery_info = array();
        if ($this->app['filesystem']->exists(self::$image_path.'/gallery/gallery.json')) {
            // read the gallery information file
            $gallery_info = $this->app['utils']->ReadJSON(self::$image_path.'/gallery/gallery.json');
        }

        $json_changed = false;

        foreach ($images as $image) {
            $basename = $image->getBasename();
            $origin_files[] = $basename;
            $image_info = $this->app['image']->getImageInfo($image->getRealPath());
            $fullsize_image = self::$image_path.'/gallery/fullsize/'.$basename;
            $thumbnail_image = self::$image_path.'/gallery/thumbnail/'.$basename;
            if (!$this->app['filesystem']->exists($fullsize_image) || (filemtime($fullsize_image) !== $image_info['last_modified'])) {
                // create the fullsize image
                $full_size = $this->app['image']->reCalculateImage($image_info['width'], $image_info['height'],
                    self::$config['gallery']['image']['fullsize']['max_width'],
                    self::$config['gallery']['image']['fullsize']['max_height']);
                $this->app['image']->resampleImage($image->getRealPath(), $image_info['image_type'],
                    $image_info['width'], $image_info['height'], $fullsize_image,
                    $full_size['width'], $full_size['height']);
                $this->app['filesystem']->touch($fullsize_image, $image_info['last_modified']);
                // create the thumbnail image
                $thumb_size = $this->app['image']->reCalculateImage($image_info['width'], $image_info['height'],
                    self::$config['gallery']['image']['thumbnail']['max_width'],
                    self::$config['gallery']['image']['thumbnail']['max_height']);
                $this->app['image']->resampleImage($image->getRealPath(), $image_info['image_type'],
                    $image_info['width'], $image_info['height'], $thumbnail_image,
                    $thumb_size['width'], $thumb_size['height']);
                $this->app['filesystem']->touch($thumbnail_image, $image_info['last_modified']);
                $gallery_info[$basename] = array(
                    'name' => $basename,
                    'fullsize' => array(
                        'url' => self::$image_url.'/gallery/fullsize/'.$basename,
                        'width' => $full_size['width'],
                        'height' => $full_size['height']
                    ),
                    'thumbnail' => array(
                        'url' => self::$image_url.'/gallery/thumbnail/'.$basename,
                        'width' => $thumb_size['width'],
                        'height' => $thumb_size['height']
                    ),
                    'locale' => array(
                        'fallback' => self::$config['gallery']['locale']['fallback']
                    )
                );
                foreach (self::$config['gallery']['locale']['locales'] as $locale) {
                    $gallery_info[$basename]['locale'][$locale] = array(
                        'description' => isset($gallery_info[$basename]['locale'][$locale]['description']) ? $gallery_info[$basename]['locale'][$locale]['description'] : '',
                        'content' => isset($gallery_info[$basename]['locale'][$locale]['content']) ? $gallery_info[$basename]['locale'][$locale]['content'] : '',
                        'link' => array(
                            'url' => isset($gallery_info[$basename]['locale'][$locale]['link']['url']) ? $gallery_info[$basename]['locale'][$locale]['link']['url'] : '',
                            'target' => isset($gallery_info[$basename]['locale'][$locale]['link']['target']) ? $gallery_info[$basename]['locale'][$locale]['link']['target'] : '_parent',
                            'title' => isset($gallery_info[$basename]['locale'][$locale]['link']['title']) ? $gallery_info[$basename]['locale'][$locale]['link']['title'] : ''
                        )
                    );
                }
                $json_changed = true;
            }
        }

        $check_files = new Finder();
        $check_files->in(self::$image_path.'/gallery/fullsize/')->files()->depth('== 0');
        foreach ($check_files as $file) {
            if (!in_array($file->getBasename(), $origin_files)) {
                // image does no longer exists, remove it
                $this->app['filesystem']->remove($file->getRealPath());
                $this->app['filesystem']->remove(self::$image_path.'/gallery/thumbnail/'.$file->getBasename());
                // remove it also form the gallery info
                unset($gallery_info[$file->getBasename()]);
                $json_changed = true;
            }
        }

        if ($json_changed) {
            // sort array ascending
            ksort($gallery_info);
            file_put_contents(self::$image_path.'/gallery/gallery.json', $this->app['utils']->JSONFormat($gallery_info));
        }

        return true;
    }

    /**
     * Controller for the FlexSlider Gallery
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

        self::$parameter['animation'] = (isset(self::$parameter['animation']) &&
            in_array(strtolower(self::$parameter['animation']), array('fade', 'slide'))) ?
            strtolower(trim(self::$parameter['animation'])) : 'slide';

        if (isset(self::$parameter['control_nav'])) {
            if (strtolower(self::$parameter['control_nav']) === 'true') {
                self::$parameter['control_nav'] = 'true';
            }
            elseif (strtolower(self::$parameter['control_nav']) === 'false') {
                self::$parameter['control_nav'] = 'false';
            }
            else {
                self::$parameter['control_nav'] = '"'.strtolower(self::$parameter['control_nav']).'"';
            }
        }
        else {
            self::$parameter['control_nav'] = 'true';
        }

        self::$parameter['start_at'] = (isset(self::$parameter['start_at']) && is_numeric(self::$parameter['start_at'])) ?
            intval(self::$parameter['start_at']) : 0;
        self::$parameter['easing'] = (isset(self::$parameter['easing']) && in_array(strtolower(self::$parameter['easing']), array('swing', 'linear'))) ?
            strtolower(self::$parameter['easing']) : 'linear';
        self::$parameter['direction'] = (isset(self::$parameter['direction']) && in_array(strtolower(self::$parameter['direction']), array('horizontal', 'vertical'))) ?
            strtolower(self::$parameter['direction']) : 'horizontal';
        self::$parameter['reverse'] = (isset(self::$parameter['reverse']) && in_array(strtolower(self::$parameter['reverse']), array('true', 'false'))) ?
            strtolower(self::$parameter['reverse']) : 'false';
        self::$parameter['animation_loop'] = (isset(self::$parameter['animation_loop']) && in_array(strtolower(self::$parameter['animation_loop']), array('true', 'false'))) ?
            strtolower(self::$parameter['animation_loop']) : 'true';
        self::$parameter['smooth_height'] = (isset(self::$parameter['smooth_height']) && in_array(strtolower(self::$parameter['smooth_height']), array('true', 'false'))) ?
            strtolower(self::$parameter['smooth_height']) : 'false';
        self::$parameter['slideshow'] = (isset(self::$parameter['slideshow']) && in_array(strtolower(self::$parameter['slideshow']), array('true', 'false'))) ?
            strtolower(self::$parameter['slideshow']) : 'true';
        self::$parameter['slideshow_speed'] = (isset(self::$parameter['slideshow_speed']) && is_numeric(self::$parameter['slideshow_speed'])) ?
            intval(self::$parameter['slideshow_speed']) : 7000;
        self::$parameter['animation_speed'] = (isset(self::$parameter['animation_speed']) && is_numeric(self::$parameter['animation_speed'])) ?
            intval(self::$parameter['animation_speed']) : 600;
        self::$parameter['init_delay'] = (isset(self::$parameter['init_delay']) && is_numeric(self::$parameter['init_delay'])) ?
            intval(self::$parameter['init_delay']) : 0;
        self::$parameter['randomize'] = (isset(self::$parameter['randomize']) && in_array(strtolower(self::$parameter['randomize']), array('true', 'false'))) ?
            strtolower(self::$parameter['randomize']) : 'false';
        self::$parameter['use_css'] = (isset(self::$parameter['use_css']) && in_array(strtolower(self::$parameter['use_css']), array('true', 'false'))) ?
            strtolower(self::$parameter['reverse']) : 'true';
        self::$parameter['direction_nav'] = (isset(self::$parameter['direction_nav']) && in_array(strtolower(self::$parameter['direction_nav']), array('true', 'false'))) ?
            strtolower(self::$parameter['direction_nav']) : 'true';
        // carousel parameters
        self::$parameter['item_width'] = (isset(self::$parameter['item_width']) && is_numeric(self::$parameter['item_width'])) ?
            intval(self::$parameter['item_width']) : self::$config['gallery']['image']['thumbnail']['max_width'];
        self::$parameter['item_margin'] = (isset(self::$parameter['item_margin']) && is_numeric(self::$parameter['item_margin'])) ?
            intval(self::$parameter['item_margin']) : 0;
        self::$parameter['min_items'] = (isset(self::$parameter['min_items']) && is_numeric(self::$parameter['min_items'])) ?
            intval(self::$parameter['min_items']) : 0;
        self::$parameter['max_items'] = (isset(self::$parameter['max_items']) && is_numeric(self::$parameter['max_items'])) ?
            intval(self::$parameter['max_items']) : 0;
        self::$parameter['move'] = (isset(self::$parameter['move']) && is_numeric(self::$parameter['move'])) ?
            intval(self::$parameter['move']) : 0;


        // predefined parameters
        self::$parameter['sync'] = '';

        // mode parameter
        $mode_array = array(
            'slider',
            'thumbnail',
            'thumbnail_slider',
            'carousel',
            'carousel_range',
            'carousel_dynamic'
        );
        self::$parameter['mode'] = (isset(self::$parameter['mode']) && in_array(strtolower(self::$parameter['mode']), $mode_array)) ?
            strtolower(self::$parameter['mode']) : 'slider';

        switch (self::$parameter['mode']) {
            case 'thumbnail':
                self::$parameter['control_nav'] = '"thumbnails"';
                break;
            case 'thumbnail_slider':
                self::$parameter['animation'] = 'slide';
                self::$parameter['control_nav'] = 'false';
                self::$parameter['animation_loop'] = 'false';
                self::$parameter['slideshow'] = 'false';
                self::$parameter['sync'] = '#carousel';
                break;
            case 'carousel':
                self::$parameter['animation'] = 'slide';
                self::$parameter['animation_loop'] = 'false';
                break;
            default:
                // nothing to do ...
                break;
        }

        $gallery = array();
        $this->createImageArray($gallery);

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/imageTweak/Template', 'flexslider.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'gallery' => $gallery,
                'parameter' => self::$parameter,
                'config' => self::$config
            ));
    }
}
