CREATE TABLE CONSOLR_POST_TAG (
    POST_ID INT UNSIGNED NOT NULL ,
    TUMBLR_NAME VARCHAR( 255 ) NOT NULL ,
    TAG VARCHAR( 255 ) NOT NULL ,

    PRIMARY KEY ( POST_ID , TAG )
) ENGINE = InnoDB;