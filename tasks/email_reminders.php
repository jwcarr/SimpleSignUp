<?php

//   This script should be placed in a non-publicly accessible directory on the server.
//   A cron job can then be set up like this, to send out email reminders at 10am every day:
//
//      0 10 * * * php -f /path/to/email_reminders.php
//
//   The paths below also need to be set up appropriately:

date_default_timezone_set('UTC');

$data_path = 'server_data/ssu/';
include_once('public_html/ssu/php/class.user.php');

$users_file = new File($data_path . 'users', False);
preg_match('/all_usernames = \{(.*?)\}/s', $users_file->data, $matches);
$users = explode('; ', trim($matches[1]));

$tomorrow = date('Y/m/d', strtotime(date('Y-m-d')) + 86400);
$formatted_date = date('l jS F', strtotime($tomorrow));

$log = 'emails sent on: ' . date('l jS F, H:i') . "\n\n";

$total_emails = 0;

foreach ($users as $username) {

  $user = new User($username, False);

  preg_match('/' . $username . ' = \{(.*?)\}/s', $users_file->data, $user_match);
  preg_match('/experiments = \[(.*?)\]/s', $user_match[1], $exp_match);
  $experiments = explode(', ', trim($exp_match[1]));

  if (count($experiments) > 0 AND $experiments[0] != '') {
    foreach ($experiments as $exp) {
      $success_count = 0;
      $experiment = new Experiment($exp, True, $username);
      if ($experiment->getAutomatedStatus() === True) {
        if ($experiment->getRemindersSent() === False) {
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
            $experiment->setLastReminders(date('Y-m-d'));
            $experiment->saveExperimentData();
            $log .= '- ' . $experiment->getName() . ' [' . $user->getName() . ']' . "\n";
            $fails = implode(', ', $fails);
            if ($fails == '') {
              $log .= "  - Reminder emails were successfully sent to {$success_count} participants\n\n";
            }
            else {
              $log .= '  - Emails failed to send to: ' . $fails . "\n\n";
            }
          }
        }
      }
      unset($experiment);
      $total_emails += $success_count;
    }
  }
  unset($user);
}

echo $total_emails . ' ' . $log;

?>
