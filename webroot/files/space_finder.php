<?php
/***********************
*@author: Ritesh Agrawal
*@description: Identifies php files that contain leading or trailing spaces before or after PHP opening or closings tags
*@version: 1.0
*@date: Nov 06, 2007
@todo - check only *.PHP or *.CTP files rather than checking all of the files
      - html based output
***********************/
//Set Source Path
$sourcepath = "/var/www/c2/yfi_cake";
//Regex Express to test leading and trailing spaces
define("PRE", "#^[\n\r|\n\r|\n|\r|\s]+<\?php#");
define("POST", "#\?>[\n\r|\n\r|\n|\r|\s]+$#");

//Clear the file Status Cache
clearstatcache();

//============ Code borrowed from php.net ===============
// Replace \ by / and remove the final / if any
$root = ereg_replace( "/$", "", ereg_replace( "[\\]", "/", $sourcepath ));
// Touch all the files from the $root directory
if( false === m_walk_dir( $root, "check", true )) {
    echo "‘{$root}’ is not a valid directory\n";
}

// Walk a directory recursively, and apply a callback on each file
function m_walk_dir( $root, $callback, $recursive = true ) {
    $dh = @opendir( $root );
    if( false === $dh ) {
        return false;
    }
    while( $file = readdir( $dh )) {
        if( "." == $file || ".." == $file ){
            continue;
        }
        call_user_func( $callback, "{$root}/{$file}" );
        if( false !== $recursive && is_dir( "{$root}/{$file}" )) {
            m_walk_dir( "{$root}/{$file}", $callback, $recursive );
        }
    }
    closedir( $dh );
    return true;
}
//============== end ======================
//If file, checks whether there is any leading spaces before opening PHP tag or
// trailing spaces after closing PHP tag
function check( $path ) {
   
    if( !is_dir( $path )) {
        $fh = file_get_contents($path);
    if(preg_match(PRE, $fh))
        echo $path. " — contains leading spaces \n";
  if(preg_match(POST, $fh))
        echo $path . " — contains trailing spaces \n";
    }
}

?>