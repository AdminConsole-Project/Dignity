/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


const lang_set_button = document.querySelectorAll('[data-toggle="admin-language"]');

for (i = 0; i < lang_set_button.length; i++){

    lang_set_button[i].addEventListener("click", function(e){

        e.preventDefault();

        document.getElementById("ac-info").children[0].classList.add("hide");
        document.getElementById("ac-info").children[1].classList.add("hide");

        const language_default = document.querySelectorAll('[data-status="default"]');

        for (i = 0; i < language_default.length; i++) {

            language_default[i].children[0].classList.toggle('fa-times-circle');
            language_default[i].children[0].classList.toggle('fa-check-circle');

            language_default[i].classList.remove('text-success');
            language_default[i].classList.add('text-danger');

            language_default[i].removeAttribute("style");
            language_default[i].removeAttribute("data-status");
        }

        this.children[0].classList.toggle('fa-times-circle');
        this.children[0].classList.toggle('fa-check-circle');

        this.classList.remove('text-danger');
        this.classList.add('text-success');

        this.setAttribute("data-status","default");
        this.style.pointerEvents = "none";

        this.blur();

        let formData = new FormData();
        formData.append("id", this.getAttribute("aria-value"))

        let ajax = new XMLHttpRequest();

        ajax.onload = function(){
            if (this.readyState == 4 && this.status == 200){
                if (this.response == 1){

                    document.getElementById("ac-info").children[1].classList.add("hide");
                    document.getElementById("ac-info").children[0].classList.remove("hide");
                    document.getElementById("ac-info").children[0].children[0].setAttribute("aria-hidden","false");

                    document.getElementById("ac-reload").classList.remove("hide");

                }else if(this.response == 0){

                    document.getElementById("ac-info").children[0].classList.add("hide");
                    document.getElementById("ac-info").children[1].classList.remove("hide");
                    document.getElementById("ac-info").children[0].children[0].setAttribute("aria-hidden","false");

                }
            }
        };

        ajax.open("POST","extensions.php?action=language_default",true);
        ajax.send(formData);
    });
}

document.getElementById("ac-reload").addEventListener("click", function(e){

    e.preventDefault();
    location.reload();

});

const alert_dismiss = document.querySelectorAll('[data-toggle="alert"]');

for (i = 0; i < alert_dismiss.length; i++){

    alert_dismiss[i].addEventListener("click", function(){

        let status = this.getAttribute("aria-hidden");

        if (status != "true"){

            this.setAttribute("aria-hidden","false");
            this.parentNode.classList.add("hide");

        }
    });
}