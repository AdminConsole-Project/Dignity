/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


let ac_relogin_form = document.getElementById("ac-relogin");

let ac_relogin_button = document.getElementById("ac-relogin-button");
let ac_relogin_info = document.getElementById("ac-relogin-info");

let ac_relogin_submit = document.getElementById("ac-relogin-submit");
let ac_relogin_max_submit = document.getElementById("ac-relogin-maxsubmit");
let ac_relogin_submit_count = document.getElementById("ac-relogin-submit-count");

let ac_relogin_overlay = document.getElementById("ac-relogin-overlay");

let ac_relogin_pwd_button = document.getElementById("ac-relogin-pwd-button");
let ac_relogin_pwd_input = document.getElementById("ac-relogin-pwd-input");
let ac_relogin_pwd_icon = document.getElementById("ac-relogin-pwd-icon");

let relogin_times_submit = 0;
let relogin_max_submit = 5;

let session_time = setInterval(session_interval_check,5000);
let relogin_timeout;

localStorage.setItem('relogin', false);

ac_relogin_pwd_button.addEventListener("click",function(){

    if (ac_relogin_pwd_input.type === "password"){

        ac_relogin_pwd_input.type = "text";
        ac_relogin_pwd_input.focus();
        ac_relogin_pwd_icon.classList.remove('fa-eye');
        ac_relogin_pwd_icon.classList.add('fa-eye-slash');

    }else {

        ac_relogin_pwd_input.type = "password";
        ac_relogin_pwd_input.focus();
        ac_relogin_pwd_icon.classList.remove('fa-eye-slash');
        ac_relogin_pwd_icon.classList.add('fa-eye');

    }
});

$(document).ready(function(){

    $('#ac-relogin-form').on('submit', function(e){

        ac_relogin_pwd_input.type = "password";
        ac_relogin_pwd_icon.classList.remove('fa-eye-slash');
        ac_relogin_pwd_icon.classList.add('fa-eye');

        ac_relogin_form.getElementsByClassName("ac-relogin-form")[0].classList.add('filter');

        ac_relogin_overlay.classList.remove('hide');

        ac_relogin_info.classList.add('hide');

        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'login.php?action=relogin',
            data: $(this).serialize(),
            success: function(data){

                ac_relogin_form.getElementsByClassName("ac-relogin-form")[0].classList.remove('filter');

                ac_relogin_overlay.classList.add('hide');

                if (relogin_times_submit == relogin_max_submit) {

                    ac_relogin_button.disabled = true;

                    ac_relogin_submit.classList.add('hide');

                    ac_relogin_max_submit.classList.remove('hide');

                    window.setTimeout(function(){

                        window.location = "login.php?action=logout&type=inactivity";

                    }, 2000);

                }else {

                    if (data == 1){

                        localStorage.setItem('relogin', false);

                        ac_relogin_pwd_input.value = "";

                        ac_relogin_form.classList.add('hide');
                        ac_relogin_button.disabled = true;

                        ac_relogin_submit.classList.add('hide');

                        ac_relogin_pwd_button.blur();

                        relogin_times_submit = 0;

                        relogin_timeout_clear();
                        session_time = setInterval(session_interval_check,5000);

                    }else if(data == 2){

                        ac_relogin_info.classList.remove('hide');

                        relogin_times_submit++;

                        ac_relogin_submit.classList.remove('hide');

                        ac_relogin_submit_count.innerHTML = relogin_times_submit;

                    }
                }
            }
        });
    });
});

function session_interval_check(){

    $(document).ready(function(){

        $.ajax({
            type: 'POST',
            url: 'login.php?action=session-timeout',
            data: '',
            success: function(data){

                if (data == 0){

                    relogin_timeout = setTimeout(relogin_timeout_redirect,300000);

                    ac_relogin_button.disabled = false;
                    ac_relogin_form.classList.remove('hide');

                    session_interval_clear();

                    localStorage.setItem('relogin', true);
                }
            }
        });
    });
}

function session_interval_clear(){

    clearInterval(session_time);

}

function relogin_timeout_redirect(){

    window.location = "login.php?action=logout&type=inactivity";

}

function relogin_timeout_clear(){

    clearTimeout(relogin_timeout);

}

window.addEventListener('storage', function(){

    let relogin  = localStorage.getItem('relogin');

    if (relogin === 'false'){
        console.log('sme tu');
        ac_relogin_pwd_input.value = "";

        ac_relogin_form.classList.add('hide');
        ac_relogin_button.disabled = true;

        ac_relogin_submit.classList.add('hide');

        ac_relogin_pwd_button.blur();

        relogin_times_submit = 0;

        relogin_timeout_clear();
        session_time = setInterval(session_interval_check,5000);
    }
});