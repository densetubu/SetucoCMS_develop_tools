<?php
require_once 'bootstrap.php';
require_once 'CommentReplace.php';


class CommentReplaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CommentReplace
     */
    private $_currentCommentReplace;

    /**
     * @var CommentReplace
     */
    private $_fixtureCommentReplace;

    private function _baseParams()
    {
        return array(
            'hoge.php',
            'target',
            'after',
        );
    }

    public function setup()
    {
        $currentParams = array_merge($this->_baseParams(), array(__FILE__));
        $this->_currentCommentReplace = new CommentReplace($currentParams);

        $fixtureParams = array_merge($this->_baseParams(), array(ConstDirPath::FIXTURE_FILE_PATH()));
        $this->_fixtureCommentReplace = new CommentReplace($fixtureParams);
    }

    
    public function test_isTargetFile_置換対象の拡張子のいずれかだったらtrueを返す()
    {

        $this->assertTrue($this->_currentCommentReplace->isTargetFile(__FILE__, array('php', 'gif')));
    }

    public function test_isTargetFile_置換対象の拡張子のいずれでもなかったらfalseを返す()
    {
        $this->assertFalse($this->_currentCommentReplace->isTargetFile(__FILE__, array('jpeg', 'gif')));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function test_isTargetFile_置換対象リストの引数に文字列を渡したらエラーを発生する()
    {
        $this->assertFalse($this->_currentCommentReplace->isTargetFile(__FILE__, 'gif'));
    }

    public function test_isInvisibleFile_隠しファイルだったらtrueを返す()
    {
        $this->assertTrue($this->_currentCommentReplace->isInvisibleFile(ConstDirPath::FIXTURE_FILE_PATH() . '/.hidden_file'));
    }

    public function test_isInvisibleFile_存在しないファイルだったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->assertTrue($this->_fixtureCommentReplace->isInvisibleFile(ConstDirPath::FIXTURE_FILE_PATH() . '/not_extists'));
    }

    public function test_isInvisibleFile_通常ファイルだったらfalseを返す()
    {
        $this->assertFalse($this->_currentCommentReplace->isInvisibleFile(__FILE__));
    }

    public function test_getList_ファイルのリストを取得する()
    {
        $targetPath = realpath(ConstDirPath::SCRIPT_PATH() . '/..');

        $basePattern    = ConstDirPath::SCRIPT_PATH() . '/*.php';
        $expected       = array();
        foreach(glob($basePattern) as $fileName) {
            $expected[] = $fileName;
        }

        $this->assertEquals($expected, $this->_currentCommentReplace->getList($targetPath, array(), array('php')));
    }

    public function test_getList_取得対象拡張子のファイルがなかったら空の配列を返す()
    {
        $this->assertEquals(array(), $this->_fixtureCommentReplace->getList(ConstDirPath::SCRIPT_PATH(), array(), array('css')));
    }

    public function test_isPrintUsage_パラメーター配列が4要素未満の場合はTrueを返す()
    {
        $commentReplace = new CommentReplace($this->_baseParams());
        $this->assertTrue($commentReplace->isPrintUsage());
    }

    public function test_isPrintUsage_パラメーター配列が4要素以上の場合はFalseを返す()
    {
        $this->assertFalse($this->_currentCommentReplace->isPrintUsage());
    }

    public function test_getAllList_パラメーターに指定したすべてのファイルを取得する()
    {
        $params = array_merge($this->_baseParams(), array(__FILE__, ConstDirPath::SCRIPT_PATH()));
        $commentReplace = new CommentReplace($params);

        $expected = array(
            ConstDirPath::TEST_PATH() . '/CommentReplaceTest.php',
            ConstDirPath::SCRIPT_PATH() . '/CommentReplace.php',
            ConstDirPath::SCRIPT_PATH() . '/ConstDirPath.php',
        );

        $this->assertEquals($expected, $commentReplace->getAllList());
    }

    public function test_beforeRunningMessage_実行前メッセージを取得する()
    {
        $this->assertTrue(is_string($this->_currentCommentReplace->beforeRunningMessage()));
    }

    public function test_rewriteAnnotation_アノテーションを置換する()
    {
        $copyPath       = ConstDirPath::FIXTURE_FILE_PATH() . '/comment_replace_target_before.php';
        $targetPath     = ConstDirPath::FIXTURE_FILE_PATH() . '/comment_replace_target.php';
        $expectedPath   = ConstDirPath::FIXTURE_FILE_PATH() . '/comment_replace_target_after.php';

        copy($copyPath, $targetPath);

        $this->_currentCommentReplace->rewriteAnnotation($targetPath);
        $this->assertFileEquals($expectedPath, $targetPath);
    }


}








