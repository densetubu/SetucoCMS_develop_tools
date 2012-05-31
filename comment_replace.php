<?php

require_once 'bootstrap.php';
require_once 'CommentReplace.php';

$commentReplace = new CommentReplace($argv);

echo $commentReplace->beforeRunningMessage();


if ($commentReplace->inputAccept()) {
    foreach ($commentReplace->getAllList() as $fileName) {
        $commentReplace->rewriteAnnotation($fileName);
        echo "{$fileName} ... 完了\n";
    }
} else {
    echo "アノテーションを書き換えるのをやめます";
}



