<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="/stylesheet.xsl" type="text/xsl"?>
<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
    {!! $podcast->podcastHeader !!}
    {!! $podcast->podcastItems !!}
    </channel>
</rss>

