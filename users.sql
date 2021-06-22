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
    estado_conexion 	TINYINT(1)	 NOT NULL		,
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


CREATE TABLE user_token (
  tokenId 				INT PRIMARY KEY NOT NULL,
  userId 				INT NOT NULL,
  token 				VARCHAR(45) NOT NULL,
  estado 				TINYINT(1)  NOT NULL,
  fecha					DATETIME 	NOT NULL,
  FOREIGN KEY(userId) REFERENCES user(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


desc user;
select * from administrador;
-- password : mnoseadmin1234 --
INSERT INTO user(ci,nombre,apellido,email,avatar,nickname,password,estado_cuenta) values('00000000','Administrador','Administrador','administrador@admin.com','/assets/admin.png','administrador','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO administrador(id) value(1);
alter table user modify estado_conexion 	TINYINT(1);