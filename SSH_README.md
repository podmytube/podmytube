To be able to push on thumb repository you should run

```
ssh-keygen -m PEM -t rsa -b 4096 -C "kimUpload from $(uname -n)" -f .ssh/kimUpload -P ""
```

then

```
ssh-copy-id -i .ssh/kimUpload.pub <HOST>
```

and finally give rights to ~~apache~~ user on this file

```
sudo chown -R $USER:$USER .ssh && sudo chmod -R 700 .ssh
```
