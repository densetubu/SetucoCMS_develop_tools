<?php
/**
 * toolsのディレクトリ構造を定義しています
 *
 *
 *
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @author suzuki-mar
 */
class ConstDirPath
{
    public static function ROOT_PATH()
    {
        return ROOT_PATH;
    }

    public static function SCRIPT_PATH()
    {
        return self::ROOT_PATH() . '/lib/php';
    }

    public static function TEST_PATH()
    {
        return self::ROOT_PATH() . '/tests/php';
    }

    public static function FIXTURE_PATH()
    {
        return self::TEST_PATH() . "/fixture";
    }

    public static function FIXTURE_FILE_PATH()
    {
        return self::FIXTURE_PATH() . '/file';
    }
}



