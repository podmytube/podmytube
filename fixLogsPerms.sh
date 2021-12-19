#!/usr/bin/zsh

pathToFix='storage/logs'
for file in $(ls $pathToFix); do
    filePath=$pathToFix/$file
    filePerms=$(sudo stat -c '%U:%G' $filePath)

    if [[ ! $filePerms = 'www-data:fred' ]]; then
        sudo chown www-data:fred $filePath
        sudo chmod g+w $filePath
    fi

done
