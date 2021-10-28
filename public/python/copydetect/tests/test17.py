function mapToIDs($array, $id_field_name = 'id')
{
    $result = [];
    array_walk($array, 
        function(&$value, $key) use (&$result, $id_field_name)
        {
            $result[$value[$id_field_name]] = $value;
        }
    );
    return $result;
}

$arr = [0 => ['id' => 'one', 'fruit' => 'apple'], 1 => ['id' => 'two', 'fruit' => 'banana']];
print_r($arr);
print_r(mapToIDs($arr));