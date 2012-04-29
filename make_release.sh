#!/bin/sh

# SetucoCMSのリリースを作成するスクリプト
# 引数に作成するバージョンを指定するとリリース用の圧縮ファイルとgitのタグが生成される
# 
# 例)
# $ sh make_release.sh 1.3.0
#

is_numeric() {
    expr "$1" + 1 > /dev/null 2>&1
    if [ $? -lt 2 ]; then
        return 0
    else
        return 1
    fi
}

if [ -z $1 ]
then
    echo '引数にリリースするバージョンを指定してください'
    echo '実行例) $' $0 '1.2.3'
    exit 1
fi

v_major=$(echo $1 | cut -d '.' -f 1)
v_minor=$(echo $1 | cut -d '.' -f 2)
v_patch=$(echo $1 | cut -d '.' -f 3)

is_numeric $v_major; res_v_major=$?
is_numeric $v_minor; res_v_minor=$?
is_numeric $v_patch; res_v_patch=$?

if [ $res_v_major -gt 0 -o $res_v_minor -gt 0 -o $res_v_patch -gt 0 ]
then
    echo '書式が不正です'
    echo '引数にリリースするバージョンを指定してください'
    echo '実行例) $' $0 '1.2.3'
    exit 1
fi

echo 'バージョン' $v_major.$v_minor.$v_patch 'のリリースを作成します'
echo

git clone git@github.com:densetubu/SetucoCMS.git SetucoCMS

cd SetucoCMS
git checkout -b $v_major.$v_minor
#git push origin $v_major.$v_minor
git tag v$v_major.$v_minor.$v_patch
#git push origin v$v_major.$v_minor.$v_patch
chmod 777 public/ public/media/ application/configs/
sed -e 's/SetEnv APPLICATION_ENV development/SetEnv APPLICATION_ENV production/' public/.htaccess.sample > public/.htaccess.sample
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
