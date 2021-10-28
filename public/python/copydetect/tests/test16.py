$i = 0;
$keys_array=array("0"=>"one","1"=>"two");

$keys = array_keys($keys_array);

for($i=0;$i<count($keys);$i++) {
    $keys_array[$keys_array[$i]]=$keys_array[$i];
    unset($keys_array[$i]);
}
print_r($keys_array);