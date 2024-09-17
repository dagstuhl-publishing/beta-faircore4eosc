<?php

namespace App\Modules;

abstract class Utils
{
    public static function getLanguageList()
    {
        return require __DIR__."/languages.php";
    }

    public static function formatFileSize(int $size): string
    {
        $suffixes = [ "B", "KiB", "MiB", "GiB", "TiB", "PiB", "EiB" ];
        $i = 0;
        $divisor = 1;
        while($divisor <= $size / 1024) {
            $i++;
            $divisor *= 1024;
        }
        return ($i == 0 ? $size : sprintf("%.2f", $size / $divisor))." ".$suffixes[$i];
    }
}
