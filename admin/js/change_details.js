<script>

$("#change_details").submit( function () {
  $("#name").css("border", "black solid 1px");
  $("#name").css("background-color", "white");
  $("#email").css("border", "black solid 1px");
  $("#email").css("background-color", "white");
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
});

$("#change_password").submit( function () {
  $("#password").css("border", "black solid 1px");
  $("#password").css("background-color", "white");
  $("#password1").css("border", "black solid 1px");
  $("#password1").css("background-color", "white");
  $("#password2").css("border", "black solid 1px");
  $("#password2").css("background-color", "white");
  var password = $("#password").val();
  var password1 = $("#password1").val();
  var password2 = $("#password2").val();
  if (password.length < 8) {
    $("#password").css("border", "#B22B3B solid 1px");
    $("#password").css("background-color", "#F5E3E6");
    return false;
  }
  if (password1.length < 8) {
    $("#password1").css("border", "#B22B3B solid 1px");
    $("#password1").css("background-color", "#F5E3E6");
    $("#password2").css("border", "#B22B3B solid 1px");
    $("#password2").css("background-color", "#F5E3E6");
    return false;
  }
  if (password1 != password2) {
    $("#password1").css("border", "#B22B3B solid 1px");
    $("#password1").css("background-color", "#F5E3E6");
    $("#password2").css("border", "#B22B3B solid 1px");
    $("#password2").css("background-color", "#F5E3E6");
    return false;
  }

});

function validateEmail(email) {
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  if (re.test(email) == true) {
    return true;
  }
  return false;
}

</script>
