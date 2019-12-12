#! /bin/sh

# running supervisor to process the queue
/usr/bin/supervisord

# running apache process 
exec apache2-foreground