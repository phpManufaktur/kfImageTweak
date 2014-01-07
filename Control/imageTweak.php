<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\imageTweak\Control;

use Silex\Application;

class imageTweak
{
    protected $app = null;
    protected static $parameter = null;
    protected static $cms = null;
    protected static $content = null;
    protected static $filter_expression = null;
    protected static $config = null;

    /**
     * Initialize imageTweak
     *
     * @param Application $app
     * @throws \Exception
     */
    protected function initialize(Application $app)
    {
        $this->app = $app;

        if (null === (self::$parameter = $app['request']->request->get('parameter', null))) {
            self::$parameter = array();
        }

        if (null === (self::$cms = $app['request']->request->get('cms', null))) {
            throw new \Exception('Missing the CMS information bag!');
        }

        if (null === (self::$content = $app['request']->request->get('content', null))) {
            throw new \Exception('Missing the content for the filter execution.');
        }

        if (null === (self::$filter_expression = $app['request']->request->get('filter_expression', null))) {
            throw new \Exception('Missing the filter expression.');
        }

        if (isset(self::$cms['locale'])) {
            // set the locale from the CMS locale
            $this->app['translator']->setLocale(self::$cms['locale']);
        }

        // remove the filter expression (clean up)
        self::$content = str_replace(self::$filter_expression, '', self::$content);

        $Configuration = new Configuration($app);
        self::$config = $Configuration->getConfiguration();
    }

    /**
     * Controller for the imageTweak filter
     *
     * @param Application $app
     */
    public function controllerImageTweak(Application $app)
    {
        $this->initialize($app);

        if (!self::$config['enabled']) {
            // imageTweak is not enabled, nothing to do ...
            return self::$content;
        }

        $DOM = new \DOMDocument();
        // suppress any errors at this point and take the content "as it is"!
        @$DOM->loadHTML(self::$content);

        foreach ($DOM->getElementsByTagName('img') as $image) {
            // loop through the image tags
            $src = $image->getAttribute('src');
            if ((strpos($src, CMS_MEDIA_URL) === 0) || (strpos($src, FRAMEWORK_MEDIA_URL) === 0)) {
                // process only images from the CMS /media or the kitFramework /kit2/media directory
                $width = null;
                $height = null;

                $alt = $image->getAttribute('alt');
                $title = $image->getAttribute('title');

                if (self::$config['image']['alt']['set'] && empty($alt)) {
                    $image->setAttribute('alt', (!empty($title)) ? $title : self::$config['image']['alt']['default']);
                }

                if (self::$config['image']['title']['set'] && empty($title)) {
                    $title = (!empty($alt)) ? $alt : self::$config['image']['title']['default'];
                    $image->setAttribute('title', $title);
                }

                $style_str = $image->getAttribute('style');

                if (!empty($style_str)) {
                    // it is possible that the width and height are set as CSS style information
                    $style_array = (strpos($style_str, ';')) ? explode(';', $style_str) : array(trim($style_str));
                    $styles = array();
                    foreach ($style_array as $item) {
                        if (strpos($item, ':')) {
                            list($key, $value) = explode(':', $item);
                            if ((strtolower(trim($key)) == 'width') || (strtolower(trim($key)) == 'height')) {
                                if (strtolower(trim($key)) == 'width') {
                                    $width = trim($value);
                                }
                                else {
                                    $height = trim($value);
                                }
                            }
                            else {
                                $styles[trim($key)] = trim($value);
                            }
                        }
                    }

                    // write back the style information
                    $style_str = '';
                    foreach ($styles as $key => $value) {
                        $style_str .= "$key:$value;";
                    }
                    if (empty($style_str)) {
                        // the style attribute is no longer needed
                        $image->removeAttribute('style');
                    }
                    else {
                        // write back the style attribute
                        $image->setAttribute('style', $style_str);
                    }

                    if (!is_null($width)) {
                        // set the width attribute
                        $image->setAttribute('width', trim(str_ireplace('px', '', $width)));
                    }
                    if (!is_null($height)) {
                        // set the height attribute
                        $image->setAttribute('height', trim(str_ireplace('px', '', $height)));
                    }
                }

                $class_str = $image->getAttribute('class');
                $class = array();
                if (!empty($class_str)) {
                    if (strpos($class_str, ' ')) {
                        $classes = explode(' ', $class_str);
                        foreach ($classes as $item) {
                            if (!empty($item)) {
                                $class[] = trim($item);
                            }
                        }
                    }
                    else {
                        $class = array($class_str);
                    }
                }

                // loop through embedded items - i.e. for a fancybox replacement
                foreach (self::$config['embed'] as $embed) {
                    if (isset($embed['image']['class']) && in_array($embed['image']['class'], $class)) {
                        // embed the image tag with the given element and attributes (<a href="#" ...)
                        $node = $DOM->createElement($embed['element']);
                        foreach ($embed['attribute'] as $key => $value) {
                            $node->setAttribute($key, str_replace(array('{src}','{title}'), array($src, $title), $value));
                        }
                        // replace the image with the new node
                        $image->parentNode->replaceChild($node, $image);
                        // append the image to the new node
                        $node->appendChild($image);
                        if ($embed['image']['remove']) {
                            unset($class[array_search($embed['image']['class'], $class)]);
                        }
                        // leave the loop!
                        break;
                    }
                }

                if (empty($class)) {
                    $image->removeAttribute('class');
                }
                else {
                    $image->setAttribute('class', implode(' ', $class));
                }

                $width = $image->getAttribute('width');
                $height = $image->getAttribute('height');

                if ((false !== (strpos($width, '%'))) || (false !== (strpos($height, '%')))) {
                    // imageTweak can not handle percentage image sizes
                    continue;
                }

                // get the path
                $relative_path = (strpos($src, CMS_MEDIA_URL) === 0) ? substr($src, strlen(CMS_URL)) : substr($src, strlen(FRAMEWORK_URL));
                $image_path = (strpos($src, CMS_MEDIA_URL) === 0) ? CMS_PATH.$relative_path : FRAMEWORK_PATH.$relative_path;

                if (!$app['filesystem']->exists($image_path)) {
                    // the image does not exists!
                    $app['monolog']->addError("[imageTweak] The image $src does not exists!", array(__METHOD__, __LINE__));
                    continue;
                }

                list($origin_width, $origin_height, $image_type) = getimagesize($image_path);

                if (empty($width) && empty($height)) {
                    // missing the attributes for width and height
                    $image->setAttribute('width', $origin_width);
                    $image->setAttribute('height', $origin_height);
                    // nothing else to do ...
                    continue;
                }
                elseif (empty($height)) {
                    // missing the attribute for the height
                    if ($width == $origin_width) {
                        $image->setAttribute('height', $origin_height);
                        // nothing else to do ...
                        continue;
                    }
                    else {
                        // calculate the height
                        $percent = (int) ($width / ($origin_width/100));
                        $height = (int) (($origin_height / 100) * $percent);
                        $image->setAttribute('height', $height);
                    }
                }
                elseif (empty($width)) {
                    // missing the attribute for the width
                    if ($height == $origin_height) {
                        $image->setAttribute('width', $origin_width);
                        // nothing else to do ...
                        continue;
                    }
                    else {
                        // calculate the width
                        $percent = (int) ($height / ($origin_height/100));
                        $width = (int) (($origin_width / 100) * $percent);
                        $image->setAttribute('width', $width);
                    }
                }

                if (($width >= $origin_width) || ($height >= $origin_height)) {
                    // image is zoomed or in original size, nothing to do ...
                    continue;
                }

                $pathinfo = pathinfo($relative_path);
                $tweaked_file = sprintf('/tweaked%s/%s_%dx%d.%s', $pathinfo['dirname'], $pathinfo['filename'],
                    $width, $height, $pathinfo['extension']);

                if ($app['filesystem']->exists(FRAMEWORK_MEDIA_PATH.$tweaked_file)) {
                    if (filemtime($image_path) == filemtime(FRAMEWORK_MEDIA_PATH.$tweaked_file)) {
                        // file exists and has not changed, set source to tweaked file and continue ...
                        $image->setAttribute('src', FRAMEWORK_MEDIA_URL.$tweaked_file);
                        continue;
                    }
                }

                // create the directory if needed
                $app['filesystem']->mkdir(dirname(FRAMEWORK_MEDIA_PATH.$tweaked_file));

                // resample the image
                $app['image']->resampleImage($image_path, $image_type, $origin_width, $origin_height,
                    FRAMEWORK_MEDIA_PATH.$tweaked_file, $width, $height);

                // set the source
                $image->setAttribute('src', FRAMEWORK_MEDIA_URL.$tweaked_file);

                $this->app['monolog']->addDebug('[imageTweak] Tweaked the file '.FRAMEWORK_MEDIA_URL.$tweaked_file);
            }
        }

        // return the content
        return $DOM->saveHTML();
    }
}
