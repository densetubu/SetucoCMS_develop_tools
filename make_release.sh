#!/bin/sh

VERSION='0.1'

CMDNAME=$(basename $0)

help() {
    echo
    echo 'SetucoCMSのリリースを作成するスクリプト'
    echo "書式 : $CMDNAME [-b BRANCH] [-d DIRECTORY] [-h] [-v] VERSION"
    echo
    echo '    -b BRANCH    作成リリースの元ブランチ。デフォルトは master'
    echo '    -h           ヘルプを表示'
    echo '    -d DIRECTORY `git clone`せずにDIRECTORY/を作業ディレクトリとして利用する'
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
while getopts hvb:d: OPT
do
    case $OPT in
        "b" ) BRANCH="$OPTARG" ;;
        "h" ) help ; exit ;;
        "d" ) FLG_CLONE=0; DIR=$OPTARG ;;
        "v" ) echo "バージョン: " $VERSION; exit ;;
    esac
done
shift `expr $OPTIND - 1`

#リリース元ブランチ
if [ -z $BRANCH ]
then
    BRANCH='master'
fi


if [ -z $1 ]
then
    help

    #TODO: 標準エラー出力
    echo 'error : リリースするバージョンを指定してください'
    echo '例) $' $CMDNAME '1.2.3'
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
    echo 'リリースするバージョンを指定してください'
    echo '例) $' $CMDNAME '1.2.3'
    exit 2
fi


## リリース作成 ここから
echo 'バージョン' $v_major.$v_minor.$v_patch 'のリリースを作成します'
echo

if [ $FLG_CLONE -gt 0 ]
then
    echo 'リモートの' $BRANCH 'ブランチからリリースを作成します'
    DIR='SetucoCMS'
    #ディレクトリチェック
    if [ -f $DIR -o -d $DIR ]
    then
        #TODO: 標準エラー出力
        echo 'ファイルかディレクトリが既に存在します : SetucoCMS/'
        exit 3
    fi

    git clone http://github.com/densetubu/SetucoCMS.git
    cd SetucoCMS
else
    if [ -d $DIR ]
    then
        cd $DIR
        echo $DIR ’ディレクトリの' $BRANCH 'ブランチからリリースを作成します’
    else
        echo 'ディレクトリが存在しません :' $DIR
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
git tag v$v_major.$v_minor.$v_patch
chmod 777 public/ public/media/ application/configs/
sed -i'' -e 's/SetEnv APPLICATION_ENV development/SetEnv APPLICATION_ENV production/' public/.htaccess.sample

#MACだと.htaccess-eファイルが作成されてしまうので
\rm -f 'public/.htaccess.sample-e'

cd ../

echo '圧縮中....'
tar pzcvf "SetucoCMS_$v_major.$v_minor.$v_patch.tar.gz" \
        --exclude=".git" \
        --exclude=".gitignore" \
        --exclude="tests" \
        $DIR > /dev/null


echo 'リリース作成完了!'
echo
