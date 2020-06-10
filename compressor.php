#!/usr/bin/php
<?php
// Max Base - MaxBase.org
// https://github.com/BaseMax/MiniPHPArchiveCompressor
/*
Tested on: Linux base 5.3.0-40-generic
Run: $ php mini.php
Run: $ ./mini.php
Using: $ ./mini c input.txt output.x
       $ ./mini d output.x input.txt
max@base:~/compress$ ./mini.php c i.txt o.txt
        Mode: c, Level: 9
        Input File: "i.txt"
        Output File: "o.txt"
        Input file size: 4275
        Total input bytes: 5
        Total output bytes: 2107
        Done.
max@base:~/compress$ ./mini.php d o.txt  oi.txt
        Mode: d, Level: 9
        Input File: "o.txt"
        Output File: "oi.txt"
        Input file size: 2107
        Total input bytes: 5
        Total output bytes: 4275
        Done.
*/
function compress($string="", $level=9) {
        $compressed=c($string, $level);
        $compressed=c($compressed, $level);
        return $compressed;
}
function decompress($string="", $level=9) {
        $decompressed=d($string);
        $decompressed=d($decompressed);
        return $decompressed;
}
function starts($haystack, $needle) {
        $length = strlen($needle);
        return(substr($haystack, 0, $length) === $needle);
}
function ends($haystack, $needle) {
        $length = strlen($needle);
        if($length === 0) {
                return true;
        }
        return(substr($haystack, -$length) === $needle);
}
function d($string="") {return gzinflate($string);}
function c($string="", $level=9) {return gzdeflate($string, $level);}
if($argc < 4) {
        printf("mini (June 4th 2020).\n");
        printf("Usage: mini [options] [mode:c or d] inputFile outputFile\n");
        printf("\nModes:\n");
        printf("c - Compresses file inputFile to a stream in file outputFile\n");
        printf("d - Decompress stream in file inputFile to file outputFile\n");
        printf("\nOptions:\n");
        printf("-l[1-9] - Compression level, higher values are slower.\n");
        printf("\nMax Base - MaxBase.org\n");
        exit(1);
}
$i=1;
$level=9;
if(starts($argv[$i], "-l")) {
        $level=mb_substr($argv[$i++], 2);
        $level=trim($level);
        if($level !== "" and is_numeric($level)) {
                $level=(int)$l;
                if(($level < 0) || ($level > 10)) {
                        printf("Invalid level!\n");
                        exit(1);
                }
        }
        else {
                printf("Invalid level format!\n");
                exit(1);
        }
}
$mode=$argv[$i++];
if(($mode === "c") || ($mode === "C")) {
        $mode=1;
}
else if(($mode === "d") || ($mode === "D")) {
        $mode=2;
}
else {
        printf("Invalid mode!\n");
        exit(1);
}
$input=$argv[$i++];
if(!file_exists($input)) {
        printf("Failed opening input file!\n");
        exit(1);
}
if(!is_file($input)) {
        printf("Input file is not a file!\n");
        exit(1);
}
$string=file_get_contents($input);
$output=$argv[$i++];
if(file_exists($output) and !is_writable($output)) {
        printf("Cannot write output file!\n");
        exit(1);
}
printf("Mode: %s, Level: %d\n", $mode === 1 ? "c" : "d", $level);
printf("Input File: \"%s\"\n", $input);
printf("Output File: \"%s\"\n", $output);
$inputSize=filesize($input);
printf("Input file size: %d\n", $inputSize);
$inputBytes=strlen($input);
printf("Total input bytes: %d\n", $inputBytes);
if($mode === 1) {
        $data=compress($string, $level);
}
else if($mode === 2) {
        // print $string."\n";
        $data=decompress($string, $level);
}
$outputBytes=strlen($data);
printf("Total output bytes: %d\n", $outputBytes);
file_put_contents($output, $data);
printf("Done.\n");
