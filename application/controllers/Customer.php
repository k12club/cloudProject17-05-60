<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('pagination');
        $this->load->model('Customer_model');
    }

    public function index() {
        $config = array();
        $config['base_url'] = base_url('customer/index');
        $config['total_rows'] = $this->Customer_model->record_count($this->input->get('keyword'));
        $config['per_page'] = $this->input->get('keyword') == NULL ? 14 : 999;
        $config['uri_segment'] = 3;
        $choice = $config['total_rows'] / $config['per_page'];
        $config['num_links'] = round($choice);

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['results'] = $this->Customer_model->fetch_user($config['per_page'], $page, $this->input->get('keyword'));
        $data['link'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('template/backheader');
        $this->load->view('customer/mainpage', $data);
        $this->load->view('template/backfooter');
    }



    public function newdata() {
        $this->load->view('template/backheader');
        $this->load->view('customer/newdata');
        $this->load->view('template/backfooter');
    }


    /*
    public function register() {
        //$this->load->view('template/backheader');
        $this->load->view('user/register');
        //$this->load->view('template/backfooter');
    }
    public function registernbu() {
        //$this->load->view('template/backheader');
        $this->load->view('user/registernorth');
        //$this->load->view('template/backfooter');
    }
    public function about() {
        //$this->load->view('template/backheader');
        $this->load->view('user/about');
        //$this->load->view('template/backfooter');
    }
    */


    public function postdata() {
        if ($this->input->server('REQUEST_METHOD') == TRUE) {

          if($this->input->post('username')=='admin' && $this->session->userdata('username')!='admin'){
            redirect('dashboard/permission','refresh');
            exit();
          }
            /*
             * อัพโหลดรูปภาพ******************************************************
             */
            $config['upload_path'] = './pictures/customers/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1024 * 1; // 1mb
            $config['overwrite'] = TRUE;
            $config['file_name'] = ($this->input->post('datafile') == '') ? uniqid() : $this->input->post('datafile');

            $this->load->library('upload', $config);
            $no_file_error = "<p>You did not select a file to upload.</p>";
            if (!$this->upload->do_upload('userfile') && $this->upload->display_errors() != $no_file_error) {
                $checkfile = FALSE;
            } else {
                $checkfile = TRUE;
                /*
                 * ปรับขนาดรูปภาพ*************************************************
                 */
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload->upload_path . $this->upload->file_name;
                $config['maintain_ratio'] = FALSE;
                $config['width'] = 160;
                $config['height'] = 160;
                $config['new_image'] = 'user_' . $this->upload->file_name;


                $this->image_lib->initialize($config);
                $this->image_lib->resize();
                $this->image_lib->clear();

                //--------------------------------------------------------------
                @unlink($this->upload->upload_path . $this->upload->file_name);
            }

            //------------------------------------------------------------------
            if ($this->input->post('id') == '') {
                /*$this->form_validation->set_rules('user_type', 'ประเภทผู้ใช้งาน', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('username', 'ชื่อผู้ใช้งาน', 'trim|required|min_length[5]|max_length[12]|is_unique[users.username]|alpha_numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'min_length' => 'ต้องมากกว่า 4 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 13 ตัวอักษรลงไป',
                    'is_unique' => 'มีชื่อผู้ใช้งานอยู่ในระบบแล้ว',
                    'alpha_numeric' => 'ต้องเป็นตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้น'
                ));
                $this->form_validation->set_rules('password', 'รหัสผ่านเข้าใช้งาน', 'trim|required|min_length[6]|max_length[20]|alpha_numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'min_length' => 'ต้องมากกว่า 5 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 21 ตัวอักษรลงไป',
                    'alpha_numeric' => 'ต้องเป็นตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้น'
                ));*/
                $this->form_validation->set_rules('firstname', 'ชื่อจริง', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('lastname', 'นามสกุล', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('phone', 'เบอร์โทรศัพท์', 'trim|required|min_length[10]|max_length[10]|numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'min_length' => 'ต้องมากกว่า 9 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 11 ตัวอักษรลงไป',
                    'numeric' => 'ต้องตัวเลข 0 - 9 เท่านั้น'
                ));
                $this->form_validation->set_rules('email', 'อีเมล์', 'trim|required|valid_email', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'valid_email' => 'รูปแบบอีเมล์ไม่ถูกต้อง'
                ));
                //$this->form_validation->set_rules('Facebook', 'Facebook', 'required', array('required' => 'ค่าห้ามว่าง!'));
                //$this->form_validation->set_rules('line', 'line', 'required', array('required' => 'ค่าห้ามว่าง!'));
                //$this->form_validation->set_rules('instargram', 'instargram', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('Address', 'Address', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('City', 'City', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('State', 'State', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('Postal_Code', 'Postal_Code', 'required|numeric', array(
                    'required' => 'ค่าห้ามว่าง!',
                    'numeric' => 'เป็นตัวเลขเท่านั้น!'
                    ));
                $this->form_validation->set_rules('Country', 'Country', 'required', array('required' => 'ค่าห้ามว่าง!'));
                


            } else {
                /*$this->form_validation->set_rules('password', 'รหัสผ่านเข้าใช้งาน', 'trim|min_length[6]|max_length[20]|alpha_numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'min_length' => 'ต้องมากกว่า 5 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 21 ตัวอักษรลงไป',
                    'alpha_numeric' => 'ต้องเป็นตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้น'
                ));*/
                $this->form_validation->set_rules('firstname', 'ชื่อจริง', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('lastname', 'นามสกุล', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('phone', 'เบอร์โทรศัพท์', 'trim|required|min_length[10]|max_length[10]|numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'min_length' => 'ต้องมากกว่า 9 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 11 ตัวอักษรลงไป',
                    'numeric' => 'ต้องตัวเลข 0 - 9 เท่านั้น'
                ));
                $this->form_validation->set_rules('email', 'อีเมล์', 'trim|required|valid_email', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'valid_email' => 'รูปแบบอีเมล์ไม่ถูกต้อง'
                ));
                //$this->form_validation->set_rules('Facebook', 'Facebook', 'required', array('required' => 'ค่าห้ามว่าง!'));
                //$this->form_validation->set_rules('line', 'line', 'required', array('required' => 'ค่าห้ามว่าง!'));
                //$this->form_validation->set_rules('instargram', 'instargram', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('Address', 'Address', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('City', 'City', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('State', 'State', 'required', array('required' => 'ค่าห้ามว่าง!'));
                $this->form_validation->set_rules('Postal_Code', 'Postal_Code', 'required|numeric', array(
                    'required' => 'ค่าห้ามว่าง!',
                    'numeric' => 'เป็นตัวเลขเท่านั้น!'
                    ));
                $this->form_validation->set_rules('Country', 'Country', 'required', array('required' => 'ค่าห้ามว่าง!'));
            }


            if ($this->form_validation->run() == TRUE && $checkfile == TRUE) {
                $this->session->set_flashdata(
                        array(
                            'msginfo' => '<div class="pad margin no-print"><div style="margin-bottom: 0!important;" class="callout callout-info"><h4><i class="fa fa-info"></i> ข้อความจากระบบ</h4>ทำรายการสำเร็จ</div></div>'
                        )
                );

                $data = $this->upload->data();
                if ($_FILES['userfile']['name'] <> '') {
                    $datafile = ($this->input->post('datafile') == '') ? $data['file_name'] : $this->input->post('datafile');
                } else {
                    $datafile = ($this->input->post('id') == '') ? '' : $this->input->post('datafile');
                }


                $this->Customer_model->entry_user($this->input->post('id'), $datafile);


                redirect('customer', 'refresh');
            } else {
                $data = array(
                    /*'error_user_type' => form_error('user_type'),
                    'username' => set_value('username'),
                    'error_username' => form_error('username'),
                    'password' => set_value('password'),
                    'error_password' => form_error('password'),*/
                    'firstname' => set_value('firstname'),
                    'error_firstname' => form_error('firstname'),
                    'lastname' => set_value('lastname'),
                    'error_lastname' => form_error('lastname'),
                    'phone' => set_value('phone'),
                    'error_phone' => form_error('phone'),
                    'email' => set_value('email'),
                    'error_email' => form_error('email'),
                    'Address' => set_value('Address'),
                    'error_Address' => form_error('Address'),
                    'City' => set_value('City'),
                    'error_City' => form_error('City'),
                    'State' => set_value('State'),
                    'error_State' => form_error('State'),
                    'Postal_Code' => set_value('Postal_Code'),
                    'error_Postal_Code' => form_error('Postal_Code'),
                    'Country' => set_value('Country'),
                    'error_Country' => form_error('Country'),
                    'err_filename' => form_error('filename'),
                    'err_filename' => $this->upload->display_errors()
                    
                );

                $this->session->set_flashdata($data);

                if ($_FILES['userfile']['name'] <> '' && $this->input->post('datafile') == '') {
                    @unlink($this->upload->upload_path . "user_" . $this->upload->file_name);
                }
            }
            if ($this->input->post('id') == NULL) {
                redirect('customer/newdata');
            } else {
                redirect('customer/edit/' . $this->input->post('id'));
            }
        }
    }

    public function new_user() {
        if ($this->input->server('REQUEST_METHOD') == TRUE) {

          if($this->input->post('username')=='admin' && $this->session->userdata('username')!='admin'){
            redirect('dashboard/permission','refresh');
            exit();
          }

          
            /*
             * อัพโหลดรูปภาพ******************************************************
             */

            
            $config['upload_path'] = './pictures/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1024 * 1; // 1mb
            $config['overwrite'] = TRUE;
            $config['file_name'] = ($this->input->post('datafile') == '') ? uniqid() : $this->input->post('datafile');
        

            
            $this->load->library('upload', $config);
            $no_file_error = "<p>You did not select a file to upload.</p>";
            if (!$this->upload->do_upload('userfile') && $this->upload->display_errors() != $no_file_error) {
                $checkfile = FALSE;
            } else {
                $checkfile = TRUE;
                
                 // ปรับขนาดรูปภาพ*************************************************
                 
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload->upload_path . $this->upload->file_name;
                $config['maintain_ratio'] = FALSE;
                $config['width'] = 160;
                $config['height'] = 160;
                $config['new_image'] = 'user_' . $this->upload->file_name;


                $this->image_lib->initialize($config);
                $this->image_lib->resize();
                $this->image_lib->clear();

                //--------------------------------------------------------------
                @unlink($this->upload->upload_path . $this->upload->file_name);
            } 

            


            //------------------------------------------------------------------
            if ($this->input->post('id') == '') {
                //$this->form_validation->set_rules('user_type', 'ประเภทผู้ใช้งาน', 'required', array('required' => 'ค่าห้ามว่าง!'));

                $this->form_validation->set_rules('username', 'ชื่อผู้ใช้งาน', 'trim|required|min_length[5]|max_length[12]|is_unique[users.username]|alpha_numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'min_length' => 'ต้องมากกว่า 4 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 13 ตัวอักษรลงไป',
                    'is_unique' => 'มีชื่อผู้ใช้งานอยู่ในระบบแล้ว',
                    'alpha_numeric' => 'ต้องเป็นตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้น'
                ));
                $this->form_validation->set_rules('password', 'รหัสผ่านเข้าใช้งาน', 'trim|required|min_length[6]|max_length[20]|alpha_numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'required' => 'ค่าห้ามว่าง',
                    'min_length' => 'ต้องมากกว่า 5 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 21 ตัวอักษรลงไป',
                    'alpha_numeric' => 'ต้องเป็นตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้น'
                ));
                $this->form_validation->set_rules('firstname', 'ชื่อจริง', 'required', array('required' => 'ค่าห้ามว่าง!'));
            } else {
                $this->form_validation->set_rules('password', 'รหัสผ่านเข้าใช้งาน', 'trim|min_length[6]|max_length[20]|alpha_numeric', array(
                    'trim' => 'มีค่าว่าง',
                    'min_length' => 'ต้องมากกว่า 5 ตัวอักษรขึ้นไป',
                    'max_length' => 'ต้องน้อยกว่า 21 ตัวอักษรลงไป',
                    'alpha_numeric' => 'ต้องเป็นตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้น'
                ));
                $this->form_validation->set_rules('firstname', 'ชื่อจริง', 'required', array('required' => 'ค่าห้ามว่าง!'));
            }


            if ($this->form_validation->run() == TRUE && $checkfile == TRUE) {
                $this->session->set_flashdata(
                        array(
                            'msginfo' => '<div class="pad margin no-print"><div style="margin-bottom: 0!important;" class="callout callout-info"><h4><i class="fa fa-info"></i> ข้อความจากระบบ</h4>ทำรายการสำเร็จ</div></div>'
                        )
                );

                $data = $this->upload->data();
                if ($_FILES['userfile']['name'] <> '') {
                    $datafile = ($this->input->post('datafile') == '') ? $data['file_name'] : $this->input->post('datafile');
                } else {
                    $datafile = ($this->input->post('id') == '') ? '' : $this->input->post('datafile');
                }


                $this->User_model->add_user($this->input->post('id'), $datafile);


                redirect('user/login', 'refresh');
            } else {
                $data = array(
                    'error_user_type' => form_error('user_type'),
                    'username' => set_value('username'),
                    'error_username' => form_error('username'),
                    'password' => set_value('password'),
                    'error_password' => form_error('password'),
                    'firstname' => set_value('firstname'),
                    'error_firstname' => form_error('firstname'),
                    'err_filename' => form_error('filename'),
                    //'err_filename' => $this->upload->display_errors()
                );
                $this->session->set_flashdata($data);

                if ($_FILES['userfile']['name'] <> '' && $this->input->post('datafile') == '') {
                    @unlink($this->upload->upload_path . "user_" . $this->upload->file_name);
                } 
            }
            if ($this->input->post('id') == NULL) {
                redirect('user/register');
            } else {
                redirect('user/edit/' . $this->input->post('id'));
            }
        }
    }



    public function edit($id) {
        $data['customer'] = $this->Customer_model->read_customer($id);
        $this->load->view('template/backheader');
        $this->load->view('customer/edit', $data);
        $this->load->view('template/backfooter');
    }

    public function read($id) {
        $data['customer'] = $this->Customer_model->read_customer($id);
        $this->load->view('template/backheader');
        $this->load->view('customer/address', $data);
        $this->load->view('template/backfooter');
    }

    public function remove($id) {
      if($this->session->userdata('username')!='admin'){
        redirect('dashboard/permission','refresh');
        exit();
      }
        $result = $this->Customer_model->read_customer($id);
        @unlink('./pictures/view_' . $result->filename);
        @unlink('./pictures/thumb_' . $result->filename);
        @unlink('./pictures/sm_' . $result->filename);

        $this->Customer_model->remove_customer($id);
        redirect('customer', 'refresh');
    }

    public function validlogin() {
        if ($this->input->server('REQUEST_METHOD') == TRUE) {
            if ($this->User_model->record_login($this->input->post('username'), $this->input->post('password')) == 1) {
                $result = $this->User_model->fetch_user_login($this->input->post('username'), $this->input->post('password'));
                $this->session->set_userdata(array('login_id' => $result->id, 'username' => $result->username, 'firstname' => $result->firstname, 'lastname' => $result->lastname,'img'=>$result->filename,'user_type'=>$result->user_type));
                redirect('dashboard','refresh');
            } else {
                $this->session->set_flashdata(array('msgerr' => '<p class="login-box-msg" style="color:red;">ชื่อผู้ใช้หรือรหัสผ่านผิดพลาด!</p>'));
                redirect('user/login', 'refresh');
            }
        }
    }

    public function logout() {
        $this->session->unset_userdata(array('login_id', 'username', 'firstname','lastname','img','user_type'));
        redirect('', 'refresh');
    }

    public function login_old() {

        $this->load->view('user/login');
    }
    public function login() {

        $this->load->view('user/main');
    }

}
