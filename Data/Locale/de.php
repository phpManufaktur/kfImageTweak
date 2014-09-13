<?php

/**
 * kitFramework::imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/imageTweak
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 *
 * This file was created by the kitFramework i18nEditor
 */

if ('á' != "\xc3\xa1") {
    // the language files must be saved as UTF-8 (without BOM)
    throw new \Exception('The language file ' . __FILE__ . ' is damaged, it must be saved UTF-8 encoded!');
}

return array(
  'help_config_imagetweak_json'
    => '<p>Diese Konfigurationsdatei wird für beide Teile der <em>imageTweak</em> Erweiterung benörigt, den <em>kitFilter</em> und das <em>kitCommand</em>.</p><p>Bitte besuchen Sie das <a href="https://github.com/phpManufaktur/kfImageTweak/wiki/config.image.tweak.json" target="_blank">imageTweak Wiki</a> um mehr über den Aufbau und die Einstellungen in der <a href="https://github.com/phpManufaktur/kfImageTweak/wiki/config.image.tweak.json" target="_blank"><var>config.image.tweak.json</var></a> zu erfahren.</p>',
  'help_gallery_json'
    => '<p>Dies ist die Beschreibungsdatei für eine <em>imageTweak Galerie</em> - sehen Sie die <a href="https://gist.github.com/hertsch/014d698a9585f2cd3dff#file-kitcommand_imagetweak_en-md" target="_blank">imageTweak Hilfe</a> für weitere Informationen, wie eine Galerie angelegt wird.</p><p><em>imageTweak</em> legt automatisch ein Unterverzeichnis <var>gallery</var> und eine <var>gallery.json</var> an. Die Beschreibungsdatei enthält für jedes Bild der Galerie einen Eintrag. Unterhalb der Bilder finden Sie vier weitere Einträge: <var>name, fullsize, thumbnail</var> und <var>locale</var>. Sie sollten <em>ausschließlich</em> Einträge unterhalb von <var>locale</var> ändern.</p><p><var>locale</var> enthält die Sprachen, die in der <a href="%FRAMEWORK_URL%/admin/json/editor/open/file/config.imagetweak.json"><var>config.image.json</var></a> definiert sind. Sie können für jedes Bild und jede Sprache eine <var>description</var> festlegen, die für den <var>alt</var> und <var>title</var> Tag des Bildes verwendet wird sowie einen <var>content</var> Eintrag festlegen. <var>content</var> kann jeden beliebigen <em>Text</em> oder auch <em>HTML</em> enthalten und wird am unteren Rand des Bildes angezeigt. Sie können außerdem einen <var>link</var> festlegen, füllen Sie einfach <var>url</var> und optional <var>title</var> für den Link aus.</p>',
  'imageTweak - FlexSlider'
    => 'imageTweak - FlexSlider',
  'imageTweak - Sandbox'
    => 'imageTweak - Sandbox',
  'Invalid image path!'
    => 'Ungültiger Abbildungspfad!',
  'Next'
    => 'Nächstes',
  'Please check the parameters for the kitCommand and specify a valid <i>base</i> and <i>directory</i>'
    => 'Bitte überprüfen Sie die Parameter für das kitCommand und geben Sie gültige Werte für <em>base</em> und <em>directory</em> an!',
  'Previous'
    => 'Vorheriges',
  'The gallery type <b>%type%</b> is unknown, please check the parameters for the kitCommand!'
    => 'Der Galerie Typ <b>%type%</b> ist unbekannt, bitte prüfen Sie die Parameter für das kitCommand!',
  
);
