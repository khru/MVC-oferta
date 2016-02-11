/**
 * Creamos la base de datos
 */

/* Si existe eliminamos la base de datos*/
drop database if exists oferta;
/*Creamos la base de datos*/
create database oferta character set utf8 collate utf8_general_ci;

/*Selecionamos la base de datos a utilizar*/
use oferta;
/* Creamos la tabla  usuario*/
CREATE TABLE usuario(
    id          int(11) auto_increment not null comment 'Primary KEY de los usuarios',
    nombre      varchar(100) not null comment 'Nombre del usuarios',
    apellido    varchar(100) not null comment 'Apellidos del usuarios',
    email       varchar(100) not null comment 'Email del usuario de la aplicacion (UNIQUE)',
    pass        varchar(50)  not null comment 'Password del usuario de la aplicacion[Haseado no más de 32 caracteres]',
    unique(email),
    PRIMARY KEY (id)
);
/* INSERCIÓN DE REGESTROS */
INSERT INTO usuario (nombre, apellido ,email, pass) VALUES ('Emmanuel', 'Valverde Ramos','admin@admin.com', SHA1('Password1'));

/* Creamos la tabla empresa */
CREATE TABLE empresa(
	id			int(11) auto_increment not null comment 'PRIMARY KEY de la empresa',
	nombre		varchar(100) not null comment 'Nombre de la empresa',
	web			varchar(255) not null comment 'Web de la empresa',
	descripcion	text not null comment 'Descripcion de de la empresa',
    usuario     int(11) not null comment 'FK cliente al que pertenece cada registro',
	unique(nombre),
	PRIMARY KEY (id),
    FOREIGN KEY (usuario) REFERENCES usuario(id) ON UPDATE cascade
);
/* INSERCIÓN DE REGESTROS */
INSERT INTO empresa (nombre, web, descripcion, usuario) VALUES ('BETWEEN - ADICIONA', 'http://www.adiciona.com/empleo','Bienvenidos a BETWEEN, empresa nacida de la fusión de ADICIONA y SOLID Enginyeria. Somos especialistas en perfiles tecnológicos. Ofrecemos servicios de outsourcing informático, servicios de selección especializada en perfiles tecnológicos (Informáticos, ingenieros industriales y eléctricos) y soluciones informáticas y multimedia. Nº de autorización como Agencia de Colocación en Cataluña: 0900000103', 1);

/* Creamos la tabla  oferta [PARA EVITAR tablas N:M, crearemos 2 columnas una para la Fk de la empresa y la otra para la FK del cliente] */
CREATE TABLE oferta(
    id          int(11) auto_increment not null comment 'PK de la tabla oferta',
    nombre      varchar(100) not null comment 'Nombre de la oferta',
    descripcion text not null comment 'Descripción de la oferta',
    requisitos	text not null comment 'Requisitos de la oferta',
    url 		varchar(255) comment 'URL de la oferta si existe admite null',
    salario		varchar(100) comment 'Salario de la oferta admite null',
    empresa 	int(11) not null comment 'FK de la empresa que realiza la oferta [Una oferta solo puede ser de una empresa]',
    fecha_alta  date comment 'Fecha de creacion de la oferta',
    unique(nombre),
    PRIMARY KEY (id),
    FOREIGN KEY (empresa) REFERENCES empresa(id) ON UPDATE cascade
);
/* INSERCIÓN DE REGESTROS */
INSERT INTO oferta (nombre, descripcion, requisitos, url, salario, empresa, fecha_alta) VALUES (UPPER('PROGRAMADOR PHP SENIOR'), 'Desde BETWEEN TECHNOLOGY seleccionamos un "Programador PHP" para un proyecto de migracion de aplicaciones de las plataformas Zend 1 y Yii a tecnologias mas modernas para una empresa multinacional alemana', 'Estudios mínimos: Ingeniero Técnico - Técnico en Informática de Sistemas, Experiencia mínima: Al menos 2 años, Requisitos mínimos: Programacion PHP orientada a objetos, Plataforma Hybris (Java), Conocimiento de Frameworks de PHP (Zend 1, Symphony,...), Buenos conocimientos de SQL, SOLR, MemCache, NGINX, Excelentes conocimientos en: HTML, XML, Javascript, CSS, etc. Experiencia en desarrollo de aplicaciones', 'https://www.infojobs.net/barcelona/programador-php-senior/of-ib1e32b1b8b48aea6594af56a3a165e', '30.000€ - 30.000€ Bruto/año', 1, '1992-06-09');
