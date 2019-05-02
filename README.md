# dashboard.podmytube.com

## purpose
Dashboard interface for podmytube


## installation summary 
1. `git clone`
1. `cat .env-sample > .env` and set proper value
1. `docker-compose up -d` (`dokup`)
1. create databases
1. grant access on databases (normal & tests)
1. `docker exec -it dash php artisan db:seed` (`dokexec dash php artisan db:seed`)


## test everything is ok

1. `docker exec -it dash phpunit` (`dokexec dash phpunit`)


