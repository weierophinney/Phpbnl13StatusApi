CREATE TABLE status
(
    id         CHARACTER(40) PRIMARY KEY NOT NULL,
    type       VARCHAR(6) NOT NULL CHECK (type IN ('status', 'image', 'link')),
    timestamp  INTEGER NOT NULL,
    user       VARCHAR(256) NOT NULL,
    text       TEXT,
    image_url  VARCHAR(256),
    link_url   VARCHAR(256),
    link_title VARCHAR(256),
    CHECK (
        (type = "status" and text IS NOT NULL)
        or (type = "image" and image_url IS NOT NULL)
        or (type = "link" and link_url IS NOT NULL)
    )
);

CREATE INDEX status_sorted ON status(timestamp DESC);
CREATE INDEX status_user_sorted ON status(user, timestamp DESC);
