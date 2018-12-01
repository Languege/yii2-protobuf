#!/usr/bin/env bash

git pull

PATH_PHP='../'

rm -rf ${PATH_PHP}"/PbApp"
rm -rf ${PATH_PHP}"/GPBMetadata"

for file in $(ls ./); do
	if [ "${file##*.}" == "proto" ]; then
		echo $file

		#生成php类
		protoc --proto_path='./' --php_out=$PATH_PHP $file

		if [ $? -ne 0 ]; then
        	echo $file" generate php proto fail"
        	exit 1
        fi

        #生成php消息定义
		php parserMeta.php $file

		if [ $? -ne 0 ]; then
        	echo $file" generate php proto define fail"
        	exit 1
        fi
	fi
done