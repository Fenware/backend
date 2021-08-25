-- CREATE SCHEMA proyecto;
-- USE proyecto;
ALTER DATABASE `database` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/* Entities */
CREATE TABLE `user`(  
	id 					INT PRIMARY KEY AUTO_INCREMENT NOT NULL,  
    ci 					CHAR(8) UNIQUE NOT NULL,
    `name` 				VARCHAR(16) NOT NULL,
    middle_name 		VARCHAR(16),
    surname 			VARCHAR(16) NOT NULL,
    second_surname 		VARCHAR(16),
    email 				VARCHAR(100) UNIQUE NOT NULL,
    avatar 				VARCHAR(50) DEFAULT '01-man.svg',  -- Agregar default avatar
    nickname 			VARCHAR(32) UNIQUE NOT NULL,  
    state_account 		TINYINT(1) NOT NULL DEFAULT 2, -- 0 inactiv 1 activ 2 pendent 
    `password` 			VARCHAR(128) NOT NULL -- hashed pasword
); 

CREATE TABLE administrator(
	id 					INT PRIMARY KEY NOT NULL,
	FOREIGN KEY(id) REFERENCES `user`(id)
);

CREATE TABLE teacher(
	id 					INT PRIMARY KEY NOT NULL,
    max_rooms_per_gs    INT NOT NULL DEFAULT 1 CHECK (max_rooms_per_gs > 0),
    FOREIGN KEY(id) REFERENCES `user`(id)
);

CREATE TABLE student(
	id 					INT PRIMARY KEY NOT NULL,
	FOREIGN KEY(id) REFERENCES `user`(id)
);

CREATE TABLE `subject`(
	id 					INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` 				VARCHAR(50) UNIQUE NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE orientation(
	id 					INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` 				VARCHAR(50) NOT NULL,
    `year`				INT NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY(`name`,`year`)
);

CREATE TABLE `group`(
	id 					INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_orientation 		INT NOT NULL,
	`name` 				VARCHAR(3) NOT NULL,
	`code` 				CHAR(8) UNIQUE NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY(id_orientation) REFERENCES orientation(id)
);

CREATE TABLE consult_schedule(
	id_teacher 			INT NOT NULL,
	`day` 				TINYINT(1) NOT NULL,
    start_hour 			TIME NOT NULL,
    end_hour 			TIME NOT NULL,
    PRIMARY KEY(id_teacher,`day`),
    FOREIGN KEY(id_teacher) REFERENCES teacher(id)
);


/* Relations */

CREATE TABLE subject_orientation(
	id_subject 			INT NOT NULL,
    id_orientation 		INT NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY(id_subject, id_orientation),
    FOREIGN KEY(id_subject) REFERENCES `subject`(id),
	FOREIGN KEY(id_orientation) REFERENCES orientation(id)
);


CREATE TABLE teacher_group(
	id_teacher 			INT NOT NULL,
    id_group 			INT NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY(id_teacher, id_group),
    FOREIGN KEY(id_teacher) REFERENCES teacher(id),
	FOREIGN KEY(id_group) REFERENCES `group`(id)
);


CREATE TABLE teacher_group_subject(
	id_teacher 			INT NOT NULL,
    id_group 			INT NOT NULL,
    id_subject			INT NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY(id_teacher, id_group,id_subject),
    FOREIGN KEY(id_teacher,id_group) REFERENCES teacher_group(id_teacher, id_group)
);

CREATE TABLE student_group(
	id_student			INT NOT NULL PRIMARY KEY,
    id_group 			INT NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY(id_student) REFERENCES student(id),
	FOREIGN KEY(id_group) REFERENCES `group`(id)
);

/* Query */
CREATE TABLE `query`(
	id 					INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_student 			INT NOT NULL,
    id_teacher 			INT NOT NULL,
    id_group 			INT NOT NULL,
    id_subject 			INT NOT NULL,
    theme 				VARCHAR(50) NOT NULL,
    creation_date		DATETIME NOT NULL,
    finish_date 		DATETIME,
    `resume`			TEXT,
    state 				TINYINT(1) NOT NULL DEFAULT 1, /*1 recibida ,2 contestada ,0 cerrada*/
    FOREIGN KEY(id_student) REFERENCES student(id),
    FOREIGN KEY(id_teacher, id_group, id_subject) REFERENCES teacher_group_subject(id_teacher, id_group, id_subject)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE room(
	id 					INT PRIMARY KEY NOT NULL,
    FOREIGN KEY(id) REFERENCES `query`(id)
);

CREATE TABLE individual(
	id 					INT PRIMARY KEY NOT NULL,
    FOREIGN KEY(id) REFERENCES `query`(id)
);

CREATE TABLE message(
	id 					INT AUTO_INCREMENT NOT NULL,
    id_query 			INT NOT NULL,
    id_user				INT NOT NULL,
    content 			TEXT NOT NULL,
    `date` 				DATETIME NOT NULL,
    PRIMARY KEY(id, id_query),
    FOREIGN KEY(id_user) REFERENCES user(id),
    FOREIGN KEY(id_query) REFERENCES `query`(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;


INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('00000000','Administrador','Administrador','administrador@admin.com','01-man.svg','administrador','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO administrator(id) value(1);

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('11111111','ELu','Kitas','lukovich@hotmail.com','02-boy.svg','LukaPro3000','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO student(id) value(2);

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('22222222','Elcome','Piedras Volador','teacher@edu.com','07-boy-2.svg','ElProfeSAPEEEEE','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO teacher(id) value(3);


-- SELECT * FROM user;
-- SELECT * FROM student;
-- SELECT * FROM teacher;
-- SELECT * FROM `subject`;
-- SELECT * FROM orientation;
-- SELECT * FROM subject_orientation ;
-- SELECT * FROM `group`;
-- SELECT * FROM  teacher_group;
