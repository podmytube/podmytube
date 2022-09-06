# PODMYTUBE.COM

## Purpose
The ultimate goal of the podmytube service is to simplify the life of a youtuber by allowing him to transform his youtube channel into an audio podcast, and to host it.

## Context
### Side project
I started to work on this project during december 2016. 
I have continued to work on this project as a side project since then.
I get my first users during february 2017. At this time everything was free and I was displaying a podmytube icon on the feed.
I expected to get some virality through this. It fails.
During the second half of 2017 I transformed the service to allow customers to display their logo on the feed and, gradually, I added several limitations to the free version. 

### Revenues
Here are my revenues during years.
2017 : -35€ (yes I was losing money)
2018 : -391€ (yes I was STILL losing money)
2019 : 102€ 
2020 : 900€
2021 : 1200€
2022 : TBC

### How does this service work ?
The application is monolithic. It is a service built on the laravel framework that manages the whole.
This service uses 3 types of servers.
- a display server (the web server accessible on www.podmytube.com)
- a processing server. It recovers the videos, transforms them into mp3 files and then deposits them on the hosting server
- the hosting server only distributes the static files (audio and xml feed)

### Bare-metal is cost effective
The 3 types of servers are of the "bare-metal" type. 
Why not on the cloud ? 
On the cloud, hosting small files is not expensive, neither in hosting nor in bandwidth.
On the other hand as soon as it is a question of hosting/distribute large audio files through all Internet, the costs become quickly very important.
The cost in bandwidth having quickly exceeded the gains I came back on a bare metal architecture. 
The infrastructure is currently hosted by OVH for its unbeatable price/quality ratio.


## Requirements
You need to have some development skills to be able to understand/manage/install this service. 
It's not hard but its legacy is quite ... interesting.

### Tools
This service uses several tools for its development.

- PHP - main language development
- Laravel - the php development framework
- mysql - well known database
- docker - virtualisation tool

### Others
- SSH - to securely transfer files between podmytube servers.
- supervisor - to handle queue worker
- nginx-proxy by jwilder (https://github.com/nginx-proxy/nginx-proxy) - reverse proxy
- yt-dlp by yt-dlp (https://github.com/yt-dlp/yt-dlp) - youtube video downloader
- mailgun - mail sender
- mailhog - mailcatcher on local environment (to avoid sending mail to real customers)


## Installation summary (to be refreshed)
1. Install nginx-proxy
1. Install YT-DLP
1. git clone podmytube
1. install supervisor


