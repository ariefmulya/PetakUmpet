CREATE TABLE userdata (
  id serial,
  name varchar(100),
  password text,
  status varchar(10),
  PRIMARY KEY(id)
);

CREATE TABLE groupdata (
  id serial,
  name varchar(50),
  PRIMARY KEY(id)
);

CREATE TABLE user_group (
  id serial,
  user_id integer,
  group_id integer,
  PRIMARY KEY(id),
  FOREIGN KEY (user_id) REFERENCES userdata (id),
  FOREIGN KEY (group_id) REFERENCES groupdata (id)
);


INSERT INTO userdata (name, password) VALUES ('arief', '1');
