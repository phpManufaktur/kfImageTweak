<?php

/**
 * imageTweak
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de
 * @copyright 2008, 2011, 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

$filter->post('/imagetweak',
    'phpManufaktur\imageTweak\Control\imageTweak::controllerImageTweak')
    ->setOption('info', MANUFAKTUR_PATH.'/imageTweak/filter.imagetweak.json');
