<?php
/**
 * @package Order Comments Boilerplate
 * @copyright Copyright 2008-2017 Zen4All
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
if (class_exists('AdminRequestSanitizer')) {
    $sanitizer = AdminRequestSanitizer::getInstance();
    $group = array(
        'comment_title' => array('sanitizerType' => 'PRODUCT_DESC_REGEX',
                                   'method' => 'both',
                                   'pages' => array('order_comment_boilerplate'),
                                   'params' => array()),
        'comment_content' => array('sanitizerType' => 'PRODUCT_DESC_REGEX',
                                   'method' => 'both',
                                   'pages' => array('order_comment_boilerplate'),
                                   'params' => array()),
        );
    $sanitizer->addComplexSanitization($group);
}