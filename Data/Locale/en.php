<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/imageTweak
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

if ('á' != "\xc3\xa1") {
    // the language files must be saved as UTF-8 (without BOM)
    throw new \Exception('The language file ' . __FILE__ . ' is damaged, it must be saved UTF-8 encoded!');
}

return array(
    'help_config_imagetweak_json'
        => '<p>This configuration file is needed for both parts of <em>imageTweak</em>, the <em>kitFilter</em> and the <em>kitCommand</em>.</p><p>Please visit the <a href="https://github.com/phpManufaktur/kfImageTweak/wiki/config.image.tweak.json" target="_blank">imageTweak Wiki</a> to get information about the structure and the settings of the <a href="https://github.com/phpManufaktur/kfImageTweak/wiki/config.image.tweak.json" target="_blank"><var>config.image.tweak.json</var></a>.</p>',
    'help_gallery_json'
        => '<p>This is the description file for a <em>imageTweak gallery</em> - see the <a href="https://gist.github.com/hertsch/014d698a9585f2cd3dff#file-kitcommand_imagetweak_en-md" target="_blank">imageTweak Help</a> for information who to create a gallery.</p><p><em>imageTweak</em> automatically create the subdirectory <var>/gallery</var> and the <var>gallery.json</var> file. The description file contain an entry for each image. At the next level there are four entries <var>name, fullsize, thumbnail</var> and <var>locale</var>. You should <em>only</em> change <i>values</i> below the entry <var>locale</var>.</p><p><var>locale</var> contains the languages specified in the <var>config.imagetweak.json</var>. You can specify an <var>description</var> which will be used for the <em>title</em> and <em>alt</em> tag of the images and a <var>content</var>. The <var>content</var> can contain any text or <em>HTML</em> and will be shown at the bottom of the fullsize images. You can also define <var>link</var>, just specify a <var>url</var> and optional an <var>title</var> tag for the link.</p>',

);
