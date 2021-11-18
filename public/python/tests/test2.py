function change_array_key( $array, $old_key, $new_key) {
    if(!is_array($array)){ print 'You must enter a array as a haystack!'; exit; }
    if(!array_key_exists($old_key, $array)){
        return $array;
    }

    $key_pos = array_search($old_key, array_keys($array));
    $arr_before = array_slice($array, 0, $key_pos);
    $arr_after = array_slice($array, $key_pos + 1);
    $arr_renamed = array($new_key => $array[$old_key]);

    return $arr_before + $arr_renamed + $arr_after;
}