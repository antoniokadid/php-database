create schema test collate utf8_general_ci;

create table HelloWorld
(
  id char(32) not null primary key,
  name varchar(256) not null
);

