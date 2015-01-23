<?php

// Insert a password on line 5 so that the cron job can authenticate

if ($_REQUEST['p'] == 'PUT-A-PASSWORD-HERE') {

  include_once("../php/globals.php");
  $data_path = '../' . $data_path;
  include_once("../php/class.user.php");

  $users_file = new File($data_path . 'users', False);
  preg_match('/all_usernames = \{(.*?)\}/s', $users_file->data, $matches);
  $users = explode('; ', trim($matches[1]));

  $tomorrow = date('Y/m/d', strtotime(date('Y-m-d')) + 86400);
  $formatted_date = date('l jS F', strtotime($tomorrow));

  $log = 'EMAILS SENT ON: ' . date('l jS F, H:i') . "\n\n";

  $total_emails = 0;

  foreach ($users as $username) {

    $user = new User($username, False);

    preg_match('/' . $username . ' = \{(.*?)\}/s', $users_file->data, $user_match);
    preg_match('/experiments = \[(.*?)\]/s', $user_match[1], $exp_match);
    $experiments = explode(', ', trim($exp_match[1]));

    if (count($experiments) > 0 AND $experiments[0] != '') {
      foreach ($experiments as $exp) {
        $success_count = 0;
        $experiment = new Experiment($exp, False, $username);
        $tomorrow_slots = $experiment->getDate($tomorrow);
        if (count($tomorrow_slots) > 0) {
          $fails = array();
          foreach ($tomorrow_slots as $time=>$slot) {
            if ($slot != Null) {
              foreach ($slot as $subject) {
                $result = $experiment->sendEmail($subject[1], $user->getName(), $user->getEmail(), 'email_reminder', array('NAME'=>$subject[0], 'DATE'=>$formatted_date, 'TIME'=>$time));
                if ($result == True) { $success_count += 1; }
                else { $fails[] = $subject_name; }
              }
            }
          }
          $log .= '- ' . $experiment->getName() . ' [' . $user->getName() . ']' . "\n";
          $fails = implode(', ', $fails);
          if ($fails == '') {
            $log .= "  - Reminder emails were successfully sent to {$success_count} participants\n\n";
          }
          else {
            $log .= '  - Emails failed to send to: ' . $fails . "\n\n";
          }
        }
        unset($experiment);
        $total_emails += $success_count;
      }
    }
    unset($user);
  }

  unset($users_file);

  if ($total_emails > 0) {

    $log = $total_emails . ' ' . $log;

    $email_headers = "From: SimpleSignUp <{$admin_email_address}>\r\nContent-Type: text/plain; charset=UTF-8";
    mail($admin_email_address, 'SimpleSignUp Automated Emails', $log, $email_headers);

  }

}

?>
