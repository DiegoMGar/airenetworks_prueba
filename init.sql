-- create database prueba_aire character set utf8 collate utf8_general_ci;

drop table if exists llamadas_a_tratar;
create table if not exists llamadas_a_tratar (id int primary key auto_increment,
origen varchar(11) not null, destino varchar(11) not null,
descripcion varchar(20) default null,
cliente int not null, fecha date not null, hora time not null, segundos int not null,
precio float not null);

drop table if exists datos_a_tratar;
create table if not exists datos_a_tratar (id int primary key auto_increment,
origen varchar(11) not null,
descripcion varchar(20) default null,
cliente int not null, fecha date not null, hora time not null, mb int not null,
precio float not null);

drop table if exists mediayconsumo;
create table mediayconsumo (id int primary key auto_increment,
telefono varchar(11) not null, media_segundos float default 0, media_mb float default 0, consumo float default 0);