
# to be sure
USE podmytubeTests;

# before erasing all data
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE channels;
TRUNCATE medias;
TRUNCATE subscriptions;
SET FOREIGN_KEY_CHECKS=1;

# insert sample into channels 
INSERT INTO channels (channel_id, user_id, channel_name, channel_premium, active,accept_video_by_tag) VALUES
    ('freeChannel', 1, 'Free users', 0, 1, null),
    ('earlyChannel', 1, 'Early birds', 1, 1, null),
    ('weeklyChannel', 1, 'Weekly youtuber', 2, 1, null),
    ('dailyChannel', 1, 'Daily youtuber', 3, 1, null),
    ('UCq80IvL314jsE7PgYsTdw7Q', 1, 'Accropolis', 3, 1, null),
    ('UCnF1gaTK11ax2pWCIdUp8-w', 1, 'Monthly subscribers 6€/month - delphine dimanche', 3, 1, null),
    ('UCnf8HI3gUteF1BKAvrDO9dQ', 1, 'Yearly subscribers 66€/year - alex borto', 3, 1, null);

# insert sample into media 
# getAudio is getting all medias ordered by published_at date
INSERT INTO medias (media_id, channel_id, title, published_at) VALUES
("invalidId1", "freeChannel",   "This media does not exist on YT", NOW()),
("invalidId2", "freeChannel",   "This media does not exist on YT", NOW()),
("invalidId3", "freeChannel",   "This media does not exist on YT", NOW()),
("YsBVu6f8pR8", "freeChannel",   "This video is eligible",   DATE_SUB(NOW() , INTERVAL 1 HOUR)),
("KsSPMDe_YWY", "freeChannel",   "This video is eligible",   DATE_SUB(NOW() , INTERVAL 2 HOUR)),
("hKjtoNByLAI", "freeChannel",   "This video is NOT eligible - tags", DATE_SUB(NOW() , INTERVAL 3 HOUR)),
("Aks6eKumi3c", "freeChannel",   "This video is NOT eligible - too long ago", DATE_SUB(NOW(), INTERVAL 2 MONTH)),

("fzkDfcF0LLo", "earlyChannel",  "This video is eligible",  DATE_FORMAT(NOW() ,'%Y-%m-28')),
("FguIk-SEkWI", "earlyChannel",  "This video is eligible",  DATE_FORMAT(NOW() ,'%Y-%m-25')),
("4iZxO8I-vIk", "earlyChannel",  "This video is eligible",  DATE_FORMAT(NOW() ,'%Y-%m-21')),
("7PlDTMVpSAI", "earlyChannel",  "This video is eligible",  DATE_FORMAT(NOW() ,'%Y-%m-15')),
("FR5KCellhXE", "earlyChannel",  "This video is eligible",  DATE_FORMAT(NOW() ,'%Y-%m-12')),
("TJ4NFeeUfbY", "earlyChannel",  "This video is eligible",  DATE_FORMAT(NOW() ,'%Y-%m-07')),
("KZQeFvTYBQQ", "earlyChannel",  "This video is eligible",  DATE_FORMAT(NOW() ,'%Y-%m-03')),
("RXq6aklLt_Y", "earlyChannel",  "This video is eligible",  LAST_DAY(now() - INTERVAL 1 MONTH)),
("TqS6fVmK5uM", "earlyChannel",  "This video is NOT eligible - too long ago",  DATE_SUB(NOW(), INTERVAL 35 DAY)),

("FYJ3NUWoOvE", "weeklyChannel", "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-28')),
("zmfOeATX4fY", "weeklyChannel", "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-25')),
("iuXckmWv4Pc", "weeklyChannel", "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-21')),
("qSs5zsGoqs4", "weeklyChannel", "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-15')),
("y3GLhAumiec", "weeklyChannel", "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-12')),
("o308rJlWKUc", "weeklyChannel", "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-07')),
("-wvQP07Syak", "weeklyChannel", "This video is eligible", LAST_DAY(now() - INTERVAL 1 MONTH)),
("8ZwgoVmILQU", "weeklyChannel", "This video is NOT eligible - too long ago", DATE_SUB(NOW(), INTERVAL 35 DAY)),

("IRsFc2gguEg", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-28')),
("PmjRhEXLReQ", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-25')),
("bgTlt5-l-AA", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-22')),
("QgG0BQSjxoo", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-21')),
("rndLWLmwgeA", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-19')),
("eIWs2IUr3Vs", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-15')),
("TIIpiYzBdhI", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-12')),
("b-kTeJhHOhc", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-11')),
("8hYlB38asDY", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-09')),
("BoohRoVA9WQ", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-07')),
("2CzoSeClcw0", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-03')),
("ORgGz9d6OhY", "dailyChannel",  "This video is eligible", DATE_FORMAT(NOW() ,'%Y-%m-01')),
("tg52up16eq0", "dailyChannel",  "This video is eligible", LAST_DAY(now() - INTERVAL 1 MONTH)),
("ZVzyGKp7Ekk", "dailyChannel",  "This video is NOT eligible - too long ago", DATE_SUB(NOW(), INTERVAL 35 DAY));


# insert sample into playlists 
# INSERT INTO playlists (channel_id, user_id, channel_name, channel_premium, active,accept_video_by_tag) VALUES
#     ('freeChannel', 0, 'The free users', 0, 1, 'Trailer'),
#     ('earlyChannel', 1, 'The first users that registered', 1, 1, null),
#     ('weeklyChannel', 2, 'Lowest price subscription', 2, 1, null),
#     ('dailyChannel', 3, 'Highest price subscription', 3, 1, null);

# playlist_id,channel_id,playlist_title,playlist_description,playlist_thumbnail,playlist_publishedAt,playlist_updatedAt,playlist_active