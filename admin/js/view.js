<script>

var past = false;
var present = true;
var future = false;
var experiment_id = '<?php echo $experiment->id; ?>';

$( document ).ready( function() {
  $("#view-past").hide();
  $("#view-future").hide();
});

$( "#view-title-past" ).click( function() {
  $("#view-past").slideToggle(duration=150);
  if (past == true) {
    $("#view-title-past").html("<h3>► Completed</h3>");
    past = false;
  }
  else {
    $("#view-title-past").html("<h3>▼ Completed</h3>");
    past = true;
  }
});

$( "#view-title-present" ).click( function() {
  $("#view-present").slideToggle(duration=150);
  if (present == true) {
    $("#view-title-present").html("<h3>► Today</h3>");
    present = false;
  }
  else {
    $("#view-title-present").html("<h3>▼ Today</h3>");
    present = true;
  }
});

$( "#view-title-future" ).click( function() {
  $("#view-future").slideToggle(duration=150);
  if (future == true) {
    $("#view-title-future").html("<h3>► Upcoming</h3>");
    future = false;
  }
  else {
    $("#view-title-future").html("<h3>▼ Upcoming</h3>");
    future = true;
  }
});

$("#green-button").click( function() {
  window.location = '../?exp=' + experiment_id;
});

$("#button").click( function() {
  window.location = 'index.php?page=edit&exp=' + experiment_id;
});

$("#orange-button").click( function() {
  window.location = 'index.php?page=close&exp=' + experiment_id;
});

$("#red-button").click( function() {
  window.location = 'index.php?page=delete&exp=' + experiment_id;
});


setTimeout("$('#notification').slideUp(duration=150);", 30000);
</script>
