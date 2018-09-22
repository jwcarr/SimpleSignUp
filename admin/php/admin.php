<?php

function generate_password() {
  $letters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
  $password = '';
  for ($i=0; $i<16; $i++) {
    $password .= $letters[rand(0, 61)];
  }
  return $password;
}

function make_user_directory($username) {
  global $data_path;
  $path = $data_path . 'user_data/' . $username;
  mkdir($path, 0777);
  chmod($path, 0777);
  return is_dir($path);
}

function delete_user_directory($username) {
  global $data_path;
  $path = $data_path . 'user_data/' . $username;
  if (is_dir($path)) {
    rmdir($path);
    return !is_dir($path);
  }
  return false;
}

function extract_element($element, $data) {
  $pattern = '/' . $element . ' = \{(.*?)\}/s';
  preg_match($pattern, $data, $matches);
  return trim($matches[1]);
}

function extract_value($value, $data) {
  $pattern = '/' . $value . ' = \[(.*?)\]/s';
  preg_match($pattern, $data, $matches);
  return trim($matches[1]);
}

function validate_username($username) {
  $pattern = '/^[a-z]+$/';
  preg_match($pattern, $username, $matches);
  if (count($matches) === 1) {
    return true;
  }
  return false;
}

function validate_email($email) {
  $pattern = '/.+@.*ed\.ac\.uk/';
  preg_match($pattern, $email, $matches);
  if (count($matches) === 1) {
    return true;
  }
  return false;
}

if ($user->username === 'admin') {

  if ($_POST['task'] === 'add_user') {
    if (validate_username($_POST['add_username'])) {
      if (validate_email($_POST['add_email'])) {
        $users_file = new File($data_path . 'users', true);
        $usernames = explode('; ', extract_element('all_usernames', $users_file->data));
        if (!in_array($_POST['add_username'], $usernames)) {
          if (make_user_directory($_POST['add_username'])) {
            $usernames[] = $_POST['add_username'];
            $new_users_data = 'all_usernames = {' . implode('; ', $usernames) . "}\n";
            foreach (explode("\n", $users_file->data) as $line) {
              $line_data = explode(' = ', $line);
              if ($line !== '' && $line_data[0] !== 'all_usernames') {
                $new_users_data .= $line . "\n";
              }
            }
            $password = generate_password();
            $hashed_password = crypt($password);
            $new_user_line = $_POST['add_username'] . ' = { name = [' . $_POST['add_name'] . '], email = [' . $_POST['add_email'] . '], phone = [], experiments = [], shares = [], password = [' . $hashed_password . "] }\n";
            $users_file->data = $new_users_data . $new_user_line;
            if ($users_file->overwrite()) {
              $notification = '<div id="notification" class="notification-green"><p>Account created with username <strong>' . $_POST['add_username'] . '</strong> and password <strong>' . $password . '</strong></p></div>';
            } else {
              $notification = '<div id="notification" class="notification-red"><p>Error: Failed to create the new user</p></div>';
            }
          } else {
            $notification = '<div id="notification" class="notification-red"><p>Error: Failed to create the new user directory</p></div>';
          }
        } else {
          $notification = '<div id="notification" class="notification-red"><p>Error: The username ' . $_POST['add_username'] . ' already exists</p></div>';
        }
      } else {
        $notification = '<div id="notification" class="notification-red"><p>Error: Invalid email address. Email must end in ed.ac.uk</p></div>';
      }
    } else {
      $notification = '<div id="notification" class="notification-red"><p>Error: Invalid username; use lowercase alphabetical characters only</p></div>';
    }
  }

  elseif ($_POST['task'] === 'reset_password') {
    $reset_user = new User($_POST['reset_username'], true);
    $password = generate_password();
    $reset_user->setPassword($password);
    if ($reset_user->saveUserDetails()) {
      $notification = '<div id="notification" class="notification-green"><p>The password for user <strong>' . $_POST['reset_username'] . '</strong> was reset to: <strong>' . $password . '</strong></p></div>';
    } else {
      $notification = '<div id="notification" class="notification-red"><p>Error: Failed to reset password</p></div>';
    }
  }

  elseif ($_POST['task'] === 'transfer_experiment') {
    $users_file = new File($data_path . 'users', false);
    $usernames = explode('; ', extract_element('all_usernames', $users_file->data));
    if (in_array($_POST['trans_username'], $usernames)) {
      $experiments_file = new File($data_path . 'experiments', true);
      $old_user = extract_element($_POST['trans_experiment'], $experiments_file->data);
      if ($_POST['trans_username'] != $old_user) {
        $old_piece = $_POST['trans_experiment'] . ' = {' . $old_user . '}';
        $new_piece = $_POST['trans_experiment'] . ' = {' . $_POST['trans_username'] . '}';
        $experiments_file->data = str_replace($old_piece, $new_piece, $experiments_file->data);
        $old_path = $data_path . 'user_data/' . $old_user . '/' . $_POST['trans_experiment'];
        $new_path = $data_path . 'user_data/' . $_POST['trans_username'] . '/' . $_POST['trans_experiment'];
        $transfer_user = new User($old_user, true);
        $transfer_user->removeExperiment($_POST['trans_experiment']); // Remove experiment from old user
        if ($transfer_user->saveUserDetails()) {
          unset($transfer_user);
          $transfer_user = new User($_POST['trans_username'], true);
          $transfer_user->addExperiment($_POST['trans_experiment']); // Add experiment to new user
          if ($transfer_user->saveUserDetails()) {
            if ($experiments_file->overwrite()) {
              if (rename($old_path, $new_path)) {
                $notification = '<div id="notification" class="notification-green"><p>The experiment ' . $_POST['trans_experiment'] . ' was transferred from ' . $old_user . ' to ' . $_POST['trans_username'] . '</p></div>';
              } else {
                $notification = '<div id="notification" class="notification-red"><p>Error: Failed to move ' . $old_path . ' to ' . $new_path . '</p></div>';
              }
            } else {
              $notification = '<div id="notification" class="notification-red"><p>Error: Failed to make changes to the experiments file</p></div>';
            }
          } else {
            $notification = '<div id="notification" class="notification-red"><p>Error: Failed to make changes to user ' . $_POST['trans_username'] . '</p></div>';
          }
        } else {
          $notification = '<div id="notification" class="notification-red"><p>Error: Failed to make changes to user ' . $old_user . '</p></div>';
        }
      } else {
        $notification = '<div id="notification" class="notification-red"><p>Error: The experiment ' . $_POST['trans_experiment'] . ' already belongs to ' . $_POST['trans_username'] . '</p></div>';
      }
    } else {
      $notification = '<div id="notification" class="notification-red"><p>Error: The username ' . $_POST['trans_username'] . ' does not exist</p></div>';
    }
  }

  elseif ($_POST['task'] === 'delete_user') {
    $users_file = new File($data_path . 'users', true);
    $usernames = explode('; ', extract_element('all_usernames', $users_file->data));
    if (in_array($_POST['del_username'], $usernames)) {
      $user_data = extract_element($_POST['del_username'], $users_file->data);
      $experiments = extract_value('experiments', $user_data);
      if ($experiments === '') {
        unset($usernames[array_search($_POST['del_username'], $usernames)]);
        $new_users_data = 'all_usernames = {' . implode('; ', $usernames) . "}\n";
        foreach (explode("\n", $users_file->data) as $line) {
          $line_data = explode(' = ', $line);
          if ($line !== '' && $line_data[0] !== 'all_usernames' && $line_data[0] !== $_POST['del_username']) {
            $new_users_data .= $line . "\n";
          }
        }
        $users_file->data = $new_users_data;
        if (delete_user_directory($_POST['del_username'])) {
          if ($users_file->overwrite()) {
            $notification = '<div id="notification" class="notification-green"><p>The user ' . $_POST['del_username'] . ' has been deleted</p></div>';
          } else {
            $notification = '<div id="notification" class="notification-red"><p>Error: Failed to delete the user</p></div>';
          }
        } else {
          $notification = '<div id="notification" class="notification-red"><p>Error: Failed to delete the user directory</p></div>';
        }
      } else {
        $n_experiments = count(explode(', ', $experiments));
        $notification = '<div id="notification" class="notification-red"><p>Cannot delete ' . $_POST['del_username'] . '. The user currently owns ' . $n_experiments . ' experiments</p></div>';
      }
    } else {
      $notification = '<div id="notification" class="notification-red"><p>Error: The username ' . $_POST['del_username'] . ' does not exist</p></div>';
    }
  }

  if (isset($usernames)) {
    sort($usernames);
  } else {
    $usernames = $user->getAllUsernames();
  }
  $current_usernames = '<option value="">SELECT USER</option>';
  foreach ($usernames as $username) {
    $current_usernames .= '<option value="' . $username . '">' . $username . '</option>';
  }

  if (!isset($experiments_file)) {
    $experiments_file = new File($data_path .'experiments', false);    
  }
  $experiment_data = explode("\n", $experiments_file->data);
  $all_experiments = array();
  foreach ($experiment_data as $line) {
    if ($line != '') {
      preg_match('/(.*?) = \{(.*?)\}/s', $line, $matches);
      $all_experiments[$matches[2]][] = $matches[1];
    }
  }
  ksort($all_experiments);
  $current_experiments = '<option value="">SELECT EXPERIMENT</option>';
  foreach ($all_experiments as $user=>$experiments) {
    foreach ($experiments as $experiment) {
      $current_experiments .= '<option value="' . $experiment . '">' . $user . ': ' . $experiment . '</option>';
    }
  }

} else {
  $page = 'main';
}

?>
