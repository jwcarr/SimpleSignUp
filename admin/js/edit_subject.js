<script>

$("#fyi_emails").hide();

$("#change_timeslot").change( function() {
  var new_time = $("#change_timeslot").val();
  if (new_time == 'none') {
    $("#fyi_emails").hide();
  }
  else {
    $("#fyi_emails").show();
  }
});

</script>
