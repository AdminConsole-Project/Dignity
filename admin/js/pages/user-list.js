/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


const delete_button = document.querySelectorAll('[data-action="delete"]');

for (i = 0; i < delete_button.length; i++){

    delete_button[i].addEventListener("click", function(e){

        e.preventDefault();

        document.getElementById("ac-delete-info").children[0].classList.add("hide");
        document.getElementById("ac-delete-info").children[1].classList.add("hide");
        document.getElementById("ac-delete-info").children[2].classList.add("hide");
        document.getElementById("ac-delete-info").children[3].classList.add("hide");

        let prompt_text = this.getAttribute("aria-text");
        let alias = this.getAttribute("aria-alias");

        let alert_box = prompt(prompt_text+": "+alias);

        if (alert_box == null || alert_box == ""){

            document.getElementById("ac-delete-info").children[1].classList.remove("hide");

        }else {
            if (alert_box == alias){

                let formData = new FormData();
                formData.append("id", this.getAttribute("aria-value"))

                let ajax = new XMLHttpRequest();

                ajax.onload = function(){
                    if (this.readyState == 4 && this.status == 200){
                        if (this.response == 1){

                            document.getElementById("ac-delete-info").children[0].classList.remove("hide");
                            document.getElementById("ac-delete-info").children[0].children[0].setAttribute("aria-hidden","false");
                            window.location.reload();

                        }else if(this.response == 0){

                            document.getElementById("ac-delete-info").children[3].classList.remove("hide");
                            document.getElementById("ac-delete-info").children[3].children[0].setAttribute("aria-hidden","false");

                        }
                    }
                };

                ajax.open("POST","user.php?action=delete",true);
                ajax.send(formData);

            }else {

                document.getElementById("ac-delete-info").children[2].classList.remove("hide");

            }
        }
    });
}

const alert_dismiss = document.querySelectorAll('[data-toggle="alert"]');

for (i = 0; i < alert_dismiss.length; i++){

    alert_dismiss[i].addEventListener("click", function(){

        this.setAttribute("aria-hidden","false");
        this.parentNode.classList.add("hide");

    });
}