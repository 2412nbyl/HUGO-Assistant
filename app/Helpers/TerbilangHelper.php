<?php

namespace App\Helpers;

class TerbilangHelper
{
    public static function convert($number)
    {
        $number = abs($number);
        $words = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $result = "";

        if ($number < 12) {
            $result = " " . $words[$number];
        } elseif ($number < 20) {
            $result = self::convert($number - 10) . " Belas";
        } elseif ($number < 100) {
            $result = self::convert((int)($number / 10)) . " Puluh" . self::convert($number % 10);
        } elseif ($number < 200) {
            $result = " Seratus" . self::convert($number - 100);
        } elseif ($number < 1000) {
            $result = self::convert((int)($number / 100)) . " Ratus" . self::convert($number % 100);
        } elseif ($number < 2000) {
            $result = " Seribu" . self::convert($number - 1000);
        } elseif ($number < 1000000) {
            $result = self::convert((int)($number / 1000)) . " Ribu" . self::convert($number % 1000);
        } elseif ($number < 1000000000) {
            $result = self::convert((int)($number / 1000000)) . " Juta" . self::convert($number % 1000000);
        } elseif ($number < 1000000000000) {
            $result = self::convert((int)($number / 1000000000)) . " Miliar" . self::convert($number % 1000000000);
        }

        return trim($result);
    }

    public static function formatRupiah($number)
    {
        if (!$number) return "";
        $spell = self::convert($number);
        return number_format($number, 0, ',', '.') . " (" . $spell . " Rupiah)";
    }
}
