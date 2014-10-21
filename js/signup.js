<script>

$("#signup").submit( function() {

  var name = $("#name").val();

  if (name.length < 2) {
    $("#name").css("border", "red solid 1px");
    return false;
  }

  var email = $("#email").val();

  if (validateEmail(email) == false) {
    $("#email").css("border", "red solid 1px");
    return false;
  }

  var n = <?php echo $experiment->total_requirements; ?>;

  for (i=0; i<n; i++) {
    if ($("#require" + i).is(":checked") == false) {
      return false
    }
  }

  return true;

});

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

</script>
