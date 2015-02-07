<script>

var re = /^\d{4}\/\d{2}\/\d{2}$/;

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
  var date = $("#new_date").val();
  if (re.test(date)) {
    var temp_date = date.substring(0, date.length-2);
    $("#new_date").val(temp_date);
    $("#calendar").append("<tr><td>" + date + "</td><td><input type='text' name='new_times[" + date + "]' value='' size='30' /></td><td></td><tr>");
  }
});

</script>
