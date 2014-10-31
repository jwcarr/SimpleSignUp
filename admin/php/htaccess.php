<?php

class HTaccess {

  private $filename = '../../../server_data/ssu/htpasswd';
  private $data = '';
  private $passwords = [];

  public function __construct() {
    $this->data = $this->openPasswordFile();
    $this->passwords = $this->loadPasswords();
  }

  public function changePassword($username, $password, $new_password) {
    if (password_verify($password, $this->passwords[$username])) {
      $this->passwords[$username] = password_hash($new_password, PASSWORD_DEFAULT);
      if ($this->overwrite()) {
        return True;
      }
    }
    return False;
  }

  private function loadPasswords() {
    $lines = explode("\n", $this->data);
    foreach ($lines as $line) {
      $user = explode(':', $line);
      $passwords[$user[0]] = $user[1];
    }
    return $passwords;
  }

  private function openPasswordFile() {
    if (file_exists($this->filename)) {
      for ($i=0; $i<5; $i++) {
        if (is_writable($this->filename)) {
          $this->file = fopen($this->filename, 'a+');
          if ($this->file != False) {
            if (flock($this->file, LOCK_EX)) {
              $data = trim(fread($this->file, filesize($this->filename)));
              return $data;
            }
            fclose($this->file);
          }
        }
        sleep(2);
      }
    }
    return False;
  }

  private function overwrite() {
    foreach ($this->passwords as $username=>$password) {
      $new_data .= $username . ':' . $password . "\n";
    }
    if (ftruncate($this->file, 0)) {
      if (fwrite($this->file, $new_data)) {
        flock($this->file, LOCK_UN);
        fclose($this->file);
        return True;
      }
    }
    flock($this->file, LOCK_UN);
    fclose($this->file);
    return False;
  }

}

?>
