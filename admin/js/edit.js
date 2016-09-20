<script>

var re_date = /^\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/;
var re_time = /^([0-1][0-9]|2[0-3]):[0-5][0-9]$/;

var current_dates = <?php echo json_encode($current_dates); ?>;
var date_count = <?php echo $date_i; ?>;

var all_times_validated = true;

if ($("#per_slot").val() < 2) {
  $("#multiperson_emails").hide();
}

$("#per_slot").change( function() {
  if ($("#per_slot").val() < 2) {
    $("#multiperson_emails").hide();
  }
  else {
    $("#multiperson_emails").show();
  }
});

$("#add_date").click( function() {
  <?php if ($page == 'new') { echo '$("#calendar").show();'; }  ?>
  var date = $("#new_date").val();
  var date_index = $.inArray(date, current_dates);
  if (date_index >= 0) {
    $("#new_times" + date_index).css("background-color", "#E6ECF3");
    setTimeout('$("#new_times' + date_index + '").css("background-color", "white")', 3000);
  }
  else {
    if (ValidateDate(date)) {
      var temp_date = date.substring(0, date.length-2);
      $("#new_date").val(temp_date);
      $("#calendar").append("<tr><td>" + date + "</td><td><input type='text' name='new_times[" + date + "]' id='new_times" + date_count + "' value='' style='width: 300px; background-color: #E6ECF3;' onchange='ValidateTimes(\"#new_times" + date_count + "\")' /></td><td></td><tr>");
      setTimeout('$("#new_times' + date_count + '").css("background-color", "white")', 3000);
      current_dates.push(date);
      date_count += 1;
    }
    else {
      $("#new_date").css("background-color", "#F5E3E6");
      setTimeout('$("#new_date").css("background-color", "white")', 3000);
    }
  }
});

function ValidateTimes(date_id) {
  all_times_validated = true;
  $(date_id).css("background-color", "white");
  var time_string = $(date_id).val();
  if (time_string != "") {
    time_string = time_string.replace(/\s/g, '');
    $(date_id).val(time_string);
    var times = time_string.split(',');
    for (i=0; i<times.length; i++) {
      if (re_time.test(times[i]) == false) {
        $(date_id).css("background-color", "#F5E3E6");
        all_times_validated = false;
      }
    }
  }
}

function ValidateDate(date) {
  if (re_date.test(date)) {
    var date = date.split('/');
    if ($.inArray(date[1], ['01', '03', '05', '07', '08', '10', '12']) >= 0) {
      if (date[2] <= 31) { return true; }
    }
    else if ($.inArray(date[1], ['04', '06', '09', '11']) >= 0) {
      if (date[2] <= 30) { return true; }
    }
    else {
      if (date[0] % 4 > 0) {
        if (date[2] <= 28) { return true; }
      }
      else if (date[0] % 100 > 0) {
        if (date[2] <= 29) { return true; }
      }
      else if (date[0] % 400 > 0) {
        if (date[2] <= 28) { return true; }
      }
      else {
        if (date[2] <= 29) { return true; }
      }
    }
  }
  return false;
}

function AllowSubmit() {
  if (all_times_validated == false) {
    alert('Please check that the times you have entered conform to the correct format. They should be in 24-hour format (HH:MM), including leading zeros, e.g. 09:00 or 14:30, and they should be separated by commas.');
    return false;
  }
  return true;
}

<?php

if ($page == 'new') {
  echo "$('#calendar').hide();";
}

?>

</script>
