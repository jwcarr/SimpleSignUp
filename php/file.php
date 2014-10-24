<?php

class Experiment {

  use File_Opener, File_Writer, Element_Extractor;

  public function __construct($experiment_id) {
    $this->id = $experiment_id;
    $this->owner = $this->extractElement($this->id, $this->openFile('data/experiments.data'));
    $this->data = $this->openFile('data/users/'. $this->owner .'/'. $this->id .'.data');
  }

  public function saveExperimentData() {
    foreach ($this->changed_data as $parameter) {
      $old_piece = $parameter . ' = {' . $this->extractElement($parameter, $this->data) . '}';
      $new_piece = $parameter . ' = {' . $this->$parameter . '}';
      $this->data = str_replace($old_piece, $new_piece, $this->data);
      $this->writeFile('data/users/'. $this->owner .'/'. $this->id .'.data', $this->data);
    }
  }

  public function getName() {
    if (isset($this->name) == False) {
      $this->name = $this->extractElement('name', $this->data);
    }
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    $this->changed_data[] = 'name';
  }

  public function getStatus() {
    if (isset($this->status) == False) {
      $this->status = $this->extractElement('status', $this->data);
    }
    return $this->status;
  }

  public function setStatus($status) {
    $this->status = $status;
    $this->changed_data[] = 'status';
  }

  public function getDescription() {
    if (isset($this->description) == False) {
      $this->description = $this->extractElement('description', $this->data);
    }
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = $description;
    $this->changed_data[] = 'description';
  }

  public function getLocation() {
    if (isset($this->location) == False) {
      $this->location = $this->extractElement('location', $this->data);
    }
    return $this->location;
  }

  public function setLocation($location) {
    $this->location = $location;
    $this->changed_data[] = 'location';
  }

  public function getRequirements() {
    if (isset($this->requirements) == False) {
      $this->requirements = explode('; ', $this->extractElement('requirements', $this->data));
    }
    return $this->requirements;
  }

  public function setRequirements($requirements) {
    $this->requirements = $requirements;
    $this->changed_data[] = 'requirements';
  }

  public function getExclusions() {
    if (isset($this->exclusions) == False) {
      $this->exclusions = explode('; ', $this->extractElement('exclusions', $this->data));
    }
    return $this->exclusions;
  }

  public function setExclusions($exclusions) {
    $this->exclusions = $exclusions;
    $this->changed_data[] = 'exclusions';
  }

  public function getMaxParticipants() {
    if (isset($this->max_participants) == False) {
      $this->max_participants = $this->extractElement('max_participants', $this->data);
    }
    return $this->max_participants;
  }

  public function setMaxParticipants($max_participants) {
    $this->max_participants = $max_participants;
    $this->changed_data[] = 'max_participants';
  }

  public function getPerSlot() {
    if (isset($this->per_slot) == False) {
      $this->per_slot = $this->extractElement('per_slot', $this->data);
    }
    return $this->per_slot;
  }

  public function setPerSlot($per_slot) {
    $this->per_slot = $per_slot;
    $this->changed_data[] = 'per_slot';
  }

  public function getNumOfSlots() {
    if (isset($this->number_of_slots) == False) {
      $this->number_of_slots = $this->extractElement('number_of_slots', $this->data);
    }
    return $this->number_of_slots;
  }

  public function setNumOfSlots($number_of_slots) {
    $this->number_of_slots = $number_of_slots;
    $this->changed_data[] = 'number_of_slots';
  }

  public function getSlotTime() {
    if (isset($this->slot_time) == False) {
      $this->slot_time = $this->extractElement('slot_time', $this->data);
    }
    return $this->slot_time;
  }

  public function setSlotTime($slot_time) {
    $this->slot_time = $slot_time;
    $this->changed_data[] = 'slot_time';
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

  public function setCalendar($calendar) {
    $this->calendar = $calendar;
    $this->changed_data[] = 'calendar';
  }

  public function getExclusionEmails() {
    if (isset($this->exclusion_emails) == False) {
      $this->exclusion_emails = explode('; ', $this->extractElement('exclusion_emails', $this->data));
    }
    return $this->exclusion_emails;
  }

  public function setExclusionEmails($exclusion_emails) {
    $this->exclusion_emails = $exclusion_emails;
    $this->changed_data[] = 'exclusion_emails';
  }

  public function printRequirements() {
    $requirements = $this->getRequirements();
    foreach ($requirements as $requirement) {
      echo '<li>' . $requirement . '</li>';
    }
  }

  public function printRequirementCheckboxes() {
    $requirements = $this->getRequirements();
    $i = 0;
    if ($_REQUEST['name'] == '') {
      foreach ($requirements as $requirement) {
        echo '<p><input type="checkbox" name="confirm" id="require' . $i . '"" /> ' . $requirement . '</p>';
        $i++;
      }
    }
    else {
      foreach ($requirements as $requirement) {
        echo '<p><input type="checkbox" name="confirm" id="require' . $i . '"" checked /> ' . $requirement . '</p>';
        $i++;
      }
    }
    $this->total_requirements = $i;
  }

  private function getAvailableSlots() {
    $this->getSlots();
    $available_slots = array();
    foreach ($this->getCalendar() as $date=>$slots) {
      $unix_date = strtotime($date);
      if ($unix_date-86400 > strtotime(date('Y-m-d'))) {
        foreach ($slots as $slot) {
          $num = count($this->slots[$slot[1]]);
          if ($num < $this->getPerSlot()) {
            $available_slots[$unix_date][$slot[0]] = array($slot[1], $num);
          }
        }
      }
    }
    return $available_slots;
  }

  public function printCalendar() {
    foreach ($this->getAvailableSlots() as $unix_date=>$slots) {
      if (count($slots) > 0) {
        echo '<h3>' . date('l, jS F Y', $unix_date) . '</h3>';
        echo '<table><tr>';
        foreach ($slots as $time=>$slot) {
          echo '<td align="center" width="50px">' . $time . '</td>';
        }
        echo '</tr><tr>';
        foreach ($slots as $time=>$slot) {
          if ($slot[1] == 0) {
            echo '<td align="center"><input type="radio" name="timeslot" value="'. $slot[0] . '" /></td>';
          }
          else {
            echo '<td align="center" style="background-color: #EAF2E8;"><input type="radio" name="timeslot" value="'. $slot[0] . '" /></td>';
          }
        }
        echo '</tr></table>';
      }
    }
  }

  public function printAvailableDates() {
    $available_dates = array();
    foreach ($this->getAvailableSlots() as $unix_date=>$slots) {
      $available_dates[] = date('jS M', $unix_date);
    }
    return implode(', ', $available_dates);
  }

  private function getSlots() {
    $this->slots = array();
    for ($i=1; $i<=$this->getNumOfSlots(); $i++) {
      $this->slots[$i] = $this->getSlot($i);
    }
  }

  public function getSlot($number) {
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

  public function setSlot($slot_id, $name, $email, $phone) {
    $slot_subjects = $this->getSlot($slot_id);
    $slot_subjects[] = array($name, $email, $phone);
    $new_slot_subjects = array();
    foreach ($slot_subjects as $subject) {
      $new_slot_subjects[] = implode(', ', $subject);
    }
    $slot_key = 'slot' . $slot_id;
    $this->$slot_key = implode('; ', $new_slot_subjects);
    $this->changed_data[] = $slot_key;
  }

}

class User {

  use File_Opener, Element_Extractor, Value_Extractor;

  public function __construct($username) {
    $this->username = $username;
    $this->data = $this->extractElement($this->username, $this->openFile('data/users.data'));
  }

  public function getPassword() {
    if (isset($this->password) == False) {
      $this->password = $this->extractValue('password', $this->data);
    }
    return $this->password;
  }

  public function getName() {
    if (isset($this->name) == False) {
      $this->name = $this->extractValue('name', $this->data);
    }
    return $this->name;
  }

  public function getEmail() {
    if (isset($this->email) == False) {
      $this->email = $this->extractValue('email', $this->data);
    }
    return $this->email;
  }

  public function getPhone() {
    if (isset($this->phone) == False) {
      $this->phone = $this->extractValue('phone', $this->data);
    }
    return $this->phone;
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
    return trim($matches[1]);
  }
}

trait Value_Extractor {
  public function extractValue($value, $data) {
    $pattern = '/' . $value . ' = \[(.*?)\]/';
    preg_match($pattern, $data, $matches);
    return trim($matches[1]);
  }

}

?>
