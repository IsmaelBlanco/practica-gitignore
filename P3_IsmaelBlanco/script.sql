drop database if exists PortiQ;
create database PortiQ;
use PortiQ;

create table usuarios(
	id int auto_increment primary key,
	username varchar(255) unique,
    email varchar(255) unique,
    password varchar(255),
    nombre varchar(255),
	apellido1 varchar(255),
    apellido2 varchar(255),
    especialidad ENUM(
	  'diseñador gráfico',
	  'diseñador UI/UX',
	  'desarrollador web',
	  'fotógrafo',
	  'videógrafo',
	  'ilustrador',
	  'artista 3D',
	  'escritor creativo',
	  'copywriter',
	  'marketing digital',
	  'community manager',
	  'músico',
	  'arquitecto',
	  'editor de vídeo',
	  'animador',
	  'otros'
	),
    paleta enum('dark','blue','red','japanese','light');
    subcategoria varchar(255),
    fecha_creado DATETIME DEFAULT CURRENT_TIMESTAMP
);

create table proyectos(
	id int auto_increment primary key,
    usuario_id int,
    titulo varchar(255),
    descripcion text,
    url text,
    orden int default 0,
    upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);