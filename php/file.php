<?php

class Experiment {

  use Element_Extractor;

  public function __construct($experiment_id, $write_access=False) {
    $this->id = $experiment_id;

    $experiments_file = new File('data/experiments.data', False);
    $this->owner = $this->extractElement($this->id, $experiments_file->data);

    $this->file = new File('data/users/'. $this->owner .'/'. $this->id .'.data', $write_access);
  }

  public function saveExperimentData() {
    foreach ($this->changed_data as $parameter) {
      $old_piece = $parameter . ' = {' . $this->extractElement($parameter, $this->file->data) . '}';
      $new_piece = $parameter . ' = {' . $this->$parameter . '}';
      $this->file->data = str_replace($old_piece, $new_piece, $this->file->data);
    }
    return $this->file->overwrite();
  }

  public function getName() {
    if (isset($this->name) == False) {
      $this->name = $this->extractElement('name', $this->file->data);
    }
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    $this->changed_data[] = 'name';
  }

  public function getStatus() {
    if (isset($this->status) == False) {
      $this->status = $this->extractElement('status', $this->file->data);
      if ($this->status == 'open') {
        if (count($this->getAvailableSlots()) == 0) {
          $this->status = 'unavailable';
        }
      }
    }
    return $this->status;
  }

  public function setStatus($status) {
    $this->status = $status;
    $this->changed_data[] = 'status';
  }

  public function getDescription() {
    if (isset($this->description) == False) {
      $this->description = $this->extractElement('description', $this->file->data);
    }
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = $description;
    $this->changed_data[] = 'description';
  }

  public function getLocation() {
    if (isset($this->location) == False) {
      $this->location = $this->extractElement('location', $this->file->data);
    }
    return $this->location;
  }

  public function setLocation($location) {
    $this->location = $location;
    $this->changed_data[] = 'location';
  }

  public function getRequirements() {
    if (isset($this->requirements) == False) {
      $this->requirements = explode('; ', $this->extractElement('requirements', $this->file->data));
    }
    return $this->requirements;
  }

  public function setRequirements($requirements) {
    $this->requirements = $requirements;
    $this->changed_data[] = 'requirements';
  }

  public function getExclusions() {
    if (isset($this->exclusions) == False) {
      $this->exclusions = explode('; ', $this->extractElement('exclusions', $this->file->data));
    }
    return $this->exclusions;
  }

  public function setExclusions($exclusions) {
    $this->exclusions = $exclusions;
    $this->changed_data[] = 'exclusions';
  }

  public function getMaxParticipants() {
    if (isset($this->max_participants) == False) {
      $this->max_participants = $this->extractElement('max_participants', $this->file->data);
    }
    return $this->max_participants;
  }

  public function setMaxParticipants($max_participants) {
    $this->max_participants = $max_participants;
    $this->changed_data[] = 'max_participants';
  }

  public function getPerSlot() {
    if (isset($this->per_slot) == False) {
      $this->per_slot = $this->extractElement('per_slot', $this->file->data);
    }
    return $this->per_slot;
  }

  public function setPerSlot($per_slot) {
    $this->per_slot = $per_slot;
    $this->changed_data[] = 'per_slot';
  }

  public function getNumOfSlots() {
    if (isset($this->number_of_slots) == False) {
      $this->number_of_slots = $this->extractElement('number_of_slots', $this->file->data);
    }
    return $this->number_of_slots;
  }

  public function setNumOfSlots($number_of_slots) {
    $this->number_of_slots = $number_of_slots;
    $this->changed_data[] = 'number_of_slots';
  }

  public function getSlotTime() {
    if (isset($this->slot_time) == False) {
      $this->slot_time = $this->extractElement('slot_time', $this->file->data);
    }
    return $this->slot_time;
  }

  public function setSlotTime($slot_time) {
    $this->slot_time = $slot_time;
    $this->changed_data[] = 'slot_time';
  }

  public function getCalendar() {
    if (isset($this->calendar) == False) {
      $calendar = explode('; ', $this->extractElement('calendar', $this->file->data));
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
      $this->exclusion_emails = explode('; ', $this->extractElement('exclusion_emails', $this->file->data));
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

  public function getAvailableSlots() {
    if (isset($this->available_slots) == False) {
      $this->getSlots();
      $this->available_slots = array();
      foreach ($this->getCalendar() as $date=>$slots) {
        $unix_date = strtotime($date);
        if ($unix_date-86400 > strtotime(date('Y-m-d'))) {
          foreach ($slots as $slot) {
            $num = count($this->slots[$slot[1]]);
            if ($num < $this->getPerSlot()) {
              $this->available_slots[$unix_date][$slot[0]] = array($slot[1], $num);
            }
          }
        }
      }
    }
    return $this->available_slots;
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
    $slot_data = $this->extractElement('slot'.$number, $this->file->data);
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

  use Element_Extractor, Value_Extractor;

  public function __construct($username) {
    $this->username = $username;
    $users_file = new File('data/users.data', False);
    $this->data = $this->extractElement($this->username, $users_file->data);
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

class File {

  private $filename = '';
  private $write_access = False;
  public $data = '';

  public function __construct($filename, $write_access=False) {
    $this->filename = $filename;
    $this->write_access = $write_access;
    if ($this->write_access) { $this->data = $this->openFileWithWriteAccess(); }
    else { $this->data = $this->openFileWithoutWriteAccess(); }
  }

  public function __destruct() {
    if ($this->write_access) {
      flock($this->file, LOCK_UN);
      fclose($this->file);
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
      for ($i=0; $i<5; $i++) {
        if (is_writable($this->filename)) {
          $this->file = fopen($this->filename, 'c+');
          if ($this->file != False) {
            if (flock($this->file, LOCK_EX)) {
              $data = fread($this->file, filesize($this->filename));
              return $data;
            }
            fclose($this->file);
          }
        }
        sleep(2);
      }
      $this->write_access = False;
      return $this->openFileWithoutWriteAccess();
    }
    $this->write_access = False;
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
