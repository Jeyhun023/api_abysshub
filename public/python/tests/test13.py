function swapKeys( &$arr, $origKey, $newKey, &$pendingKeys ) {
    if( !isset( $arr[$newKey] ) ) {
        $arr[$newKey] = $arr[$origKey];
        unset( $arr[$origKey] );
        if( isset( $pendingKeys[$origKey] ) ) {
            // recursion to handle conflicting keys with conflicting keys
            swapKeys( $arr, $pendingKeys[$origKey], $origKey, $pendingKeys );
            unset( $pendingKeys[$origKey] );
        }
    } elseif( $newKey != $origKey ) {
        $pendingKeys[$newKey] = $origKey;
    }
}