#!/bin/bash


function copy_fileSet {
    echo "parameter : $1"
    for var in `ls $1`
    do
	CMD="ln -sf $var ${var:${#1}-1}";
	echo "$CMD";
	`$CMD`;
    done
    echo "done";
    exit
}

if [ "$1" = "local" ]
then
    copy_fileSet 'local_*';
fi

if [ "$1" = "prod" ]
then
    copy_fileSet 'prod_*';
fi

echo "usage: setup.sh [prod|local]"
echo "link either the local_* or prod_* into *"
echo "i.e. : if you have a directory containing local_config.php and local_.htaccess, "
echo "after running setup.sh local"
echo "config.php will point to local_config.php and "
echo ".htaccess will poing to local_.htaccess"

