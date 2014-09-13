<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/imageTweak
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

// administrative links
$admin->get('/imagetweak/setup',
    'phpManufaktur\imageTweak\Data\Setup\Setup::Controller');
$admin->get('/imagetweak/update',
    'phpManufaktur\imageTweak\Data\Setup\Update::ControllerUpdate');

// imageTweak FILTER (main function)
$filter->post('/imagetweak',
    'phpManufaktur\imageTweak\Control\Filter\imageTweak::controllerImageTweak')
    ->setOption('info', MANUFAKTUR_PATH.'/imageTweak/filter.imagetweak.json');

// imageTweak kitCOMMAND (for galleries ...)
$command->post('/imagetweak',
    'phpManufaktur\imageTweak\Control\Command\Action::ControllerAction')
    ->setOption('info', MANUFAKTUR_PATH.'/imageTweak/command.imagetweak.json');

$app->get('/imagetweak/gallery/flexslider',
    'phpManufaktur\imageTweak\Control\Command\GalleryFlexSlider::ControllerGallery');

$app->get('/imagetweak/gallery/sandbox',
    'phpManufaktur\imageTweak\Control\Command\GallerySandbox::ControllerGallery');
