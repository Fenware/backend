/*ENTIDADES:*/
CREATE TABLE user(
	id 					INT PRIMARY KEY AUTO_INCREMENT,
    ci					VARCHAR(8) 	 UNIQUE NOT NULL ,
    nombre 				VARCHAR(16)  NOT NULL		,
    segundo_nombre		VARCHAR(16)					,
    apellido 			VARCHAR(16)	 NOT NULL		,
    segundo_apellido	VARCHAR(16)					,
    email				VARCHAR(100) NOT NULL		,
    avatar 				VARCHAR(30)	 NOT NULL 		,
    nickname 			VARCHAR(16)  NOT NULL 		,
    estado_conexion 	TINYINT(1)	 				,
	password			CHAR(128)	 NOT NULL		,
    estado_cuenta		TINYINT(1)	 NOT NULL
);

CREATE TABLE alumno(
	id 					INT PRIMARY KEY,
    FOREIGN KEY(id) REFERENCES user(id)
);

CREATE TABLE docente(
	id 					INT PRIMARY KEY,
    FOREIGN KEY(id) REFERENCES user(id)
);

CREATE TABLE administrador(
	id 					INT PRIMARY KEY,
    FOREIGN KEY(id) REFERENCES user(id)
);

create table materia(
	id 					INT PRIMARY KEY AUTO_INCREMENT,
	nombre 				VARCHAR(50) NOT NULL /*ADA Web tiene mas de 40 chars*/,
    estado				TINYINT(1) NOT NULL DEFAULT 1/* 0 inactivo ,1 activo*/
);
ALTER TABLE materia
ALTER estado SET DEFAULT 1;
select * from materia;

INSERT INTO user(ci,nombre,apellido,email,avatar,nickname,password,estado_cuenta) values('00000000','Administrador','Administrador','administrador@admin.com','/assets/admin.png','administrador','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO administrador(id) value(1);

INSERT INTO user(ci,nombre,apellido,email,avatar,nickname,password,estado_cuenta) values('11111111','ELu','Kitas','lukovich@hotmail.com','/assets/alumno.png','LukaPro3000','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO alumno(id) value(1);