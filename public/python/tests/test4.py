$array = [
    'old1' => 1
    'old2' => 2
];

$renameMap = [
    'old1' => 'new1',   
    'old2' => 'new2'
];

$array = array_combine(array_map(function($el) use ($renameMap) {
    return $renameMap[$el];
}, array_keys($array)), array_values($array));

/*
$array = [
    'new1' => 1
    'new2' => 2
];
*/