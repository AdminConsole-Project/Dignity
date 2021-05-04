/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


let ac_pwd_button = document.getElementById("ac-pwd-button");
let ac_pwd_input = document.getElementById("ac-pwd-input");
let ac_pwd_icon = document.getElementById("ac-pwd-icon");

ac_pwd_button.addEventListener("click",function(){

    if (ac_pwd_input.type === "password"){

        ac_pwd_input.type = "text";
        ac_pwd_input.focus();
        ac_pwd_icon.classList.remove('fa-eye');
        ac_pwd_icon.classList.add('fa-eye-slash');

    }else {

        ac_pwd_input.type = "password";
        ac_pwd_input.focus();
        ac_pwd_icon.classList.remove('fa-eye-slash');
        ac_pwd_icon.classList.add('fa-eye');

    }
});