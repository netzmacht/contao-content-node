<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Netzmacht\Contao\ContentNode\Dca\RedirectHelper;

$GLOBALS['TL_DCA']['tl_content_node'] = array
(
    'config' => array(
        'dataContainer'   => 'Table',
        'ctable'          => array('tl_content'),
        'onload_callback' => array(
            RedirectHelper::callback('redirect'),
        ),
        'sql'           => array
        (
            'keys' => array
            (
                'id'  => 'primary',
                'pid' => 'index'
            )
        ),
    ),
    'fields' => array
    (
        'id'     => array
        (
            'sql' => "int(10) unsigned NOT NULL"
        ),
        'pid'    => array
        (
            'sql' => "int(10) unsigned NOT NULL"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
    )
);
