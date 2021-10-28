/**
 * Helper function to rename array keys.
 */
function _rename_arr_key($oldkey, $newkey, array &$arr) {
    if (array_key_exists($oldkey, $arr)) {
        $arr[$newkey] = $arr[$oldkey];
        unset($arr[$oldkey]);
        return TRUE;
    } else {
        return FALSE;
    }
}