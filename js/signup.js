<script>

$("#signup").submit( function() {

  $("#name").css("border", "black solid 1px");
  $("#name").css("background-color", "white");
  $("#email").css("border", "black solid 1px");
  $("#email").css("background-color", "white");
  $("#phone").css("border", "black solid 1px");
  $("#phone").css("background-color", "white");
  $("#reqs").css("background-color", "white");

  var name = $("#name").val();

  if (name.length < 2) {
    $("#name").css("border", "#B22B3B solid 1px");
    $("#name").css("background-color", "#F5E3E6");
    return false;
  }

  var email = $("#email").val();

  if (validateEmail(email) == false) {
    $("#email").css("border", "#B22B3B solid 1px");
    $("#email").css("background-color", "#F5E3E6");
    return false;
  }

  var phone = $("#phone").val().replace(/ /g, '');
  $("#phone").val(phone);

  if (phone != "") {
    if (validatePhone(phone) == false) {
      $("#phone").css("border", "#B22B3B solid 1px");
      $("#phone").css("background-color", "#F5E3E6");
      return false;
    }
  }

  var n = <?php echo $experiment->total_requirements; ?>;

  for (i=0; i<n; i++) {
    if ($("#require" + i).is(":checked") == false) {
      $("#reqs").css("background-color", "#F5E3E6");
      return false
    }
  }

  return true;

});

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function validatePhone(phone) {
    var re = /^07\d{9}$/;
    return re.test(phone);
}

</script>
