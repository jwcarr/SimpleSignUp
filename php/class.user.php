<?php

include_once('class.file.php');
include_once('class.experiment.php');

class User {

  private $new_hash_method = True; // Set to True if using PHP >= 5.5. Uses password_hash() instead of crypt()
  private $valid_user = False;

  public function __construct($username, $write_access=False) {
    global $data_path;
    $this->username = $username;
    $this->users_file = new File($data_path .'users', $write_access);
    $this->data = $this->extractElement($this->username, $this->users_file->data);
    if ($this->data != '') { $this->valid_user = True; }
  }

  public function authorize($password, $using_hash) {
    $check_result = $this->checkPassword($password, $using_hash);
    if ($this->valid_user == True) {
      if ($check_result == True) {
        return $check_result;
      }
    }
    return False;
  }

  private function checkPassword($password, $using_hash) {
    if ($using_hash) {
      if ($password == $this->getPassword()) { return True; }
    }
    else {
      if ($this->new_hash_method) {
        if (password_verify($password, $this->getPassword())) { return True; }
      }
      else {
        if ($target_password == crypt($password, $this->getPassword())) { return True; }
      }
    }
    return False;
  }

  public function saveUserDetails() {
    $new_user_data = $this->data;
    foreach ($this->changed_data as $parameter) {
      $old_piece = $parameter . ' = [' . $this->extractValue($parameter, $this->data) . ']';
      $new_piece = $parameter . ' = [' . $this->$parameter . ']';
      $new_user_data = str_replace($old_piece, $new_piece, $new_user_data);
    }
    $old_piece = $this->username . ' = { ' . $this->data . ' }';
    $new_piece = $this->username . ' = { ' . $new_user_data . ' }';
    $this->users_file->data = str_replace($old_piece, $new_piece, $this->users_file->data);
    return $this->users_file->overwrite();
  }

  public function getName() {
    if (isset($this->name) == False) {
      $this->name = $this->extractValue('name', $this->data);
    }
    return $this->name;
  }

  public function setName($name) {
    if ($name != $this->getName()) {
      $this->name = $name;
      $this->changed_data[] = 'name';
    }
  }

  public function getEmail() {
    if (isset($this->email) == False) {
      $this->email = $this->extractValue('email', $this->data);
    }
    return $this->email;
  }

  public function setEmail($email) {
    if ($email != $this->getName()) {
      $this->email = $email;
      $this->changed_data[] = 'email';
    }
  }

  public function getPhone() {
    if (isset($this->phone) == False) {
      $this->phone = $this->extractValue('phone', $this->data);
    }
    return $this->phone;
  }

  public function setPhone($phone) {
    if ($phone != $this->getPhone()) {
      $this->phone = $phone;
      $this->changed_data[] = 'phone';
    }
  }

  public function getPassword() {
    if (isset($this->password) == False) {
      $this->password = $this->extractValue('password', $this->data);
    }
    return $this->password;
  }

  public function setPassword($password) {
    if ($password != $this->getPassword()) {
      $this->password = $password;
      $this->changed_data[] = 'password';
    }
  }

  public function getExperiments() {
    if (isset($this->experiments) == False) {
      $this->experiments = explode(', ', $this->extractValue('experiments', $this->data));
      if ($this->experiments[0] == '') {
        $this->experiments = array();
      }
    }
    return $this->experiments;
  }

  public function addExperiment($code_name) {
    $this->getExperiments();
    $this->experiments[] = $code_name;
    $this->changed_data[] = 'experiments';
  }

  private function getExperimentObjects() {
    if (isset($this->experiment_objects) == False) {
      $this->experiment_objects = array();
      foreach ($this->getExperiments() as $experiment) {
        $this->experiment_objects[] = new Experiment($experiment, False);
      }
    }
  }

  public function printExperimentList($status) {
    if (isset($this->experiment_objects) == False) {
      $this->getExperimentObjects();
    }
    if (count($this->experiment_objects) > 0) {
      echo '<ul>';
      foreach ($this->experiment_objects as $experiment) {
        if ($experiment->getStatus() == $status) {
          echo '<li><a href="index.php?page=view&exp='. $experiment->id .'">'. $experiment->getName() .'</a></li>';
        }
      }
      echo '</ul>';
    }
    else {
      echo "<p>None</p>";
    }
  }

  private function extractElement($element, $data) {
    $pattern = '/' . $element . ' = \{(.*?)\}/s';
    preg_match($pattern, $data, $matches);
    return trim($matches[1]);
  }

  private function extractValue($value, $data) {
    $pattern = '/' . $value . ' = \[(.*?)\]/s';
    preg_match($pattern, $data, $matches);
    return trim($matches[1]);
  }

}

?>
