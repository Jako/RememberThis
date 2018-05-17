<?php
/**
 * @package rememberthis
 */
$xpdo_meta_map['RememberThisList']= array (
  'package' => 'rememberthis',
  'version' => '1.1',
  'table' => 'rememberthis_list',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'user_id' => 0,
    'list' => NULL,
    'hash' => NULL,
    'createdon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'list' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
    'hash' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => true,
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'timestamp',
      'null' => true,
    ),
  ),
);
