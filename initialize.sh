#!/bin/bash

# Author: Rex
# For initialize sso

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

function generate_key {
	if [ -d cert ]; then
		log info '跳过证书生成步骤。'
		return
	fi
	mkdir cert && cd cert
	log info '生成本应用证书……'
	openssl genrsa -out private_key.pem 2048 2> /dev/null
	openssl rsa -in private_key.pem -pubout -out public_key.pem 2> /dev/null
	openssl rsa -pubin -inform PEM -modulus -noout < public_key.pem > js_public_key.dat 2> /dev/null
	sed 's/Modulus=//g' js_public_key.dat > .tmp_js_public_key.dat
	mv .tmp_js_public_key.dat js_public_key.dat
	log info '证书生成完成。'
	log warning '其它应用需要重新初始化。'
}

if [ -d cert ]; then
	log input '证书目录已存在，确定重新生成？（y/N）'
	read cmd
	if [[ $cmd == 'Y' || $cmd == 'y' ]]; then
		rm -rf cert
	fi
fi
generate_key

log info '导入数据库数据……'
php db_migrate.php
log info '导入完成。'

log info '程序初始化完成，可以正常使用了。'
