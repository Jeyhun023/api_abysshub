<?php
$array = ['test' => 'value', ['etc...']];

$array['test2'] = $array['test'];
unset($array['test']);