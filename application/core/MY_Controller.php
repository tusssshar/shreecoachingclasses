<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {}

/**
 * Base for the teacher / parent / student portals: login check for one role,
 * menu-permission guard (Settings > Menu Permissions) and small helpers.
 */
class Portal_Controller extends CI_Controller
{
    public $exam_model;
    public $portal_model;

    /** login_type of this portal: teacher | parent | student */
    protected $role = '';
    /** URL segment of the controller: teacher | parents | student */
    protected $base = '';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        if ($this->session->userdata($this->role . '_login') != 1) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('ok' => false, 'error' => 'session_expired'));
                exit;
            }
            redirect(base_url() . 'index.php?login', 'refresh');
        }
        $this->load->model('exam_model');
        $this->load->model('portal_model');
        $this->exam_model->ensure_schema();
    }

    /** Logged-in user's id (teacher_id / parent_id / student_id). */
    protected function me()
    {
        return (int)$this->session->userdata($this->role . '_id');
    }

    protected function can($menu)
    {
        return sms_menu_allowed($this->portal_model->permissions(), $this->role, $menu);
    }

    /** Stop with a friendly page when the menu is switched off for this role. */
    protected function allow($menu)
    {
        if ($this->can($menu)) return;
        $this->session->set_flashdata('error_message', get_phrase('you_do_not_have_access_to_this_page'));
        redirect(base_url() . 'index.php?' . $this->base . '/dashboard', 'refresh');
    }

    protected function back($url, $message, $error = false)
    {
        $this->session->set_flashdata($error ? 'error_message' : 'flash_message', $message);
        redirect(base_url() . 'index.php?' . $this->base . '/' . $url, 'refresh');
    }

    protected function json($data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /** Render a page inside the normal layout. $page may point to a shared view (../portal/x). */
    protected function render($page, $title, $data = array())
    {
        $data['page_name']   = $page;
        $data['page_title']  = $title;
        $data['portal_base'] = base_url() . 'index.php?' . $this->base . '/';
        $data['portal_role'] = $this->role;
        $this->load->view('backend/index', $data);
    }

    /** Change own password (accounts may store bcrypt or legacy plain text). */
    protected function change_password($table, $id_field)
    {
        $row = $this->db->get_where($table, array($id_field => $this->me()))->row();
        $error = sms_password_change_error($row ? $row->password : '', $this->input->post('password'),
            $this->input->post('new_password'), $this->input->post('confirm_new_password'));
        if ($error) $this->back('manage_profile', get_phrase($error), true);
        $this->db->where($id_field, $this->me())->update($table, array('password' => password_hash($this->input->post('new_password'), PASSWORD_BCRYPT)));
        $this->back('manage_profile', get_phrase('password_updated'));
    }
}
