#!/bin/bash

# Author: Rex
# For initialize libsso

function log {
	case $1 in
	'info')
		echo -e '\033[32m[info]\033[0m '$2
		;;
	'warning')
		echo -e '\033[33m[warning]\033[0m '$2
		;;
	'error')
		echo -e '\033[31m[error]\033[0m '$2
		;;
	'input')
		echo -e '\033[35m[input]\033[0m '$2
		echo -n '> '
		;;
	esac
}

log info '从 sso 获取公钥与私钥……'
if [ -d cert ]; then
	rm -rf cert
fi
mkdir cert && cd cert
sso_path='/data/www/sso'
while [ ! -f $sso_path/DO_NOT_REMOVE.sso.flag ]; do
	log warning $sso_path' 不是正确的 sso 的路径，请手动输入。'
	log input 'Windows 环境下请输入"/盘符/路径/sso"，例如"/e/zfj/sso"。'
	read sso_path
done
log info '确定 sso 路径为：'$sso_path
if [ ! -d $sso_path/cert ]; then
	log error '目录 '$sso_path/cert' 不存在！无法继续执行脚本。'
	exit
fi
cp $sso_path/cert/public_key.pem public_key.pem
cp $sso_path/cert/private_key.pem private_key.pem
cp $sso_path/cert/js_public_key.dat js_public_key.dat
log info '复制完成。'

cd ..

log info '检查配置文件是否就绪……'
for file in 'config.php' ; do
	if [ ! -f $file ]; then
		log warning '未找到 '$file' 文件。'
		flag=1
	fi
done
if [ '$flag' == '1' ]; then
	log error '配置文件未就绪，请手动设置！'
else
	log info '配置文件已就绪，可以正常使用了。'
fi
