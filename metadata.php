<?php

$sMetadataVersion = '2.0';

$aModule = array(
    'id'          => 'rs-dbschema',
    'title'       => '*RS DB Schema',
    'description' => 'Display DB schema',
    'thumbnail'   => '',
    'version'     => '1.0.0',
    'author'      => '',
    'url'         => '',
    'email'       => '',
    'controllers' => array(
        'rs_dbschema_ide' => \rs\dbschema\Application\Controller\Admin\rs_dbschema_ide::class,
    ),
    'extend'      => array(
    ),
    'templates' => array(
        'rs_dbschema_ide.tpl'   => 'rs/dbschema/views/tpl/rs_dbschema_ide.tpl',
        'inc/rs_dbschema_addrelations.tpl'   => 'rs/dbschema/views/tpl/inc/rs_dbschema_addrelations.tpl',
        'inc/rs_dbschema_addtables.tpl'   => 'rs/dbschema/views/tpl/inc/rs_dbschema_addtables.tpl',
        'inc/rs_dbschema_line.tpl'   => 'rs/dbschema/views/tpl/inc/rs_dbschema_line.tpl',
        'inc/rs_dbschema_table.tpl'   => 'rs/dbschema/views/tpl/inc/rs_dbschema_table.tpl',
        'inc/rs_dbschema_tables.tpl'   => 'rs/dbschema/views/tpl/inc/rs_dbschema_tables.tpl',
        'inc/rs_dbschema_tablelist.tpl'   => 'rs/dbschema/views/tpl/inc/rs_dbschema_tablelist.tpl',

    ),
    'blocks'      => array(
    ),
    'settings'    => array(
    ),
);