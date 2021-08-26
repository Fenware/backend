<?php

require_once '/var/www/html/core/model.php';
require_once '/var/www/html/core/response.php';
/*
Modelo para las consultas
*/
class ScheduleModel extends Model{

    private $teacher;
    private $day;
    private $start_hour;
    private $end_hour;


    public function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }

    //Crea un horario en un dia  
    public function createScheduleForDay(){
        $stm = 'INSERT INTO `consult_schedule`(id_teacher,`day`,start_hour,end_hour) VALUES(?,?,?,?)';
        $rows = parent::query($stm, [ $this->teacher , $this->day , $this->start_hour , $this->end_hour ] );
        //Devuelvo 1 si se crea bien , 0 si salio mal
        return $rows;
    }
    //Modifica un horario en un dia  
    public function modifyScheduleForDay(){
        $stm = 'UPDATE `consult_schedule` SET `day` = ? , start_hour = ? , end_hour = ? WHERE id_teacher = ? AND `day` = ?';
        $rows = parent::query($stm, [ $this->day , $this->start_hour , $this->end_hour , $this->teacher , $this->day ] );
        //Devuelvo 1 si se crea bien , 0 si salio mal
        return $rows;
    }


    //Devuelve los horarios de un docente
    public function getTeacherSchedule($teacher){
        $stm = 'SELECT * FROM `consult_schedule` WHERE id_teacher = ?';
        $schedules = parent::query($stm , [$teacher] );
        return $schedules;
    }

    public function teacherHasDayCreated($teacher,$day){
        $stm = 'SELECT * FROM `consult_schedule` WHERE id_teacher = ? AND `day` = ?';
        $schedules = parent::query($stm , [$teacher,$day] );
        return $schedules;
    }

    public function deleteTeacherScheduleForDay($teacher,$day){
        $stm = 'DELETE FROM `consult_schedule` WHERE id_teacher = ? AND `day` = ?';
        $rows = parent::nonQuery($stm , [$teacher,$day] );
        return $rows;
    }

    public function setTeacher($teacher){
        $this->teacher = $teacher;
    }

    public function getTeacher(){
        return $this->teacher;
    }

    public function setDay($day){
        $this->day = $day;
    }

    public function getDay(){
        return $this->day;
    }

    public function setStartHour($start_hour){
        $this->start_hour = $start_hour;
    }

    public function getStartHour(){
        return $this->start_hour;
    }

    public function setEndHour($end_hour){
        $this->end_hour = $end_hour;
    }

    public function getEndHour(){
        return $this->end_hour;
    }

}