/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


const delete_button = document.querySelectorAll('[data-action="delete"]');

for (i = 0; i < delete_button.length; i++){

    delete_button[i].addEventListener("click", function(e){

        e.preventDefault();

        document.getElementById("ac-info").children[0].classList.add("hide");
        document.getElementById("ac-info").children[1].classList.add("hide");

        let formData = new FormData();
        formData.append("id", this.getAttribute("aria-value"))

        let ajax = new XMLHttpRequest();

        ajax.onload = function(){
            if (this.readyState == 4 && this.status == 200){
                if (this.response == 1){

                    document.getElementById("ac-info").children[0].classList.remove("hide");
                    window.location.reload();

                }else if(this.response == 0){

                    document.getElementById("ac-info").children[3].classList.remove("hide");

                }
            }
        };

        ajax.open("POST","media.php?action=delete",true);
        ajax.send(formData);
    });
}

const copy_button = document.querySelectorAll('[data-action="copy"]');

for (i = 0; i < copy_button.length; i++){

    copy_button[i].addEventListener("click", function(e){

        e.preventDefault();

        let copy = this.getAttribute("aria-location");

        let dummy = document.createElement("input");

        document.body.appendChild(dummy);
        dummy.value = copy;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);

        document.execCommand("copy");
    });
}

const alert_dismiss = document.querySelectorAll('[data-toggle="alert"]');

for (i = 0; i < alert_dismiss.length; i++){

    alert_dismiss[i].addEventListener("click", function(){

        this.setAttribute("aria-hidden","false");
        this.parentNode.classList.add("hide");

    });
}