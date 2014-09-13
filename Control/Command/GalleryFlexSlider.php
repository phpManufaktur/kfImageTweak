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

class GalleryFlexSlider extends Gallery
{

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

        // grant the 'mode' parameter for the gallery
        self::$parameter['mode'] = isset(self::$parameter['mode']) ? strtolower(self::$parameter['mode']) : 'basic';
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
            strtolower(self::$parameter['use_css']) : 'true';

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
