<?php

/**
 * ソースコードのドキュメントコメントで、指定したアノテーションの
 * 文言を変更するPHPスクリプトです。
 * 引数なしで実行すると、使い方を表示します。
 *
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @author  suzuki-mar
 */
class CommentReplace
{
    /**
     * 引数の最低個数（1の場合は実行ファイル名のみ）
     */
    const PARAM_MIN_COUNT = 4;

    /**
     * 実行時引数のうち、ファイル名もしくはディレクトリ名の位置
     */
    const PATH_POSITION_BEGIN = 3;

    private $_targetFiles = array();

    /**
     * 置換対象の拡張子
     */
    public static function TARGET_FILE_EXTS()
    {
        return array('.php', '.phtml', '.js', '.css');
    }

    public function __construct(array $params)
    {
        for ($i = self::PATH_POSITION_BEGIN; $i < count($params); $i++) {
            $this->_targetFiles[] = $params[$i];
        }

    }

    /**
     * 使い方を表示するか
     * 
     * @return boolean 使い方を表示するか
     * @author suzuki-mar
     */
    public function isPrintUsage($paramter)
    {
        return ($paramter < CommentReplace::PARAM_MIN_COUNT);
    }

    /**
     * パラメーターに指定したファイル(ディレクトリ)のすべてのファイル名を取得する
     *
     * ディレクトリ名を指定した場合は、ディレクトリにあるファイルを再帰的に取得する
     *
     * @return array ファイル名の配列
     * @author suzuki-mar
     */
    public function getAllList()
    {
        $fileNames = array();
        foreach ($this->_targetFiles as $fileName) {
            $this->getList($fileName, &$fileNames, self::TARGET_FILE_EXTS());
        }

        return $fileNames;
    }

    /**
     * ディレクトリパス内のファイルのリストを取得します。
     * . もしくは .. は除外します。
     *
     * @param string $dirPath ディレクトリパス
     * @author charlesvineyard suzuki-mar
     */
    public function getList($dirPath, $filenames, $targetFileExts)
    {
        if (is_file($dirPath)) {
            if (!self::isInvisibleFile($dirPath) && self::isTargetFile($dirPath, $targetFileExts)) {
                $filenames[] = $dirPath;
            }
            return $filenames;
        }

        // 最後に区切り文字があれば削除
        if (strrpos($dirPath, DIRECTORY_SEPARATOR) === (strlen($dirPath) - 1)) {
            $dirPath = substr_replace($dirPath, '', -1, 1);
        }

        $dir = dir($dirPath);
        while ($path = $dir->read()) {
            if ($path === "." || $path === "..") {
                continue;
            }
            $longPath = $dirPath . DIRECTORY_SEPARATOR . $path;
            self::getList($longPath, &$filenames, $targetFileExts);
        }
        return $filenames;
    }

    /**
     * 隠しファイルかを判断します。
     *
     * @param  string $path ファイルパス
     * @return 隠しファイルなら true
     * @throws 存在しないファイルを渡したら例外が発生する
     * @author charlesvineyard suzuki-mar
     */
    public function isInvisibleFile($path)
    {

        if (!file_exists($path)) {
            throw new InvalidArgumentException("{$path}というファイルは存在しません");
        }


        $filename = $path;
        if (strpos($filename, DIRECTORY_SEPARATOR) !== false) {
            $filename = substr($filename,
                            strrpos($filename, DIRECTORY_SEPARATOR) + 1);
        }
        return (strpos($filename, '.') === 0);
    }

    /**
     * 置換対象のファイル拡張子かを判断します。
     *
     * @param string $path           ファイルパス
     * @param array  $targetFileExts 置換対象の拡張子の配列
     * @author charlesvineyard suzuki-mar
     */
    public function isTargetFile($path, array $targetFileExts)
    {
        foreach ($targetFileExts as $ext) {
            if (strrpos($path, $ext) === strlen($path) - strlen($ext)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 当スクリプトの使い方を表示します。
     *
     * @return void
     * @author charlesvineyard suzuki-mar
     */
    public function printUsage()
    {
        // $argv[0] means this script file.
        // $argv[1] --args is passing after arguments to script.
        echo "usage:\n"
        . "\tphp " . basename(__FILE__) . " {annotation} {words} {file or directory list}\n"
        . "example:\n"
        . "\tindex.php ファイルの @license の内容を\"新しい内容文\"に置換するときの例\n"
        . "\tphp " . basename(__FILE__) . " license 新しい内容文 index.php\n"
        . "{annotation}\n"
        . "\t置換対象の、@無しのアノテーションを指定します。\n"
        . "{words}\n"
        . "\t置換する内容文を指定します。スペースを含む場合は\"\"で囲ってください。\n"
        . "{file or directory list}\n"
        . "\t置換するファイル名またはディレクトリ名を指定します。\n"
        . "\tファイルはスペース区切りで複数指定可能です。\n"
        . "\tディレクトリを指定すると再帰的に置換します。\n"
        . "\t隠しファイルは含まれません。\n";
    }

}
