<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Form Validation
| -------------------------------------------------------------------------
| Array containing all form validation directives.
|
|	http://codeigniter.com/user_guide/libraries/form_validation.html
|
*/

$config = array(
                 'login/sign_up' => array(
                                    
                                    array(
                                            'field' => 'login',
                                            'label' => 'Username',
                                            'rules' => 'trim|required|min_length[3]|xss_clean|callback_validate_account'
                                         ),
                                    array(
                                            'field' => 'password',
                                            'label' => 'Password',
                                            'rules' => 'trim|required|xss_clean|matches[passconf]'
                                         ),
                                    array(
                                            'field' => 'passconf',
                                            'label' => 'PasswordConfirmation',
                                            'rules' => 'trim|required'
                                         ),
                                    array(
                                            'field' => 'email',
                                            'label' => 'Email',
                                            'rules' => 'required|xss_clean|valid_email'
                                         )
                                    ),
                 'login/sign_in_simple' => array(
                                    
                                    array(
                                            'field' => 'login',
                                            'label' => 'Username',
                                            'rules' => 'trim|required|min_length[3]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'password',
                                            'label' => 'Password',
                                            'rules' => 'trim|required'
                                         )
                                    ),
                 'admin/account/create' => array(
                                    
                                    array(
                                            'field' => 'username',
                                            'label' => 'lang:username',
                                            'rules' => 'trim|required|min_length[3]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'password',
                                            'label' => 'lang:password',
                                            'rules' => 'trim|required|min_length[8]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'email',
                                            'label' => 'lang:email',
                                            'rules' => 'required|xss_clean|valid_email'
                                         )
                                    ),
                 'admin/account/update' => array(                                    
                                   array(
                                            'field' => 'id',
                                            'label' => 'lang:id',
                                            'rules' => 'trim|required|integer'
                                         ),
                                   array(
                                            'field' => 'username',
                                            'label' => 'lang:username',
                                            'rules' => 'trim|required|min_length[3]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'password',
                                            'label' => 'lang:password',
                                            'rules' => 'trim|required|min_length[8]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'email',
                                            'label' => 'lang:email',
                                            'rules' => 'required|xss_clean|valid_email'
                                         ),
                                        
                                    array(
                                            'field' => 'roles',
                                            'label' => 'lang:roles',
                                            'rules' => 'required|xss_clean|trim'
                                         )
                                    ),
                 'admin/role/create' => array(
                                    
                                		array(
                                            'field' => 'name',
                                            'label' => 'lang:name',
                                            'rules' => 'trim|required|min_length[3]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'description',
                                            'label' => 'lang:description',
                                            'rules' => 'trim|xss_clean'
                                         ),
                                    array(
                                            'field' => 'suspendedon',
                                            'label' => 'lang:suspendedon',
                                            'rules' => 'xss_clean'
                                         )
                                    ),
                 'admin/role/update' => array(                                    
                                   array(
                                            'field' => 'id',
                                            'label' => 'lang:id',
                                            'rules' => 'trim|required|integer'
                                         ),
                                   array(
                                            'field' => 'name',
                                            'label' => 'lang:name',
                                            'rules' => 'trim|required|min_length[3]|xss_clean'
                                         ),
                                    array(
                                            'field' => 'description',
                                            'label' => 'lang:description',
                                            'rules' => 'trim|xss_clean'
                                         ),
                                    array(
                                            'field' => 'suspendedon',
                                            'label' => 'lang:suspendedon',
                                            'rules' => 'xss_clean'
                                         )
                                    ),                                    
                 'group' => array(
                                    array(
                                            'field' => 'gruopname',
                                            'label' => 'GroupName',
                                            'rules' => 'required|alpha'
                                         ),
                                    array(
                                            'field' => 'description',
                                            'label' => 'Description',
                                            'rules' => 'required|alpha'
                                         )

                                    )                          
               );

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */