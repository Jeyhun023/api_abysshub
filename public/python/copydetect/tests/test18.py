<?php
$arr = [
    'foo',
    'bar'=>'alfa',
    'baz'=>['a'=>'hello', 'b'=>'world'],
];

foreach($arr as $k=>$v) {
    $kk = is_numeric($k) ? $v : $k;
    $vv = is_numeric($k) ? null : $v;
    $arr2[$kk] = $vv;
}

print_r($arr2);
function renameArrKey($arr, $oldKey, $newKey){
    if(!isset($arr[$oldKey])) return $arr; // Failsafe
    $keys = array_keys($arr);
    $keys[array_search($oldKey, $keys)] = $newKey;
    $newArr = array_combine($keys, $arr);
    return $newArr;
          $arr[$newKey] = $arr[$origKey];
        unset(
for($i=0;$i<count($keys);$i++) {
    $keys_array[$keys_array[$i]]=$keys_array[$i];
    unset($keys_array[$i]);
}set( $pendingKeys[$origKey] );
        }
    } elseif( $newKey != $origKey ) {
        $pendingKeys[$newKey] = $origKey;
    }
}