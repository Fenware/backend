ALTER DATABASE `database` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_cs;
/* Entities */
CREATE TABLE `user`(  
	id 					INT PRIMARY KEY AUTO_INCREMENT NOT NULL,  
    ci 					CHAR(9) UNIQUE NOT NULL,
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
	`day` 				TINYINT NOT NULL CHECK (`day` >= 1 AND `day` <= 5),
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

CREATE TABLE `message`(
	id 					INT AUTO_INCREMENT NOT NULL,
    id_query 			INT NOT NULL,
    id_user				INT NOT NULL,
    content 			VARCHAR(600) NOT NULL,
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
-- 	PASS : SAME AS BEFORE 
-- -----------------------------------------------------------------------------------------
-- ORIENTATION
INSERT INTO orientation(`name`,`year`) values('Informatica 1',1);
INSERT INTO orientation(`name`,`year`) values('Informatica 2',2);
INSERT INTO orientation(`name`,`year`) values('Desarrollo y Soporte',3);
INSERT INTO orientation(`name`,`year`) values('Desarrollo Web',3);
-- -----------------------------------------------------------------------------------------
-- GROUP
INSERT INTO `group`(id_orientation,`name`,`code`) values(1,"AA","gERfubQ2");
INSERT INTO `group`(id_orientation,`name`,`code`) values(1,"BA","uD9J2iQF");

INSERT INTO `group`(id_orientation,`name`,`code`) values(2,"BC","USHzbV7K");
INSERT INTO `group`(id_orientation,`name`,`code`) values(2,"CD","CFjtm2se");

INSERT INTO `group`(id_orientation,`name`,`code`) values(3,"BE","6Y6ufEeG");
INSERT INTO `group`(id_orientation,`name`,`code`) values(4,"CA","w8PZ53kE");

-- -----------------------------------------------------------------------------------------
-- STUDENTS, STUDENT_GROUP
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55618821','Lucas','Pintos','lucaspintos909@gmail.com','02-boy.svg','lucaspintos909','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',2);
INSERT INTO student(id) value(2);
INSERT INTO student_group(id_student,id_group) values(2,5);
-- 	PASS : SAME AS BEFORE 

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55618823','Juan','Perez','jp@gmail.com','02-boy.svg','Chopan','$2y$10$SUKaHxnxnDi4BLZ9gaXwPee0V9tTJVVekq3f1W1q8MVxsC9CeypZi',1);
INSERT INTO student(id) value(3);
INSERT INTO student_group(id_student,id_group) values(3,1);
-- 	PASS : jp546jp

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55628425','Gimena','Sosa','laGime123@gmail.com','02-boy.svg','WatterLemon','$2y$10$Zin6Sn6KsJbVPVeC9sFmguyTFWsbTvU2MmjKt04hmiSmbgFqc/mmq',1);
INSERT INTO student(id) value(4);
INSERT INTO student_group(id_student,id_group) values(4,2);
-- 	PASS : gs546gs

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55654882','Felipe','Dobrinin','fel@gmail.com','07-boy-2.svg','Ramandudu','$2y$10$IKLJtiGl.ti2p52tdpLTYOUlZWDMS8sxMTRL75CIFnG04fSO3JBrW',2);
INSERT INTO student(id) value(5);
INSERT INTO student_group(id_student,id_group) values(5,5);
-- 	PASS : fd546fd

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('55123882','David','De Los Santos','d2a@gmail.com','07-boy-2.svg','El Planilla','$2y$10$sTt2XEiRDwIep2YA6nerPeNVOxmFj2gUAOjXsFDne5h0IW8N9QuhC',1);
INSERT INTO student(id) value(6);
INSERT INTO student_group(id_student,id_group) values(6,5);
-- 	PASS : dd546dd

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('35123882','Adrian','Del Valle','incongnito43@gmail.com','07-boy-2.svg','Larabel','$2y$10$SHSoqQ7R419cipEOB3mfP.f7P5uoSjiBeMI5xF0MVuRAmPx4bQhe6',1);
INSERT INTO student(id) value(7);
INSERT INTO student_group(id_student,id_group) values(7,3);
-- 	PASS : ad546ad

INSERT INTO user(ci,`name`,middle_name,surname,email,avatar,nickname,`password`,state_account) values('15123882','Maria','Antonieta','De Las Nieves','marymary@gmail.com','07-boy-2.svg','La Chilindrina','$2y$10$WRkUcgw1Xs4CAqV9Juut9evoTNzWvpXGOBm1MgaBi2lxJ1jGrrPbS',1);
INSERT INTO student(id) value(8);
INSERT INTO student_group(id_student,id_group) values(8,4);
-- 	PASS : md546md
-- -----------------------------------------------------------------------------------------
-- TEACHERS
INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('25123882','Dulcinea','Del Toboso','ejemplo@gmail.com','07-boy-2.svg','La Donna','$2y$10$CnrfzHzJX1ptLdw2PxQUo.JxR/5pPPxbtMU6I5B.CF77Cl2Vr/ih2',1);
INSERT INTO teacher(id) value(9);
-- 	PASS : dul546dul

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('25123482','Docente','Del ESI','docente@gmail.com','07-boy-2.svg','docente123','$2y$10$NOA9YzGzXsE.DCGwMMor2uYcl5ZtJGJxCix88blfVIcNg3H7c7KKW',2);
INSERT INTO teacher(id) value(10);
-- 	PASS : mnoseadmin1234

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('37123482','Hugo','Soca','delPlatoALaTierra@gmail.com','07-boy-2.svg','Hugox','$2y$10$L10fWCklfyHYE6HHYBJfPuKB/k9jI8luFdYwYxQlvUPAi2hK0FWQ2',1);
INSERT INTO teacher(id) value(11);
-- 	PASS : hs546hs

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('17123482','Armando','Mesa','carpinteria@gmail.com','07-boy-2.svg','WiWi','$2y$10$wsQRNDfHy/msMmbUh9hFBeVQmU9aiNzM2xunTNVNqWAjsMbPwXFsi',1);
INSERT INTO teacher(id) value(12);
-- 	PASS : armando546armando

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('47321482','Keny','Bell','personal@gmail.com','07-boy-2.svg','Campanita','$2y$10$PIy9wHl9KhNoND.NFGTx9udt.zdkzoHl6wdfNkrBkbL.EBU6LV0bq',1);
INSERT INTO teacher(id) value(13);
-- 	PASS : kb546kb

INSERT INTO user(ci,`name`,surname,email,avatar,nickname,`password`,state_account) values('49821482','Adrian','Machado','machado@gmail.com','07-boy-2.svg','Manchado','$2y$10$eN3xOi3I5MeZ.KxnbYOEreB/cWjAgPWmg.Y3WyISMjk84DvR4jJ5C',1);
INSERT INTO teacher(id) value(14);
-- 	PASS : adrian546adrian
-- -----------------------------------------------------------------------------------------

-- SUBJECT , SUBJECT_ORIENTATION

-- tronco comun
INSERT INTO `subject`(`name`) values("Matematica");
INSERT INTO subject_orientation(id_subject,id_orientation) values(1,1), (1,2), (1,3), (1,4);
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
INSERT INTO `subject`(`name`) values("Lab. de Tecnologias Electricas Aplicadas");
INSERT INTO subject_orientation(id_subject,id_orientation) values(17,1);

-- especificas 2
INSERT INTO `subject`(`name`) values("Programacion II");
INSERT INTO subject_orientation(id_subject,id_orientation) values(18,2);
INSERT INTO `subject`(`name`) values("Sistemas Operativos II");
INSERT INTO subject_orientation(id_subject,id_orientation) values(19,2);

INSERT INTO `subject`(`name`) values("Disenno Web");
-- esta en 2 y en 3 de web
INSERT INTO subject_orientation(id_subject,id_orientation) values(20,2);
INSERT INTO subject_orientation(id_subject,id_orientation) values(20,4);

INSERT INTO `subject`(`name`) values("Sistemas de Bases de Datos I");
INSERT INTO subject_orientation(id_subject,id_orientation) values(21,2);
INSERT INTO `subject`(`name`) values("Lab. de Redes de Area Local");
INSERT INTO subject_orientation(id_subject,id_orientation) values(22,2);
INSERT INTO `subject`(`name`) values("Electronica aplicada a la Informatica");
INSERT INTO subject_orientation(id_subject,id_orientation) values(23,2);

-- especificas 3, WEB
INSERT INTO `subject`(`name`) values("Programacion Web");
INSERT INTO subject_orientation(id_subject,id_orientation) values(24,4);

INSERT INTO `subject`(`name`) values("Gestion de Proyectos Web");
INSERT INTO subject_orientation(id_subject,id_orientation) values(25,4);

-- especificas 3, SOPORTE 
INSERT INTO `subject`(`name`) values("Programacion III");
INSERT INTO subject_orientation(id_subject,id_orientation) values(26,3);
INSERT INTO `subject`(`name`) values("Gestion de Proyecto");
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
INSERT INTO `subject`(`name`) values("Analisis y disenno de Aplicaciones");
INSERT INTO subject_orientation(id_subject,id_orientation) values(32,3);
INSERT INTO subject_orientation(id_subject,id_orientation) values(32,4);

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
-- QUERY, INDIVIDUAL, ROOM, MESSAGE, ROOM_PARTICIPANTS
INSERT INTO `query`(id_student, id_teacher,id_group,id_subject,theme,creation_date,finish_date,`resume`) 
values(2,13,5,8,"Que es un desviado social?","2021-09-10","2021-09-10","Un desviado social es una persona que tiene un comportamiento que va contra de la expectativa de los demas");
INSERT INTO individual value(1);
INSERT INTO message(id_query,id_user,content,`date`) values(1,2,"Hola profe, como anda? la pregunta es la que puse en el tema. Gracias","2021-09-10");
INSERT INTO message(id_query,id_user,content,`date`) values(1,13,"Buen dia, todo tranquilo bajo el sol? Me parece que faltaste el dia que tratamos ese tema en clase, un desviado social es una persona que tiene un comportamiento que va en contra de la expectativa de los demas. Que pase bien","2021-09-10");

INSERT INTO `query`(id_student, id_teacher,id_group,id_subject,theme,creation_date,finish_date,`resume`) 
values(5,12,5,2,"The correct translation of <<consulta>> ","2021-09-2","2021-09-3","We can use consultation or query, but consultation sounds like you don't know english and you are throwing fruit");
INSERT INTO room value(2);
INSERT INTO message(id_query,id_user,content,`date`) values(2,5,"Hello teacher, we would like to know the correct translation of <<consulta>> because we gonna use it in our proyect. Thanks!","2021-09-2");
INSERT INTO message(id_query,id_user,content,`date`) values(2,2,"That's a very good cuestion bro","2021-09-2");
INSERT INTO message(id_query,id_user,content,`date`) values(2,12,"Hello students, mmmm... you can use consultation or query, query sounds better, use that one.","2021-09-3");
INSERT INTO room_participants values(2,5);
INSERT INTO room_participants values(2,2);
INSERT INTO room_participants values(2,12);
-- -----------------------------------------------------------------------------------------
-- CONSULT_SCHEDULE
INSERT INTO consult_schedule values(13,0,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,1,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,2,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,3,"00:00:00","18:00:00");
INSERT INTO consult_schedule values(13,4,"00:00:00","18:00:00");

-- -----------------------------------------------------------------------------------------