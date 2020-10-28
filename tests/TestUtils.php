<?php

namespace Tests;

trait TestUtils
{
    public static function loadJson($path)
    {
        return json_decode(self::getFileContents($path), true);
    }

    private static function getFileContents($path)
    {
        return file_get_contents(base_path($path));
    }
}
