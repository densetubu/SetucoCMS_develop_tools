<?php
require_once 'bootstarap.php';
require_once ConstDirPath::ROOT_PATH() . '/comment_replace.php';


class CommentReplaceTest extends PHPUnit_Framework_TestCase
{
    public function test_isTargetFile_置換対象の拡張子のいずれかだったらtrueを返す()
    {
        $this->assertTrue(isTargetFile(__FILE__, array('php', 'gif')));
    }

    public function test_isTargetFile_置換対象の拡張子のいずれでもなかったらfalseを返す()
    {
        $this->assertFalse(isTargetFile(__FILE__, array('jpeg', 'gif')));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function test_isTargetFile_置換対象リストの引数に文字列を渡したらエラーを発生する()
    {
        $this->assertFalse(isTargetFile(__FILE__, 'gif'));
    }

    public function test_isInvisibleFile_隠しファイルだったらtrueを返す()
    {
        $this->assertTrue(isInvisibleFile(ConstDirPath::FIXTURE_FILE_PATH() . '/.hidden_file'));
    }

    public function test_isInvisibleFile_存在しないファイルだったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->assertTrue(isInvisibleFile(ConstDirPath::FIXTURE_FILE_PATH() . '/not_extists'));
    }

    public function test_isInvisibleFile_通常ファイルだったらfalseを返す()
    {
        $this->assertFalse(isInvisibleFile(__FILE__));
    }

    public function test_getList_ファイルのリストを取得する()
    {
        $targetPath = realpath(ConstDirPath::SCRIPT_PATH() . '/..');

        $expected = array(
            '/Users/suzukimasayuki/project/setuco_tools/script/php/ConstDirPath.php',
        );

        $this->assertEquals($expected, getList($targetPath, array(), array('php')));
    }

    public function test_getList_取得対象拡張子のファイルがなかったら空の配列を返す()
    {
        $this->assertEquals(array(), getList(ConstDirPath::SCRIPT_PATH(), array(), array('css')));
    }


}









