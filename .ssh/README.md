To be able to push on thumb repository you should run
```
ssh-keygen -t rsa -b 4096 -C "sftpThumb from micromania" -f SFTPthumb -P ""
```

then 
```
ssh-copy-id -i SFTPthumb.pub kim1
```

and finally give rights to apache user on this file
```
please chown www-data:$USER .ssh/SFTPthumb*
```

