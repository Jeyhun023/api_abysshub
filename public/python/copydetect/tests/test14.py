$myArray = array( '1970-01-01 00:00:01', '1970-01-01 00:01:00' );
$pendingKeys = array();
foreach( $myArray as $key => $myArrayValue ) {
    // NOTE: strtotime( '1970-01-01 00:00:01' ) = 1 (a conflicting key)
    $timestamp = strtotime( $myArrayValue );
    swapKeys( $myArray, $key, $timestamp, $pendingKeys );
}
// RESULT: $myArray == array( 1=>'1970-01-01 00:00:01', 60=>'1970-01-01 00:01:00' )