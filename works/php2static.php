<?php
// script by mashpote.net 
    mb_internal_encoding("UTF-8");
 
    $convertefiles = array();
 
    if ($argc != 2)
    {
        echo "パラメータ不正のため終了します\n";
        exit(-1);
    }
     
    $target = $argv[1];
 
    $arraydist = array();
    outputStatic($target, $arraydist);
     
    $outdir = basename($target);
    convertInnerPath($outdir, $convertefiles);
 
    echo "完了！";
    exit(0);
 
    function outputStatic($target, $arraydist)
    {
        global $convertefiles;
        $abpath   = realpath($target);
        $filename = basename($abpath);
        $outputpath = toPath($arraydist, $filename);
        if (file_exists($outputpath))
        {
            echo "出力先にファイルが存在するためエラー終了します : " . $outputpath . "\n";
            exit(-1);
        }
         
        if (is_dir($abpath))
        {
            echo "Copied : " . toPath($arraydist, $filename) . "\n";
            mkdir($outputpath);
            array_push($arraydist, $filename);
             
            $handle = opendir($abpath) or exit('NG');
            while ($fname = readdir($handle))
            {
                if ($fname == "." || $fname == "..")
                {
                    continue;
                }
                outputStatic($abpath . DIRECTORY_SEPARATOR . $fname, $arraydist);
            }
            array_pop($arraydist);
            return;
        }
        $sp = explode('.', $filename);
        if (count($sp) < 2)
        {
            echo "Copied : " . $outputpath . "\n";
            copy ($abpath, $outputpath);
            $tm = filemtime($abpath);
            touch($outputpath, $tm);
             
            return;
        }
        $ext = $sp[1];
        if ($ext != "php")
        {
            echo "Copied : " . $outputpath . "\n";
            copy ($abpath, $outputpath);
            $tm = filemtime($abpath);
            touch($outputpath, $tm);
             
            return;
        }
        {
            $outname = str_replace(".php", ".html", $filename);
            echo "Changed: " . toPath($arraydist, $outname) . "\n";
            $out1path = toPath($arraydist, $outname);
            exec('php "' . $abpath . '" > "' . $out1path . '"');
            $tm = filemtime($abpath);
            touch($out1path, $tm);
            array_push($convertefiles, $filename);
        }
    }
    function convertInnerPath($target, $convertefiles)
    {
        $abpath   = realpath($target);
        if (is_dir($abpath))
        {
            $handle = opendir($abpath) or exit('NG');
            while ($fname = readdir($handle))
            {
                if ($fname == "." || $fname == "..")
                {
                    continue;
                }
                convertInnerPath($abpath . DIRECTORY_SEPARATOR . $fname, $convertefiles);
            }
            return;
        }
        $converted = false;
        $str = file_get_contents($abpath);
        foreach ($convertefiles as $tgtpath)
        {
            $strv = str_replace($tgtpath, str_replace(".php", ".html", $tgtpath), $str);
            if ($strv != $str)
            {
                $converted = true;
                $str = $strv;
            }
        }
         
        if ($converted)
        {
            echo "Convert:" . $abpath . "\n";
            $tm = filemtime($abpath);
            file_put_contents($abpath, $str);
            touch($abpath, $tm);
        }
    }
    function toPath($arraydist, $outname = null)
    {
        $path = implode(DIRECTORY_SEPARATOR, $arraydist);
        if (isset($outname)) {
            if (strlen($path) > 0) {
                return $path . DIRECTORY_SEPARATOR . $outname;
            }
            else {
                return $outname;
            }
        }
        else {
            return $path;
        }
    }
?>