<?php

class Experiment {

  use File_Opener, Element_Extractor;

  function __construct($experiment_id) {
    $this->id = $experiment_id;
    $this->owner = $this->extractElement($this->id, $this->openFile('data/experiments.data'));
    $this->data = $this->openFile('data/users/'. $this->owner .'/'. $this->id .'.data');
  }

  public function getName() {
    if (isset($this->name) == False) {
      $this->name = $this->extractElement('name', $this->data);
    }
    return $this->name;
  }

  public function getDescription() {
    if (isset($this->description) == False) {
      $this->description = $this->extractElement('description', $this->data);
    }
    return $this->description;
  }

  public function getLocation() {
    if (isset($this->location) == False) {
      $this->location = $this->extractElement('location', $this->data);
    }
    return $this->location;
  }

  public function getRequirements() {
    if (isset($this->requirements) == False) {
      $this->requirements = explode(';', $this->extractElement('requirements', $this->data));
    }
    return $this->requirements;
  }

  public function getExclusions() {
    if (isset($this->exclusions) == False) {
      $this->exclusions = explode(';', $this->extractElement('exclusions', $this->data));
    }
    return $this->exclusions;
  }

  public function getMaxParticipants() {
    if (isset($this->max_participants) == False) {
      $this->max_participants = $this->extractElement('max_participants', $this->data);
    }
    return $this->max_participants;
  }

  public function getPerSlot() {
    if (isset($this->per_slot) == False) {
      $this->per_slot = $this->extractElement('per_slot', $this->data);
    }
    return $this->per_slot;
  }

  public function getSlotTime() {
    if (isset($this->slot_time) == False) {
      $this->slot_time = $this->extractElement('slot_time', $this->data);
    }
    return $this->slot_time;
  }

  public function getCalendar() {
    if (isset($this->calendar) == False) {
      $calendar = explode('; ', $this->extractElement('calendar', $this->data));
      $d = array();
      foreach ($calendar as $date) {
        $date_times = explode(': ', $date);
        $d[$date_times[0]] = explode(', ', $date_times[1]);
      }
      $this->calendar = array();
      foreach ($d as $date=>$slots) {
        $b = array();
        foreach ($d[$date] as $slot) {
          $b[] = explode(' = ', $slot);
        }
        $this->calendar[$date] = $b;
      }
    }
    return $this->calendar;
  }

  public function printRequirements() {
    $requirements = $this->getRequirements();
    foreach ($requirements as $requirement) {
      echo '<li>' . $requirement . '</li>';
    }
  }

  public function printRequirementCheckboxes() {
    $requirements = $this->getRequirements();
    foreach ($requirements as $requirement) {
      echo '<p><input type="checkbox" name="confirm" /> ' . $requirement . '</p>';
    }
  }

public function printCalendar() {
  foreach ($this->getCalendar() as $date=>$slots) {
    echo '<h3>' . $date . '</h3>';
    echo '<table><tr>';
    foreach ($slots as $slot) {
      echo '<td align="center" width="50px">' . $slot[0] . '</td>';
    }
    echo '</tr><tr>';
    foreach ($slots as $slot) {
      $this->getSlots(16);
      $num = count($this->slots[$slot[1]]);
      if ($num == 0) {
        echo '<td align="center"><input type="radio" name="time" value="'. $slot[1] . '|' . $date . '|' . $slot[0] .'" /></td>';
      }
      elseif ($num == $this->getPerSlot()) {
        echo '<td align="center">-</td>';
      }
      else {
        echo '<td align="center" style="background-color: orange;"><input type="radio" name="time" value="'. $slot[1] . '|' . $date . '|' . $slot[0] .'" /></td>';
      }
    }
    echo '</tr></table>';
  }
}

  private function getSlots($number) {
    $this->slots = array();
    for ($i=1; $i<=$number; $i++) {
      $this->slots[$i] = $this->createSlot($i);
    }
  }

  private function createSlot($number) {
    $slot_data = $this->extractElement('slot'.$number, $this->data);
    $participants = explode('; ', $slot_data);
    $subjects = array();
    foreach ($participants as $participant) {
      $subject = explode(', ', $participant);
      if (count($subject) == 3) {
        $subjects[] = $subject;
      }
    }
    return $subjects;
  }

}

class User {

  use File_Opener, Element_Extractor, Value_Extractor;

  function __construct($username) {
    $this->username = $username;
    $userfile = $this->openFile('data/users.data');
    $this->userdata = $this->extractElement($this->username, $userfile);
    $this->password = $this->extractValue('password', $this->userdata);
    $this->name = $this->extractValue('name', $this->userdata);
    $this->email = $this->extractValue('email', $this->userdata);
    $this->phone = $this->extractValue('phone', $this->userdata);
  }
}

trait File_Opener {
  // Open a file
  public function openFile($filename) {
    // If the file exists...
    if (file_exists($filename)) {
      // If the filesize > 0...
      if (filesize($filename) > 0) {
        // Open the file
        $file = fopen($filename, 'r');
        // If you can secure a read lock on the file...
        if (flock($file, LOCK_SH)) {
          // Read the data from the file...
          $data = fread($file, filesize($filename));
          // ... and then unlock, close, and return its contents
          flock($file, LOCK_UN);
          fclose($file);
          return $data;
        }
        // Failure to obtain a lock on the file, so close file and return False
        fclose($file);
        return False;
      }
      // Filesize is 0, so return null
      return '';
    }
    // The file does not exist, so return False
    return False;
  }
}

trait File_Writer {
  // Write data to a file
  public function writeFile($filename, $data) {
    // If the file exists and is writeable...
    if (is_writable($filename)) {
      // Open the file
      $file = fopen($filename, 'w');
      // If you can secure a write lock on the file...
      if (flock($file, LOCK_EX)) {
        // If you succeed in writing the data to the file...
        if (fwrite($file, $data)) {
          // unlock, close, and return True
          flock($file, LOCK_UN);
          fclose($file);
          return True;
        }
        // Failure to write to the file, so unlock
        flock($file, LOCK_UN);
      }
      // Failure to obtain a lock on the file or to write to the file, so close
      fclose($file);
    }
    // The file does not exist or cannot write or cannot obtain lock, so return False
    return False;
  }
}

trait Element_Extractor {

  public function extractElement($element, $data) {
    $pattern = '/' . $element . ' = \{(.*?)\}/';
    preg_match($pattern, $data, $matches);
    return $matches[1];
  }
}

trait Value_Extractor {
  public function extractValue($value, $data) {
    $pattern = '/' . $value . ' = \[(.*?)\]/';
    preg_match($pattern, $data, $matches);
    return $matches[1];
  }

}

?>
