<script>

$("#signup").submit( function() {

  var chosen_time = $('input[type="radio"]:checked').val();

  if (chosen_time == null) {
    return false;
  }

  return true;

});

</script>
