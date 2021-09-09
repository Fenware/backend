
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
    state_account 		TINYINT(1) NOT NULL DEFAULT 2,
    connection_time     DATETIME, -- 0 inactiv 1 activ 2 pendent 
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

CREATE TABLE room_participants(
    id_room         INT NOT NULL,
    id_user         INT NOT NULL,
    PRIMARY KEY (id_room,id_user),
    FOREIGN KEY(id_room) REFERENCES room(id),
    FOREIGN KEY(id_user) REFERENCES `user`(id)
); 


-- -------------------------------UNTIL END OF PAGE, HARCODEADOS ----------------------------------------------------------
-- ADMIN
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('00000000','Administrador','Administrador','administrador@admin.com','01-man.svg','administrador','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO administrator(id) value(1);
-- -----------------------------------------------------------------------------------------
-- STUDENTS
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55618821','Lucas','Pintos','lukovich@hotmail.com','02-boy.svg','LukaPro3000','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',1);
INSERT INTO student(id) value(2);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55618823','Juan','Perez','jp@hotmail.com','02-boy.svg','Chopan','$2y$10$FVyaUDDXhzEeWvx2I//UoeSlRQlUNgzSe.vVYo6Y73qKNBFvfmFKS',1);
INSERT INTO student(id) value(3);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55628425','Gimena','Sosa','laGime123@hotmail.com','02-girl.svg','WatterLemon','$2y$10$qoGJyepvtWP.mYsV9adgU.ovWZP62Tb0GA91PuOgf5T53tlK2ErUa',1);
INSERT INTO student(id) value(4);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55654882','Felipe','Dobrinin','fel@hotmail.com','07-boy-2.svg','Ramandudu','$2y$10$4T.ztyM97bPBvpp6V9ZebOXNF7lhQxywgWDTtJS6z5VzGFstogqDK',1);
INSERT INTO student(id) value(5);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55123882','David','De Los Santos','d2a@hotmail.com','07-boy-2.svg','El Planilla','$2y$10$4T.ztyM97bPBvpp6V9ZebOXNF7lhQxywgWDTtJS6z5VzGFstogqDK',1);
INSERT INTO student(id) value(6);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('35123882','Adrian','Del Valle','incongnito43@hotmail.com','07-boy-2.svg','Larabel','$2y$10$JNTSFAmrcEyG77WNpdD/FOS0cwNpzOClGNgG1fEoGhW7Nd8A2rdtS',1);
INSERT INTO student(id) value(7);
INSERT INTO user(ci,`name`,middle_name,surname,email,avatar,nickname,`password`,state_account) values('15123882','Maria','Antonieta','De Las Nieves','marymary@hotmail.com','07-boy-2.svg','La Chilindrina','$2y$10$Ir4hV9iwqBCImyijHvGy1OUM1eR3/MKTqghWVsa1iymX9IsHlfN3K',1);
INSERT INTO student(id) value(8);
-- -----------------------------------------------------------------------------------------
-- TEACHERS
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('25123882','Dulcinea','Del Toboso','ejemplo@hotmail.com','07-boy-2.svg','La Donna','$2y$10$CXk22kPt/i7YhQwM/zpw7uFlMcrrZZljeHI74K.SvdzQvFSJcV/IW',1);
INSERT INTO teacher(id) value(9);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('25123482','Marta','Arbeleche','martaMartaMARTA@hotmail.com','07-boy-2.svg','MISS MONEY','$2y$10$6Rj62wvq.AOByFsYeKjRx.HQsRnITL7dHziVKHFLP5u6uCJV/RiJS',1);
INSERT INTO teacher(id) value(10);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('37123482','Hugo','Soca','delPlatoALaTierra@hotmail.com','07-boy-2.svg','Hugox','$2y$10$R503VVHa3HOLUm1cc0pTXuCvZCXKFmQwKetMgW3/RR.4u176QZTWC',1);
INSERT INTO teacher(id) value(11);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('17123482','Armando','Mesa','carpinteria@hotmail.com','07-boy-2.svg','WiWi','$2y$10$QlJr669nriQ9kOXcRL3Gbuh395m9e2vIlLvguaULzni73Mm3E3HaO',1);
INSERT INTO teacher(id) value(12);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('47321482','Keny','Bell','personal@hotmail.com','07-boy-2.svg','Campanita','$2y$10$htY7/Spbo8B6HCBS00EYs.OcvTpaqSsgFkQfatenY3ughh0tiQZ0m',1);
INSERT INTO teacher(id) value(13);
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('49821482','Adrian','Machado','machado@hotmail.com','07-boy-2.svg','Manchado','$2y$10$sLivTjfbyIEKdr1f5gygfOhclw9J.h/2x9q.C0nd/dug5AqkDGd2m',1);
INSERT INTO teacher(id) value(14);
-- -----------------------------------------------------------------------------------------
-- ORIENTATION
INSERT INTO orientation(`name`,`year`) values('Informatica 1',1);
INSERT INTO orientation(`name`,`year`) values('Informatica 2',2);
INSERT INTO orientation(`name`,`year`) values('Desarrollo y Soporte',3);
INSERT INTO orientation(`name`,`year`) values('Desarrollo Web',3);
-- -----------------------------------------------------------------------------------------
-- SUBJECT , SUBJECT_ORIENTATION

-- tronco comun
INSERT INTO `subject`(`name`) values("Matematica");
INSERT INTO subject_orientation(id_subject,id_orientation) values(1,1);
INSERT INTO subject_orientation(id_subject,id_orientation) values(1,2);
INSERT INTO subject_orientation(id_subject,id_orientation) values(1,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(1,4);
INSERT INTO `subject`(`name`) values("Ingles");
INSERT INTO subject_orientation(id_subject,id_orientation) values(2,1);
INSERT INTO subject_orientation(id_subject,id_orientation) values(2,2);
INSERT INTO subject_orientation(id_subject,id_orientation) values(2,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(2,4);
INSERT INTO `subject`(`name`) values("Fisica");
INSERT INTO subject_orientation(id_subject,id_orientation) values(3,2);
INSERT INTO `subject`(`name`) values("Quimica");
INSERT INTO subject_orientation(id_subject,id_orientation) values(4,1);
INSERT INTO `subject`(`name`) values("Analisis y Produccion de Textos");
INSERT INTO subject_orientation(id_subject,id_orientation) values(5,1);
INSERT INTO subject_orientation(id_subject,id_orientation) values(5,2);
INSERT INTO `subject`(`name`) values("Historia");
INSERT INTO subject_orientation(id_subject,id_orientation) values(6,1);
INSERT INTO `subject`(`name`) values("Filosofia");
INSERT INTO subject_orientation(id_subject,id_orientation) values(7,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(7,4);
INSERT INTO `subject`(`name`) values("Sociologia");
INSERT INTO subject_orientation(id_subject,id_orientation) values(8,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(8,4);
INSERT INTO `subject`(`name`) values("Biologia CTS");
INSERT INTO subject_orientation(id_subject,id_orientation) values(9,1);
INSERT INTO `subject`(`name`) values("Economia");
INSERT INTO subject_orientation(id_subject,id_orientation) values(10,2);

-- especificas 1
INSERT INTO `subject`(`name`) values("Programacion I");
INSERT INTO subject_orientation(id_subject,id_orientation) values(11,1);
INSERT INTO `subject`(`name`) values("Sistemas Opeativos I");
INSERT INTO subject_orientation(id_subject,id_orientation) values(12,1);
INSERT INTO `subject`(`name`) values("Logica Para Informatica");
INSERT INTO subject_orientation(id_subject,id_orientation) values(13,1);
INSERT INTO `subject`(`name`) values("Metodos Discretos");
INSERT INTO subject_orientation(id_subject,id_orientation) values(14,1);
INSERT INTO `subject`(`name`) values("Lab. de Soporte de Equipos Informaticos");
INSERT INTO subject_orientation(id_subject,id_orientation) values(15,1);
INSERT INTO `subject`(`name`) values("Geometria");
INSERT INTO subject_orientation(id_subject,id_orientation) values(16,1);
INSERT INTO subject_orientation(id_subject,id_orientation) values(16,2);
INSERT INTO `subject`(`name`) values("Lab. de Tecnologías Eléctricas Aplicadas");
INSERT INTO subject_orientation(id_subject,id_orientation) values(17,1);

-- especificas 2
INSERT INTO `subject`(`name`) values("Programacion II");
INSERT INTO subject_orientation(id_subject,id_orientation) values(18,2);
INSERT INTO `subject`(`name`) values("Sistemas Operativos II");
INSERT INTO subject_orientation(id_subject,id_orientation) values(19,2);

INSERT INTO `subject`(`name`) values("Diseño Web");
-- esta en 2 y en 3 de web
INSERT INTO subject_orientation(id_subject,id_orientation) values(20,2);
INSERT INTO subject_orientation(id_subject,id_orientation) values(20,4);

INSERT INTO `subject`(`name`) values("Sistemas de Bases de Datos I");
INSERT INTO subject_orientation(id_subject,id_orientation) values(21,2);
INSERT INTO `subject`(`name`) values("Lab. de Redes de Área Local");
INSERT INTO subject_orientation(id_subject,id_orientation) values(22,2);
INSERT INTO `subject`(`name`) values("Electrónica aplicada a la Informática");
INSERT INTO subject_orientation(id_subject,id_orientation) values(23,2);

-- especificas 3, WEB
INSERT INTO `subject`(`name`) values("Programacion Web");
INSERT INTO subject_orientation(id_subject,id_orientation) values(24,4);

INSERT INTO `subject`(`name`) values("Gestión de Proyectos Web");
INSERT INTO subject_orientation(id_subject,id_orientation) values(25,4);

-- especificas 3, SOPORTE 
INSERT INTO `subject`(`name`) values("Programacion III");
INSERT INTO subject_orientation(id_subject,id_orientation) values(26,3);
INSERT INTO `subject`(`name`) values("Gestión de Proyecto");
INSERT INTO subject_orientation(id_subject,id_orientation) values(27,3);


INSERT INTO `subject`(`name`) values("Redes de Datos y Seguridad");
INSERT INTO subject_orientation(id_subject,id_orientation) values(28,3);

-- especificas 3, web y soporte
INSERT INTO `subject`(`name`) values("Sistemas de Bases de Datos II");
INSERT INTO subject_orientation(id_subject,id_orientation) values(29,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(29,4);
INSERT INTO `subject`(`name`) values("Formacion Empresarial");
INSERT INTO subject_orientation(id_subject,id_orientation) values(30,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(30,4);
INSERT INTO `subject`(`name`) values("Sistemas Operativos III");
INSERT INTO subject_orientation(id_subject,id_orientation) values(31,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(31,4);
INSERT INTO `subject`(`name`) values("Analisis y diseño de Aplicaciones");
INSERT INTO subject_orientation(id_subject,id_orientation) values(32,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(32,4);

-- -----------------------------------------------------------------------------------------
-- GROUP
INSERT INTO `group`(id_orientation,`name`,`code`) values(1,"AA","GMFTiB1T");
INSERT INTO `group`(id_orientation,`name`,`code`) values(1,"BA","GMFTaB1T");

INSERT INTO `group`(id_orientation,`name`,`code`) values(2,"BC","GMFTqB1T");
INSERT INTO `group`(id_orientation,`name`,`code`) values(2,"CD","GMFTrB1T");

INSERT INTO `group`(id_orientation,`name`,`code`) values(3,"BE","GMFTsB1T");
INSERT INTO `group`(id_orientation,`name`,`code`) values(4,"CA","GMFTtB1T");

-- -----------------------------------------------------------------------------------------
-- TEACHER_GROUP
INSERT INTO teacher_group(id_teacher,id_group) values(9,1);
INSERT INTO teacher_group(id_teacher,id_group) values(9,2);
INSERT INTO teacher_group(id_teacher,id_group) values(9,3);



INSERT INTO teacher_group(id_teacher,id_group) values(10,5);
INSERT INTO teacher_group(id_teacher,id_group) values(10,6);

INSERT INTO teacher_group(id_teacher,id_group) values(11,1);
INSERT INTO teacher_group(id_teacher,id_group) values(11,2);

INSERT INTO teacher_group(id_teacher,id_group) values(12,1);
INSERT INTO teacher_group(id_teacher,id_group) values(12,2);
INSERT INTO teacher_group(id_teacher,id_group) values(12,5);

INSERT INTO teacher_group(id_teacher,id_group) values(13,1);
INSERT INTO teacher_group(id_teacher,id_group) values(13,2);
INSERT INTO teacher_group(id_teacher,id_group) values(13,5);
-- -----------------------------------------------------------------------------------------
-- TEACHER_GROUP_SUBJECT
desc teacher_group_subject;
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(9,1,1);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(9,2,1);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(9,3,1);

INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(10,5,30);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(10,6,30);


INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(11,1,5);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(11,2,5);


INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(12,1,2);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(12,2,2);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(12,5,2);

INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(13,1,6);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(13,2,10);
INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values(13,5,8);

-- INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values();
-- INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values();
-- INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values();
-- INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values();
-- INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values();
-- INSERT INTO teacher_group_subject(id_teacher,id_group,id_subject) values();


-- -----------------------------------------------------------------------------------------
-- QUERY, INDIVIDUAL, ROOM, MESSAGE
INSERT INTO `query`(id_student, id_teacher,id_group,id_subject,theme,creation_date,finish_date,`resume`) 
values(2,13,5,8,"Que es un desviado social?","2021-09-10","2021-09-10","Un desviado social es una persona que tiene un comportamiento que va en contra de la expectativa de los demas");
INSERT INTO individual value(1);
DESC message;
INSERT INTO message(id_query,id_user,content,`date`) values(1,2,"Hola profe, como anda? la pregunta es la que puse en el tema. Gracias","2021-09-10");
INSERT INTO message(id_query,id_user,content,`date`) values(1,13,"Buen dia, todo tranquilo bajo el sol? Me parece que faltaste el dia que tratamos ese tema en clase, un desviado social es una persona que tiene un comportamiento que va en contra de la expectativa de los demas. Que pase bien","2021-09-10");

INSERT INTO `query`(id_student, id_teacher,id_group,id_subject,theme,creation_date,finish_date,`resume`) 
values(5,12,5,2,"The correct translation of <<consulta>> ","2021-09-2","2021-09-3","We can use consultation or query, but consultation sounds like you don't know english and you are throwing fruit");
INSERT INTO room value(2);
INSERT INTO message(id_query,id_user,content,`date`) values(2,5,"Hello teacher, we would like to know the correct translation of <<consulta>> because we gonna use it in our proyect. Thanks!","2021-09-2");
INSERT INTO message(id_query,id_user,content,`date`) values(2,2,"That's a very good cuestion bro","2021-09-2");
INSERT INTO message(id_query,id_user,content,`date`) values(2,12,"Hello students, mmmm... you can use consultation or query, query sounds better, use that one.","2021-09-3");
-- -----------------------------------------------------------------------------------------
-- CONSULT_SCHEDULE
desc consult_schedule;
INSERT INTO consult_schedule values(13,0,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,1,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,2,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,3,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,4,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,5,"00:00:00","20:00:00");
INSERT INTO consult_schedule values(13,6,"00:00:00","20:00:00");

-- -----------------------------------------------------------------------------------------
select * from user;
select * from student;
select * from teacher;
select * from `subject`;
select * from orientation;
select * from subject_orientation ;
select * from `group`;
select * from  teacher_group;
select * from  `query`;
select * from individual;
select * from room;
select * from message;
SELECT * from consult_schedule;


-- SELECT s.id,s.`name`,s.state,o.id,so.state
-- FROM `subject` s,orientation o,subject_orientation so
-- WHERE s.id = so.id_subject AND o.id = so.id_orientation AND so.id_orientation =1;

-- select * from teacher_group inner join `user` where user.id=teacher_group.id_teacher;

-- UPDATE orientation SET `name` = 'ROBOTICA' , `year` = 2 WHERE id = 2;

-- UPDATE orientation SET `name` = "MATEMATICAS", `year` = 3 WHERE id = 1;
-- UPDATE `group` SET `state` = 1 WHERE id = 1;

-- DELETE FROM `subject`;

-- SELECT ci,`name`,middle_name,surname,second_surname,email,avatar,nickname,state_account 
-- FROM user u,administrator a
-- WHERE a.id != u.id;
