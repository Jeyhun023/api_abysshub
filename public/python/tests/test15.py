function keyRename(array $hash, array $replacements) {

    foreach($hash as $k=>$v)
        if($ok=array_search($k,$replacements))
        {
          $hash[$ok]=$v;
          unset($hash[$k]);
        }

    return $hash;       
}