<?php

include_once('class.file.php');

class Experiment {

  public function __construct($experiment_id, $write_access=False, $owner=False) {
    global $data_path;
    $this->id = $experiment_id;
    if ($owner == False) {
      $this->experiments_file = new File($data_path .'experiments', False);
      $this->owner = $this->extractElement($this->id, $this->experiments_file->data);
    }
    else {
      $this->owner = $owner;
    }
    $this->file = new File($data_path .'user_data/'. $this->owner .'/'. $this->id, $write_access);
    $this->changed_data = array();
  }

  public function saveExperimentData() {
    foreach ($this->changed_data as $parameter) {
      if (preg_match('/' . $parameter . ' = \{.*?\}/s', $this->file->data) == 0) {
        if ($parameter == 'calendar') { $this->file->data .= $parameter . ' = {' . $this->flattenCalendar() . "}"; }
        else { $this->file->data .= $parameter . ' = {' . $this->$parameter . "}"; }
        $this->file->data .= "\n\n";
      }
      else {
        $old_piece = $parameter . ' = {' . $this->extractElement($parameter, $this->file->data) . '}';
        if ($parameter == 'calendar') { $new_piece = $parameter . ' = {' . $this->flattenCalendar() . '}'; }
        else { $new_piece = $parameter . ' = {' . $this->$parameter . '}'; }
        $this->file->data = str_replace($old_piece, $new_piece, $this->file->data);
      }
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
      $this->name = trim($name);
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
      $this->description = $this->extractElement('description', $this->file->data);
    }
    return $this->description;
  }

  public function setDescription($description) {
    if ($description != $this->getDescription()) {
      $this->description = trim($description);
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
      $this->location = trim($location);
      $this->changed_data[] = 'location';
    }
  }

  public function getRequirements() {
    if (isset($this->requirements) == False) {
      $this->requirements = explode('; ', $this->extractElement('requirements', $this->file->data));
      if ($this->requirements[0] == '') { $this->requirements = Null; }
    }
    return $this->requirements;
  }

  public function setRequirements($requirements) {
    $requirements = explode("\n", trim($requirements));
    foreach ($requirements as $key=>$value) {
      $requirements[$key] = trim($value);
    }
    $requirements = implode('; ', $requirements);
    if ($requirements != $this->extractElement('requirements', $this->file->data)) {
      $this->requirements = $requirements;
      $this->changed_data[] = 'requirements';
    }
  }

  public function getExclusions() {
    if (isset($this->exclusions) == False) {
      $this->exclusions = explode('; ', $this->extractElement('exclusions', $this->file->data));
    }
    return $this->exclusions;
  }

  public function setExclusions($exclusions) {
    if (is_null($exclusions) == False) {
      $exclusions = implode('; ', $exclusions);
      if ($exclusions != $this->extractElement('exclusions', $this->file->data)) {
        $this->exclusions = $exclusions;
        $this->changed_data[] = 'exclusions';
      }
    }
  }

  public function getPerSlot() {
    if (isset($this->per_slot) == False) {
      $this->per_slot = $this->extractElement('per_slot', $this->file->data);
    }
    return $this->per_slot;
  }

  public function setPerSlot($per_slot) {
    if ($per_slot != $this->getPerSlot()) {
      $this->per_slot = trim($per_slot);
      $this->changed_data[] = 'per_slot';
    }
  }

  public function getSharedAccess() {
    if (isset($this->shared_access) == False) {
      $this->shared_access = explode('; ', $this->extractElement('shared_access', $this->file->data));
      if (count($this->shared_access) == 1 AND $this->shared_access[0] == '') {
        $this->shared_access = array();
      }
    }
    return $this->shared_access;
  }

  public function setSharedAccess($shared_access, $all_users) {
    if (is_null($shared_access)) { $shared_access = array(); }
    $shared_access_string = implode('; ', $shared_access);
    if ($shared_access_string != $this->extractElement('shared_access', $this->file->data)) {
      $this->shared_access = $shared_access_string;
      foreach ($all_users as $user) {
        if (in_array($user, $shared_access)) {
          $user_object = new User($user, True);
          $user_object->addSharedExperiment($this->id);
          $user_object->saveUserDetails();
          unset($user_object);
        }
        else {
          $user_object = new User($user, True);
          $user_object->removeSharedExperiment($this->id);
          $user_object->saveUserDetails();
          unset($user_object);
        }
      }
      $this->changed_data[] = 'shared_access';
    }
  }

  public function printSharedAccess($usernames) {
    $shared_users = $this->getSharedAccess();
    foreach ($usernames as $username) {
      if ($username != $this->owner) {
        if (in_array($username, $shared_users)) {
          $options .= '<option value="' . $username . '" selected>' . $username . '</option>';
        }
        else {
          $options .= '<option value="' . $username . '">' . $username . '</option>';
        }
      }
    }
    return $options;
  }

  public function getCalendar() {
    if (isset($this->calendar) == False) {
      $this->calendar = array();
      $date_data = explode("\n", $this->extractElement('calendar', $this->file->data));
      if ($date_data[0] == '') {
        $this->calendar = Null;
        return $this->calendar;
      }
      else {
        $dates_array = array();
        foreach ($date_data as $date_datum) {
          $date_times = explode(' ~ ', $date_datum);
          if ($date_times[1] == '') {
            $date_times = explode(' ~', $date_datum);
            $time_data = array();
          }
          else {
            $time_data = explode('; ', $date_times[1]);
          }
          $times_array = array();
          foreach ($time_data as $time_datum) {
            $time = explode(' = ', $time_datum);
            $time = $time[0];
            $subjects = $this->extractValue($time, $time_datum);
            if ($subjects == '') {
              $times_array[$time] = Null;
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
    }
    return $this->calendar;
  }

  public function removeTime($date, $time=False) {
    $calendar = $this->getCalendar();
    if ($time == False) {
      foreach ($calendar[$date] as $time=>$slot) {
        if (is_null($slot) == False) {
          return False;
        }
      }
      unset($calendar[$date]);
    }
    else {
      if (is_null($calendar[$date][$time]) == False) {
        return False;
      }
      unset($calendar[$date][$time]);
    }
    $this->calendar = $calendar;
    $this->changed_data[] = 'calendar';
    return True;
  }

  public function setCalendar($new_times) {
    $edit = False;
    $calendar = $this->getCalendar();
    foreach ($new_times as $date=>$time_string) {
      if ($date != '') {
        if ($time_string != '') {
          $times = explode(',', trim($time_string));
          foreach ($times as $time) {
            if ($time != '') {
              $calendar[trim($date)][trim($time)] = Null;
              $edit = True;
            }
          }
          ksort($calendar[trim($date)]);
        }
      }
    }
    ksort($calendar);
    if ($edit == True) {
      $this->calendar = $calendar;
      $this->changed_data[] = 'calendar';
    }
  }

  private function flattenCalendar() {
    $date_array = array();
    foreach ($this->calendar as $date=>$slots) {
      $time_array = array();
      if (is_null($slots)) { $slots = array(); }
      foreach ($slots as $time=>$subjects) {
        if (is_null($subjects)) {
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
    if (is_null($slot)) { $slot = array(); }
    return $slot;
  }

  public function addToSlot($date, $time, $name, $email, $phone) {
    $slot = $this->getSlot($date, $time);
    if ($phone == '') { $phone = 'Not provided'; }
    $slot[] = array(trim($name), trim($email), trim($phone));
    $this->calendar[$date][$time] = $slot;
    $this->changed_data[] = 'calendar';
  }

  public function getDate($date) {
    $calendar = $this->getCalendar();
    return $calendar[$date];
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
      $calendar[$date][$time] = Null;
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

  public function editSubject($date, $time, $subject_number, $name, $email, $phone, $new_timeslot, $user_name, $user_email, $send_emails) {
    $calendar = $this->getCalendar();
    $subject_email = $calendar[$date][$time][$subject_number][1];
    if ($new_timeslot == 'none') {
      $calendar[$date][$time][$subject_number] = array(trim($name), trim($email), trim($phone));
    }
    else {
      $new_timeslot = explode('|', $new_timeslot);
      $new_date = $new_timeslot[0];
      $new_time = $new_timeslot[1];
      $slot = $this->getSlot($new_date, $new_time);
      if (count($slot) >= $this->getPerSlot()) {
        return False;
      }
      $current_subjects = $slot;
      if ($phone == '') { $phone = 'Not provided'; }
      $slot[] = array(trim($name), trim($email), trim($phone));
      $calendar[$new_date][$new_time] = $slot;
      unset($calendar[$date][$time][$subject_number]);
      if (count($calendar[$date][$time]) == 0) {
        $calendar[$date][$time] = Null;
      }
      else {
        $calendar[$date][$time] = array_values($calendar[$date][$time]);
      }
      if ($send_emails == True) {
        $formatted_date = date('l jS F', strtotime($new_date));
        foreach ($current_subjects as $subject) {
          $this->sendEmail($subject[1], $user_name, $user_email, 'email_full', array('NAME'=>$subject[0], 'DATE'=>$formatted_date, 'TIME'=>$new_time));
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
