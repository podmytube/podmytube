To be able to push on thumb repository you should run

```
ssh-keygen -t rsa -b 4096 -C "kimUpload from msi-laptop" -f .ssh/kimUpload -P ""
```

then

```
ssh-copy-id -i .ssh/kimUpload.pub kim1
```

and finally give rights to apache user on this file

```
sudo chown www-data:$USER .ssh/kimUpload*
sudo chmod ug+rw .ssh/kimUpload*
```
