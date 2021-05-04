/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


const clear_cache = document.querySelector('[data-toggle="clear-cache"]');

clear_cache.addEventListener("click", function(e){

    e.preventDefault();

    document.getElementById("ac-info").children[0].classList.add("hide");
    document.getElementById("ac-info").children[1].classList.add("hide");

    let ajax = new XMLHttpRequest();

    ajax.onload = function(){
        if (this.readyState == 4 && this.status == 200){
            if (this.response == 1){

                document.getElementById("ac-info").children[0].classList.remove("hide");

            }else if(this.response == 0){

                document.getElementById("ac-info").children[1].classList.remove("hide");

            }
        }
    };

    ajax.open("POST","index.php?action=clear-cache",true);
    ajax.send();
});

const alert_dismiss = document.querySelectorAll('[data-toggle="alert"]');

for (i = 0; i < alert_dismiss.length; i++){

    alert_dismiss[i].addEventListener("click", function(){

        this.setAttribute("aria-hidden","false");
        this.parentNode.classList.add("hide");

    });
}