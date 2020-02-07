<?php
    $url = "./source.php";
 
    $buff = file_get_contents($url);
 
    $fname = "public/index.html";
    $fhandle = fopen( $fname, "w");
    fwrite( $fhandle, $buff);
    fclose( $fhandle );