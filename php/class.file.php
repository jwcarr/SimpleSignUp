<?php

class File {

  private $filename = '';
  private $write_access = False;
  public $data = '';

  public function __construct($filename, $write_access=False) {
    $this->filename = $filename;
    $this->write_access = $write_access;
    $this->data = $this->openFile();
  }

  public function __destruct() {
    if ($this->write_access) {
      flock($this->file, LOCK_UN);
      fclose($this->file);
    }
  }

  private function openFile() {
    if ($this->write_access) {
      return $this->openFileWithWriteAccess();
    }
    else {
      return $this->openFileWithoutWriteAccess();
    }
  }

  private function openFileWithoutWriteAccess() {
    if (file_exists($this->filename)) {
      $this->file = fopen($this->filename, 'r');
      if ($this->file != False) {
        if (flock($this->file, LOCK_SH)) {
          $data = fread($this->file, filesize($this->filename));
          flock($this->file, LOCK_UN);
          fclose($this->file);
          return $data;
        }
        fclose($this->file);
      }
    }
    return False;
  }

  private function openFileWithWriteAccess() {
    if (file_exists($this->filename)) {
      if (is_writable($this->filename)) {
        $this->file = fopen($this->filename, 'a+');
        if (filesize($this->filename) == 0) {
          return '';
        }
        else {
          for ($i=0; $i<10; $i++) {
            if (flock($this->file, LOCK_EX)) {
              $data = fread($this->file, filesize($this->filename));
              return $data;
            }
            sleep(1);
          }
        }
        fclose($this->file);
      }
      $this->write_access = False;
      return $this->openFileWithoutWriteAccess();
    }
    return False;
  }

  public function overwrite() {
    if ($this->write_access) {
      if (ftruncate($this->file, 0)) {
        if (fwrite($this->file, $this->data)) {
          flock($this->file, LOCK_UN);
          fclose($this->file);
          $this->write_access = False;
          return True;
        }
      }
      flock($this->file, LOCK_UN);
      fclose($this->file);
    }
    $this->write_access = False;
    return False;
  }

}

?>
