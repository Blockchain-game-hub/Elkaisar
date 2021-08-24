CREATE TABLE team (
	id_team int(11) PRIMARY KEY NOT null AUTO_INCREMENT,
    id_leader int(11) NOT NULL,
    name varchar(32),
    lvl int(3) DEFAULT 1,
    slog_top int(3) DEFAULT 0,
    slog_cnt int(3) DEFAULT 0,
    slog_btm int(3) DEFAULT 0,
    mem_num int(3) DEFAULT 1,
    word text DEFAULT "",
    prestige BigInt(10) DEFAULT 0,
    honor BigInt(10) DEFAULT 0,
    time_stamp int(11) DEFAULT 0
    
);


CREATE TABLE team_donation (
	id_team int(11) NOT NULL PRIMARY KEY,
    food  int(11) DEFAULT 0,
    wood  int(11) DEFAULT 0,
    stone int(11) DEFAULT 0,
    metal int(11) DEFAULT 0,
    coin  int(11) DEFAULT 0,
    gold  int(11) DEFAULT 0

);

CREATE TABLE team_inv (
    id_inv int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_team int(11) NOT NULL,
    id_player int(11) NOT NULL,
    inv_by int(11) NOT NULL,
    time_stamp int(10) DEFAULT 0
);

CREATE TABLE team_req (
    id_req int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_team int(11) NOT NULL,
    id_player int(11) NOT NULL,
    time_stamp int(10) DEFAULT 0
);

CREATE TABLE team_relation(
    id_rel int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_team_1 int(11) NOT NULL,
    id_team_2 int(11) NOT NULL,
    relation int(3) DEFAULT 0,
    id_made_by int(11) NOT null,
    time_stamp int(10) DEFAULT 0
);

CREATE TABLE team_member(
	id_team int(11) NOT NULL,
    id_player int(11) NOT NULL,
    time_join int(10) DEFAULT 0,
    rank int(3) DEFAULT 0,
    prize_share int(3) DEFAULT 0,
    PRIMARY KEY(id_team , id_player)

);