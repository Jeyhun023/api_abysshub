<?php
$array = ['test' => 'value', ['etc...']];

$keys = array_keys( $array );
$keys[array_search('test', $keys, true)] = 'test2';
array_combine( $keys, $array );