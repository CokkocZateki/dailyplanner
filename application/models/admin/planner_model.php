<?php

class Planner_Model extends CI_Model {

    protected $table = 'dp_schedule';

    public function getAppointments($datesWeek){
        $this->db->select('*');
        $this->db->from(''.$this->table.' as dps');
        $this->db->where_in('dps.date', $datesWeek);
        $this->db->where(array('dps.user_id' => $this->userid, 'deleted' => NULL));
        return $this->db->get()->result();
    }

    public function getSingleAppointment($datePassed, $timeslot){
        $this->db->select('*');
        $this->db->from(''.$this->table.' as dps');
        $this->db->where(array('dps.user_id' => $this->userid, 'dps.date' => $datePassed, 'dps.timeslot' => $timeslot, 'deleted' => NULL));
        return $this->db->get()->result();
    }

    public function updateTaskByDrag($data){
        $dataToUpdate = array(
            'timeslot' => $data['timeslot'],
            'date' => $data['date']
        );
        $this->db->where('id', $data['id']);
        $this->db->update($this->table, $dataToUpdate);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function updateTaskByID($dataPassed, $id){
        $this->db->where('id', $id);
        $this->db->update($this->table, $dataPassed);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }   

    public function updateTaskToDelete($id){
        $dataToUpdate = array(
            'deleted' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $id);
        $this->db->update($this->table, $dataToUpdate);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }     

    

    public function getWeekPlanner($datePassed) {
        $dayNum = date('N', strtotime($datePassed));
        $diffD = $dayNum - 1;
        if ($dayNum > 1) {
            $firstDayDate = date('Y-m-d', strtotime($datePassed .'- '.$diffD.' days'));
        }

        //$dates = [];
        for($i=0; $i<7; $i++) {
            $dates = date('Y-m-d', strtotime($firstDayDate)+86400*$i);

            $this->db->select('*');
            $this->db->from(''.$this->table.' as dps');
            $this->db->where(array('dps.user_id' => $this->userid, 'dps.date' => $dates));
        }
        //return $dates;

    }

    public function checkRemainingMinutes() {

        $total_minutes = 0;

        $checkData = array(
            'dps.date' => $this->input->post('dp_date'),
            'dps.timeslot' => $this->input->post('dp_timeslot'),
            'dps.user_id' => $this->userid,
            'dps.deleted' => NULL
        );
        $this->db->select('dps.duration');
        $this->db->from(''.$this->table.' as dps');
        $this->db->where($checkData);
        $minutes = $this->db->get()->result();

        foreach ($minutes as $key => $value) {
            $total_minutes += $value->duration;
        }
        return $total_minutes;
        
    }

    public function insertTaskData($data){
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }    

}