<?php

class Experiment {

  public function __construct($experiment_id, $write_access=False) {
    global $data_path;
    $this->id = $experiment_id;
    $experiments_file = new File($data_path .'experiments', False);
    $this->owner = $this->extractElement($this->id, $experiments_file->data);
    $this->file = new File($data_path .'user_data/'. $this->owner .'/'. $this->id, $write_access);
    $this->changed_data = array();
  }

  public function saveExperimentData() {
    foreach ($this->changed_data as $parameter) {
      $old_piece = $parameter . ' = {' . $this->extractElement($parameter, $this->file->data) . '}';
      if ($parameter == 'calendar') { $new_piece = $parameter . ' = {' . $this->flattenCalendar() . '}'; }
      else { $new_piece = $parameter . ' = {' . $this->$parameter . '}'; }
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
    if ($name != $this->getName()) {
      $this->name = $name;
      $this->changed_data[] = 'name';
    }
  }

  public function getStatus() {
    if (isset($this->status) == False) {
      $this->status = $this->extractElement('status', $this->file->data);
    }
    return $this->status;
  }

  public function setStatus($status) {
    $this->status = $status;
    $this->changed_data[] = 'status';
  }

  public function getDescription() {
    if (isset($this->description) == False) {
      $this->description = str_replace("\n", '<br />', $this->extractElement('description', $this->file->data));
    }
    return $this->description;
  }

  public function setDescription($description) {
    if ($description != $this->getDescription()) {
      $this->description = $description;
      $this->changed_data[] = 'description';
    }
  }

  public function getLocation() {
    if (isset($this->location) == False) {
      $this->location = $this->extractElement('location', $this->file->data);
    }
    return $this->location;
  }

  public function setLocation($location) {
    if ($location != $this->getLocation()) {
      $this->location = $location;
      $this->changed_data[] = 'location';
    }
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

  public function getPerSlot() {
    if (isset($this->per_slot) == False) {
      $this->per_slot = $this->extractElement('per_slot', $this->file->data);
    }
    return $this->per_slot;
  }

  public function setPerSlot($per_slot) {
    if ($perSlot != $this->getPerSlot()) {
      $this->per_slot = $per_slot;
      $this->changed_data[] = 'per_slot';
    }
  }

  public function getCalendar() {
    if (isset($this->calendar) == False) {
      $this->calendar = array();
      $date_data = explode("\n", $this->extractElement('calendar', $this->file->data));
      $dates_array = array();
      foreach ($date_data as $date_datum) {
        $date_times = explode(' ~ ', $date_datum);
        $time_data = explode('; ', $date_times[1]);
        $times_array = array();
        foreach ($time_data as $time_datum) {
          $time = explode(' = ', $time_datum)[0];
          $subjects = $this->extractValue($time, $time_datum);
          if ($subjects == '') {
            $times_array[$time] = None;
          }
          else {
            $subject_data = explode(' & ', $subjects);
            $subjects_array = array();
            foreach ($subject_data as $subject_datum) {
              $subjects_array[] = explode(', ', $subject_datum);
            }
            $times_array[$time] = $subjects_array;
          }
        }
        $dates_array[$date_times[0]] = $times_array;
      }
      $this->calendar = $dates_array;
    }
    return $this->calendar;
  }

  public function flattenCalendar() {
    $date_array = array();
    foreach ($this->calendar as $date=>$slots) {
      $time_array = array();
      foreach ($slots as $time=>$subjects) {
        if ($subjects == None) {
          $time_array[] = $time . ' = []';
        }
        else {
          $subject_array = array();
          foreach ($subjects as $subject) {
            $subject_array[] = implode(', ', $subject);
          }
          $time_array[] = $time . ' = [' . implode(' & ', $subject_array) . ']';
        }
      }
      $date_array[] = $date . ' ~ ' . implode('; ', $time_array);
    }
    return implode("\n", $date_array);
  }

  public function getSlot($date, $time) {
    $calendar = $this->getCalendar();
    $slot = $calendar[$date][$time];
    if ($slot == None) { $slot = array(); }
    return $slot;
  }

  public function addToSlot($date, $time, $name, $email, $phone) {
    $slot = $this->getSlot($date, $time);
    if ($phone == '') { $phone = 'Not provided'; }
    $slot[] = array($name, $email, $phone);
    $this->calendar[$date][$time] = $slot;
    $this->changed_data[] = 'calendar';
  }

  public function getSubject($date, $time, $subject_number) {
    $slot = $this->getSlot($date, $time, $subject_number);
    return $slot[$subject_number];
  }

  public function deleteSubject($date, $time, $subject_number) {
    $calendar = $this->getCalendar();
    $subject_email = $calendar[$date][$time][$subject_number][1];
    unset($calendar[$date][$time][$subject_number]);
    if (count($calendar[$date][$time]) == 0) {
      $calendar[$date][$time] = None;
    }
    else {
      $calendar[$date][$time] = array_values($calendar[$date][$time]);
    }
    print_r($calendar[$date][$time]);
    $this->calendar = $calendar;
    $this->changed_data[] = 'calendar';
    $this->removeExclusionEmail($subject_email);
    $this->setExclusionEmails();
  }

      }
        }
      }
    }
    $this->calendar = $calendar;
    $this->changed_data[] = 'calendar';
    if ($subject_email != $email) {
      $this->removeExclusionEmail($subject_email);
      $this->addExclusionEmails(array($email));
      $this->setExclusionEmails();
    }
    return True;
  }

  public function getExclusionEmails() {
    if (isset($this->exclusion_emails) == False) {
      $exclusion_emails = $this->extractElement('exclusion_emails', $this->file->data);
      if ($exclusion_emails == '') {
        $this->exclusion_emails = array();
      }
      else {
        $this->exclusion_emails = explode('; ', $exclusion_emails);
      }
    }
    return $this->exclusion_emails;
  }

  public function setExclusionEmails() {
    $this->exclusion_emails = array_map('strtolower', $this->exclusion_emails);
    $this->exclusion_emails = implode('; ', $this->exclusion_emails);
    $this->changed_data[] = 'exclusion_emails';
  }

  public function addExclusionEmails($exclusion_emails) {
    $this->exclusion_emails = array_merge($this->getExclusionEmails(), array_map('strtolower', $exclusion_emails));
  }

  private function removeExclusionEmail($email) {
    $exclusion_emails = $this->getExclusionEmails();
    if (($key = array_search(strtolower($email), $exclusion_emails)) !== False) {
      unset($exclusion_emails[$key]);
    }
    $this->exclusion_emails = $exclusion_emails;
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

  public function getAvailableSlots($include_today=False) {
    if (isset($this->available_slots) == False) {
      $calendar = $this->getCalendar();
      $this->available_slots = array();
      $unix_today = strtotime(date('Y-m-d'));
      foreach ($calendar as $date=>$slots) {
        $unix_date = strtotime($date);
        if ($include_today == True) {
          $cut_off_date = $unix_today - 86400;
        }
        else {
          $cut_off_date = $unix_today;
        }
        if ($unix_date > $cut_off_date) {
          foreach ($slots as $time=>$slot) {
            if ($slot == None) { $slot_count = 0; }
            else { $slot_count = count($slot); }
            if ($slot_count < $this->getPerSlot()) {
              $this->available_slots[$date][$time] = $slot_count;
            }
          }
        }
      }
    }
    return $this->available_slots;
  }

  public function printCalendar() {
    foreach ($this->getAvailableSlots() as $date=>$times) {
      echo '<h3>' . date('l, jS F Y', strtotime($date)) . '</h3>';
      echo '<table><tr>';
      foreach ($times as $time=>$slot_count) {
        echo '<td align="center" width="50px">' . $time . '</td>';
      }
      echo '</tr><tr>';
      foreach ($times as $time=>$slot_count) {
        if ($slot_count == 0) {
          echo '<td align="center"><input type="radio" name="timeslot" value="'. $date . '|'. $time .'" /></td>';
        }
        else {
          echo '<td align="center" style="background-color: #EAF2E8;"><input type="radio" name="timeslot" value="'. $date . '|'. $time .'" /></td>';
        }
      }
      echo '</tr></table>';
    }
  }

  public function printAvailableDates() {
    foreach ($this->getAvailableSlots() as $date=>$times) {
      $available_times = array();
      foreach ($times as $time=>$slot_count) {
        $available_times[] = $time;
      }
      $available_times = implode(', ', $available_times);
      $available_dates .= '<li>' . date('jS M', strtotime($date)) . ' (' . $available_times . ')</li>';
    }
    return $available_dates;
  }

  public function printAltTimeslots($current_date, $current_time) {
    $available_times = '';
    foreach ($this->getAvailableSlots(True) as $date=>$times) {
      foreach ($times as $time=>$slot_count) {
        if ($date == $current_date AND $time == $current_time) {} else {
          $available_times .= '<option value="'. $date . '|'. $time .'">' . date('jS M', strtotime($date)) . ', ' . $time . ' (' . $slot_count . ')</option>';
        }
      }
    }
    return $available_times;
  }

  public function sendEmail($to_address, $from_name, $from_address, $content_ref, $fill_values) {
    $email_content = $this->createEmailContent($content_ref, $fill_values);
    $email_headers = "From: {$from_name} <{$from_address}>\r\nContent-Type: text/plain; charset=UTF-8";
    return mail($to_address, $this->getName(), $email_content, $email_headers);
  }

  public function createEmailContent($content_ref, $fill_values) {
    $content = $this->extractElement($content_ref, $this->file->data);
    foreach ($fill_values as $key=>$value) {
      $content = str_replace('<'. $key .'>', $value, $content);
    }
    $content = str_replace("\n", "\r\n", $content);
    return $content;
  }

  public function extractElement($element, $data) {
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

class User {

  public function __construct($username) {
    global $data_path;
    $this->username = $username;
    $users_file = new File($data_path .'users', False);
    $this->data = $this->extractElement($this->username, $users_file->data);
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
      for ($i=0; $i<10; $i++) {
        if (is_writable($this->filename)) {
          $this->file = fopen($this->filename, 'a+');
          if ($this->file != False) {
            if (flock($this->file, LOCK_EX)) {
              $data = fread($this->file, filesize($this->filename));
              return $data;
            }
            fclose($this->file);
          }
        }
        sleep(1);
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

?>
