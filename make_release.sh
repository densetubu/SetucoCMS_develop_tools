#!/bin/sh

VERSION='0.1'

CMDNAME=$(basename $0)

help() {
    echo
    echo 'SetucoCMSのリリースを作成するスクリプト'
    echo "書式 : $CMDNAME [-b BRANCH] [-l] [-h] VERSION"
    echo
    echo '    -b BRANCH    作成リリースの元ブランチ。デフォルトは master'
    echo '    -h           ヘルプを表示'
    echo '    -l           `git clone`せずに../SetucoCMS/ディレクトリを元ディレクトリとして利用。'
    echo '    -v           このスクリプトのバージョンを表示'
    echo
}

is_numeric() {
    expr "$1" + 1 > /dev/null 2>&1
    if [ $? -lt 2 ]; then
        return 0
    else
        return 1
    fi
}

#オプション処理
FLG_CLONE=1
while getopts hlvb: OPT
do
    case $OPT in
        "b" ) BRANCH="$OPTARG" ;;
        "h" ) help ; exit ;;
        "l" ) FLG_CLONE=0 ;;
        "v" ) echo "バージョン: " $VERSION; exit ;;
    esac
done
shift `expr $OPTIND - 1`

#リリース元ブランチ
if [ -n $BRANCH ]
then
    echo -n ''
else
    BRANCH='master'
fi


if [ -z $1 ]
then
    help

    #TODO: 標準エラー出力
    echo 'error : 引数にリリースするバージョンを指定してください'
    echo '例) $' $0 '1.2.3'
    exit 1
fi

#バージョン指定のチェック
v_major=$(echo $1 | cut -d '.' -f 1); is_numeric $v_major; res_v_major=$?
v_minor=$(echo $1 | cut -d '.' -f 2); is_numeric $v_minor; res_v_minor=$?
v_patch=$(echo $1 | cut -d '.' -f 3); is_numeric $v_patch; res_v_patch=$?
if [ $res_v_major -gt 0 -o $res_v_minor -gt 0 -o $res_v_patch -gt 0 ]
then
    help

    #TODO: 標準エラー出力
    echo 'error : 書式が不正です'
    echo '引数にリリースするバージョンを指定してください'
    echo '例) $' $0 '1.2.3'
    exit 2
fi


## リリース作成 ここから
echo 'バージョン' $v_major.$v_minor.$v_patch 'のリリースを作成します'
echo

if [ $FLG_CLONE -gt 0 ]
then
    echo 'リモートのmasterブランチからリリースを作成します。'
    #ディレクトリチェック
    if [ -f 'SetucoCMS' -o -d 'SetucoCMS' ]
    then
        #TODO: 標準エラー出力
        echo 'ファイルかディレクトリが既に存在します'
        exit 3
    fi

    git clone http://github.com/densetubu/SetucoCMS.git
    cd SetucoCMS
else
    if [ -d '../SetucoCMS' ]
    then
        cd ../SetucoCMS
        echo `pwd` ’ディレクトリを元にリリースを作成します’
    else
        cd ../
        echo "`pwd`/SetucoCMS/ ディレクトリが存在しません。"
        exit 4
    fi
fi

git checkout $BRANCH
if [ $? -gt 0 ]
then
    #TODO: 標準エラー出力
    echo '存在しないブランチです : ' $BRANCH
    exit 5
fi
git checkout -b $v_major.$v_minor
#git push origin $v_major.$v_minor
git tag v$v_major.$v_minor.$v_patch
#git push origin v$v_major.$v_minor.$v_patch
chmod 777 public/ public/media/ application/configs/
sed -i '' -e 's/SetEnv APPLICATION_ENV development/SetEnv APPLICATION_ENV production/' public/.htaccess.sample

#MACだと.htaccess-eファイルが作成されてしまうので
if [ -f 'public/.htaccess.sample-e' ]
then
    \rm -f 'public/.htaccess.sample-e'
fi

cd ../

echo '圧縮中....'
tar pzcvf "SetucoCMS_$v_major.$v_minor.$v_patch.tar.gz" \
        --exclude=".git" \
        --exclude=".gitignore" \
        --exclude="tests" \
        SetucoCMS > /dev/null


#echo '作業ディレクトリを削除'
#rm -rf SetucoCMS/


echo '完了!'
