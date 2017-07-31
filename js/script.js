$(document).ready(function() {
  $("form.ajax").submit(function(e) {
    e.preventDefault();
    var form_data = {};
    var empty = false;
    var may_be_empty = ['thumbnail_path', 'img_path'];
    $.each($(this).serializeArray(), function(key, value) {
      if(value.value || may_be_empty.indexOf(value.name) === -1) {
        if (value.value) {
          form_data[value.name] = value.value;
        } else {
          alert("No empty boxes");
          empty = true;
          return false;
        }
      }
    });
    console.log(form_data);
    //return;
    if(!empty) {
      $.ajax({
        method: "POST",
        url: $(this).attr("action"),
        data: form_data,
        complete: function(e) {
           console.log(e.responseText);
        },
        success: function() {
          alert('Something happend and it resulted in success!');
        }
      });
    }
  });
});
