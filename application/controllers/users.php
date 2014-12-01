<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/planner_model');
        $this->load->helper('array');

        if ($this->session->userdata('loggedin') != 1) {
            redirect('/');
        }
    }

    public function index() {
        redirect('users/plannerboard');
    }

    public function plannerboard() {

        if ($this->input->get('date') == '') {
            $weekDates = $this->getListWeeks(date('Y-m-d'));
            $this->data['users'] = $this->user->getAllUsers();
            $this->data['weekToday'] = $this->week_of_month(date('Y-m-d H:i:s'));
            $this->data['fullMonth'] = date('F', strtotime(date('Y-m-d H:i:s')));
            $this->data['pageDate'] = date('Y-m-d');
            $this->data['schedData'] = $this->planner_model->getAppointments($weekDates);
            $this->data['weekDates'] = $weekDates;
        
        } else {
            $dateInput = base64_decode($this->input->get('date'));
            $weekDates = $this->getListWeeks($dateInput);
            $this->data['users'] = $this->user->getAllUsers();
            $this->data['weekToday'] = $this->week_of_month($dateInput);
            $this->data['fullMonth'] = date('F', strtotime($dateInput));
            $this->data['pageDate'] = date('Y-m-d', strtotime($dateInput));
            $this->data['schedData'] = $this->planner_model->getAppointments($weekDates);
            $this->data['weekDates'] = $weekDates;            
        }

        $this->load_view('pages/plannerboard', 'Planner Board');
    }

    public function getAppointmentsOnCreateUpdate(){

        $weekDates = $this->getListWeeks($this->input->get('dateFull'));
        $newData = $this->planner_model->getAppointments($weekDates);
        echo json_encode($newData);

    }

    private function getListWeeks($datePassed) {

        $dayNum = date('N', strtotime($datePassed));
        $diffD = $dayNum - 1;
        $firstDayDate = date('Y-m-d', strtotime($datePassed));
        if ($dayNum > 1) {
            $firstDayDate = date('Y-m-d', strtotime($datePassed .'- '.$diffD.' days'));
        }

        $dates = [];
        for($i=0; $i<7; $i++) {
            $dates[] = date('Y-m-d', strtotime($firstDayDate)+86400*$i);
        }
        return $dates;
    }

    public function getWeekDateSched() {
        $dateGet = $this->input->get('date');
        $timeslot = $this->input->get('slot');
        $appVar = $this->planner_model->getSingleAppointment($dateGet, $timeslot);
        echo json_encode($appVar);

    }    

    private function week_of_month($date) {
        $date_parts = explode('-', $date);
        $date_parts[2] = '01';
        $first_of_month = implode('-', $date_parts);
        $day_of_first = date('N', strtotime($first_of_month));
        $day_of_month = date('j', strtotime($date));
        return floor(($day_of_first + $day_of_month - 1) / 7) + 1;
    }    

    public function logout() {
        if($this->user->logoutUser($this->email)) {
            $this->session->sess_destroy();
            $this->db->cache_delete_all();
            redirect('/');            
        }
    }

    public function change_password() {
        $passArray = array(
            'oldpass' => $this->input->post('old-password'),
            'newpass' => $this->input->post('new-password'),
            'conpass' => $this->input->post('confirm-password')
        );
        $changedPass = $this->user->changePassword($passArray);
        if ($changedPass === 'invalid') {
            $dataMessage = array('status' => 'error', 'message' => 'Wrong Old Password');
        } else if ($changedPass === 'not matched') {
            $dataMessage = array('status' => 'error', 'message' => 'New and Confirm Password does not match');
        } else if ($changedPass === true){
            $dataMessage = array('status' => 'success', 'message' => 'Password Updated');
        }
        echo json_encode($dataMessage);
    }

    public function profile($email=null) {
        if (!$email) {
            redirect('users/plannerboard');
        } else {
            $this->data['emailValid'] = $this->user->getUserByEmail($email);
            $this->load_view('pages/profile', 'Profile of '.$email.'');            
        }

    }

    public function addTask(){

        $dateInput = $this->input->post('dp_date');
        $taskData = array(
            'date' => $dateInput,
            'title' => $this->input->post('dp_title'),
            'description' => $this->input->post('dp_description'),
            'duration' => $this->input->post('dp_duration'),
            'color' => $this->input->post('dp_color'),
            'timeslot' => $this->input->post('dp_timeslot'),
            'created_at' => date('Y-m-d H:i:s'),
            'user_id' => $this->userid
        );        

        $pageDate = $this->input->post('dp_pagedate');
        $weekOnPage = date('W', strtotime($pageDate));
        $weekInput = date('W', strtotime($dateInput));
        $insertTask = $this->planner_model->insertTaskData($taskData);
        if ($insertTask) {

            if ($weekOnPage != $weekInput) {
                $dateEncrypt = base64_encode($dateInput);
                $dataMessage = array('status' => 'success', 'message' => 'Task added. But it belongs to other set of weeks.', 'updateThisPage' => 'nope' , 'link' => '/users/plannerboard?date='.$dateEncrypt.'');
            } else {
                $dataMessage = array('status' => 'success', 'message' => 'Task added.', 'updateThisPage' => 'yes');
            }

        } else {
            $dataMessage = array('status' => 'error', 'message' => 'Adding task failed. Try refreshing the page.');
        }
        echo json_encode($dataMessage);
    }

    public function editTask(){

        $dateInput = $this->input->post('dp_date');
        $idPassed = $this->input->post('dp_id');
        $taskData = array(
            'date' => $dateInput,
            'title' => $this->input->post('dp_title'),
            'description' => $this->input->post('dp_description'),
            'duration' => $this->input->post('dp_duration'),
            'color' => $this->input->post('dp_color'),
            'timeslot' => $this->input->post('dp_timeslot'),
            'user_id' => $this->userid
        );        

        $pageDate = $this->input->post('dp_pagedate');
        $weekOnPage = date('W', strtotime($pageDate));
        $weekInput = date('W', strtotime($dateInput));
        $updateTask = $this->planner_model->updateTaskByID($taskData, $idPassed);        
        if ($updateTask) {

            if ($weekOnPage != $weekInput) {
                $dateEncrypt = base64_encode($dateInput);
                $dataMessage = array('status' => 'success', 'message' => 'Task added. But it belongs to other set of weeks.', 'updateThisPage' => 'yes' , 'link' => '/users/plannerboard?date='.$dateEncrypt.'', 'foredit' => 'yes');
            } else {
                $dataMessage = array('status' => 'success', 'message' => 'Task updated.', 'updateThisPage' => 'yes', 'foredit' => 'yes');
            }

        } else {
            $dataMessage = array('status' => 'warning', 'message' => 'Try editing first before saving the task.');
        }
        echo json_encode($dataMessage);
    }    

    public function deleteTask(){

        $idPassed = $this->input->post('dp_id');
        $updateTask = $this->planner_model->updateTaskToDelete($idPassed);        
        if ($updateTask) {
            $dataMessage = array('status' => 'success', 'message' => 'Task deleted.', 'updateThisPage' => 'yes', 'fordeleted' => 'yes');
        } else {
            $dataMessage = array('status' => 'warning', 'message' => 'This task must be deleted already. Try refreshing the page.', 'fordeleted' => 'yes');
        }
        echo json_encode($dataMessage);        

    }

    public function checkDateMinutes() {

        $minutes = $this->planner_model->checkRemainingMinutes();
        $remainingMinutes = 240 - $minutes;
        $dataMessage = array('status' => 'success', 'message' => 'You have '.$remainingMinutes.' minutes remaining for this timeslot.', 'setminutes' => $remainingMinutes);
        echo json_encode($dataMessage);

    }

    public function updateTaskByDrag(){
        $taskData = array(
            'id' => $this->input->post('id'),
            'date' => $this->input->post('date'),
            'timeslot' => $this->input->post('timeslot'),
        );
        $updatedTask = $this->planner_model->updateTaskByDrag($taskData);
        if ($updatedTask) {
            $dataMessage = array('status' => 'success', 'message' => 'Task updated.');
        } else {
            $dataMessage = array('status' => 'error', 'message' => 'Adding task failed. Try refreshing the page.');
        }
        echo json_encode($dataMessage);
    }    

}