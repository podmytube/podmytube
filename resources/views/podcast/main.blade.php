<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
    {!! $podcast->podcastHeader()->render() !!}
    {!! $podcast->podcastItems()->render() !!}
    </channel>
</rss>

