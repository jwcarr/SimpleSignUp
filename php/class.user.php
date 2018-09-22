<?php

include_once('class.file.php');
include_once('class.experiment.php');

class User {

  private $valid_user = False;
  private $changed_data = array();

  public function __construct($username, $write_access=False) {
    global $data_path;
    $this->username = $username;
    $this->users_file = new File($data_path .'users', $write_access);
    $this->data = $this->extractElement($this->username, $this->users_file->data);
    if ($this->data != '') { $this->valid_user = True; }
  }

  public function authorize($password, $using_hash) {
    if ($this->valid_user == True) {
      $check_result = $this->checkPassword($password, $using_hash);
      if ($check_result == True) {
        return $check_result;
      }
    }
    return False;
  }

  private function checkPassword($password, $using_hash) {
    global $new_hash_method;
    if ($using_hash) {
      if ($password == $this->getPassword()) { return True; }
    }
    else {
      if ($new_hash_method) {
        if (password_verify($password, $this->getPassword())) { return $this->getPassword(); }
      }
      else {
        if ($this->getPassword() == crypt($password, $this->getPassword())) { return $this->getPassword(); }
      }
    }
    return False;
  }

  public function saveUserDetails() {
    if (count($this->changed_data) > 0) {
      $new_user_data = $this->data;
      foreach ($this->changed_data as $parameter) {
        $old_piece = $parameter . ' = [' . $this->extractValue($parameter, $this->data) . ']';
        if ($parameter == 'experiments' OR $parameter == 'shares') { $new_piece = $parameter . ' = [' . implode(', ', $this->$parameter) . ']'; }
        else { $new_piece = $parameter . ' = [' . $this->$parameter . ']'; }
        $new_user_data = str_replace($old_piece, $new_piece, $new_user_data);
      }
      $old_piece = $this->username . ' = { ' . $this->data . ' }';
      $new_piece = $this->username . ' = { ' . $new_user_data . ' }';
      $pattern = '/^' . $this->username . '\s=\s\{\sname\s=\s\[.+\],\semail\s=\s\[.+\],\sphone\s=\s\[.*\],\sexperiments\s=\s\[.*\],\sshares\s=\s\[.*\],\spassword\s=\s\[.+\]\s\}$/';
      if (preg_match($pattern, $new_piece, $matches) == 0) {
        return false;
      }
      $this->users_file->data = str_replace($old_piece, $new_piece, $this->users_file->data);
      return $this->users_file->overwrite();
    }
    return True;
  }

  public function getName() {
    if (isset($this->name) == False) {
      $this->name = $this->extractValue('name', $this->data);
    }
    return $this->name;
  }

  public function setName($name) {
    if ($name != $this->getName()) {
      $this->name = str_replace("'", '’', trim($name));
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
      $this->email = trim($email);
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
      $this->phone = trim($phone);
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
    global $new_hash_method;
    if ($new_hash_method == True) {
      $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    else {
      $this->password = crypt($password);
    }
    $this->changed_data[] = 'password';
    return $this->password;
  }

  public function getExperiments() {
    if (isset($this->experiments) == False) {
      $this->experiments = explode(', ', $this->extractValue('experiments', $this->data));
      if ($this->experiments[0] == '') {
        $this->experiments = array();
      }
    }
    return array_reverse($this->experiments);
  }

  public function addExperiment($code_name) {
    $this->getExperiments();
    if (in_array($code_name, $this->experiments) == False) {
      $this->experiments[] = $code_name;
      $this->changed_data[] = 'experiments';
    }
  }

  public function removeExperiment($code_name) {
    $this->getExperiments();
    if (($key = array_search($code_name, $this->experiments)) !== False) {
      unset($this->experiments[$key]);
    }
    $this->changed_data[] = 'experiments';
  }

  public function getSharedExperiments() {
    if (isset($this->shares) == False) {
      $this->shares = explode(', ', $this->extractValue('shares', $this->data));
      if ($this->shares[0] == '') {
        $this->shares = array();
      }
    }
    return array_reverse($this->shares);
  }

  public function addSharedExperiment($code_name) {
    $this->getSharedExperiments();
    if (in_array($code_name, $this->shares) == False) {
      $this->shares[] = $code_name;
      $this->changed_data[] = 'shares';
    }
  }

  public function removeSharedExperiment($code_name) {
    $this->getSharedExperiments();
    if (($key = array_search($code_name, $this->shares)) !== False) {
      unset($this->shares[$key]);
      $this->changed_data[] = 'shares';
    }
  }

  private function getExperimentObjects($experiments, $type) {
    $objects = array();
    if ($type == 'mine') { $owner = $this->username; } else { $owner = False; }
    foreach ($experiments as $experiment) {
      $objects[] = new Experiment($experiment, False, $owner);
    }
    return $objects;
  }

  public function printExperimentList($status, $type) {
    if ($type == 'mine') {
      $experiments = $this->getExperiments();
    }
    else {
      $experiments = $this->getSharedExperiments();
    }
    $objects = $this->getExperimentObjects($experiments, $type);
    foreach ($objects as $experiment) {
      if ($experiment->getStatus() == $status AND $type == 'mine') {
        $list .= '<li><a href="index.php?page=view&exp='. $experiment->id .'">'. $experiment->getName() .'</a></li>';
      }
      elseif ($experiment->getStatus() == $status AND $type == 'shared') {
        $list .= '<li><a href="index.php?page=view&exp='. $experiment->id .'">'. $experiment->getName() . ' (' . $experiment->owner . ')</a></li>';
      }
    }
    if (isset($list)) {
      echo '<ul>'. $list .'</ul>';
    }
    else {
      echo "<p>None</p>";
    }
  }

  public function getAllUsernames() {
    $usernames = explode('; ', $this->extractElement('all_usernames', $this->users_file->data));
    sort($usernames);
    return $usernames;
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
