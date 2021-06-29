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
    email 				VARCHAR(100) NOT NULL,
    avatar 				VARCHAR(50),
    nickname 			VARCHAR(32),  
    state_account 		TINYINT(1) NOT NULL DEFAULT 2, -- 0 inactiv 1 activ 2 pendent 
    `password` 			VARCHAR(128) NOT NULL -- hashed pasword
); 

CREATE TABLE administrator(
	id 					INT PRIMARY KEY NOT NULL,
	FOREIGN KEY(id) REFERENCES `user`(id)
);

CREATE TABLE teacher(
	id 					INT PRIMARY KEY NOT NULL,
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
	`name` 				VARCHAR(3) UNIQUE NOT NULL,
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
	id_student			INT NOT NULL,
    id_group 			INT NOT NULL,
    state 				TINYINT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY(id_student, id_group),
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
    state 				TINYINT(1) NOT NULL DEFAULT 1, /*1 realizada por el alumno 2 contestada por el  3 recibida*/
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


INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('00000000','Administrador','Administrador','administrador@admin.com','/assets/admin.png','administrador','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO administrator(id) value(1);

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('11111111','ELu','Kitas','lukovich@hotmail.com','/assets/alumno.png','LukaPro3000','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO student(id) value(2);

select * from user;
select * from `subject`;
select * from orientation;
select * from subject_orientation;

SELECT s.id,s.`name`,s.state,o.id,so.state
FROM `subject` s,orientation o,subject_orientation so
WHERE s.id = so.id_subject AND o.id = so.id_orientation AND so.id_orientation =1;

UPDATE orientation SET `name` = 'ROBOTICA' , `year` = 2 WHERE id = 2;

UPDATE orientation SET `name` = "POPO", `year` = 3 WHERE id = 1;
