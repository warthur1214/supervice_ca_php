$(function() {
    var form = $('#login_form');
    form.validate({
        errorElement: 'label', //default input error message container
        errorClass: 'text-red', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        rules: {
            account_name: {
                required: true
            },
            pwd: {
                required: true
            }
        },
        invalidHandler: function(event, validator) { //display error alert on form submit   
            $('.alert-error').show();
        },

        highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },

        success: function(label) {
            $('.alert-error').hide();
            label.prevAll('.form-group').removeClass('has-error');
            label.remove();
        },

        errorPlacement: function(error, element) {
            error.addClass('text-red').insertAfter(element.closest('.form-group'));
        },

        submitHandler: function(form) {
            $('.alert-error').hide();
            $('.alert-success').show();
        }
    });
    $('.login').keyup(function(e) {
        if (e.keyCode == 13) {
            $('input').each(function() {
                if ($(this).val() == "") {
                    $(this).focus();
                    return false;
                }
            });
            $('#submit').click();
        }
    });

    $('#submit').click(function() {
        if (form.valid() == false) {
            return false;
        }
        var account_name = $('#account_name').val();
        var pwd = $('#pwd').val(); 
        pwd = newHexMd5( pwd ); 
        var requestData = {
            "account_name": account_name,
            "password": pwd
        }; 
        $.ajax({
            url: "/Home/Login/loginAjax",
            type: "post",
            data: requestData,
            dataType: "json",
            success: function(result) {
                if (result.status == 0) {
                    $('.alert').html(result.msg);
                    $('.alert').show();
                    setTimeout(function() { window.location.reload() }, 2000);
                } else {
                    //$('.alert').show().removeClass('alert-error').addClass('alert-info');
                    setTimeout(function() { window.location.href = '/' }, 500);
                }

            }
        });
    });
});
