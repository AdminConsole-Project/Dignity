/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


let ac_info = document.getElementById("ac-info");

$(document).ready(function(){

    $('#ac-form').on('submit', function(e){

        for (i = 0; i < ac_info.children.length; i++) {

            ac_info.children[i].classList.add("hide");

        }

        e.preventDefault();
        let post_url = $(this).attr('action');

        $.ajax({
            type: 'POST',
            url: post_url,
            data: $(this).serialize(),
            success: function(data){

                if (data == 0){

                    ac_info.children[1].classList.remove("hide");
                    ac_info.children[1].children[0].setAttribute("aria-hidden","false");

                }else if(data == 1){

                    ac_info.children[0].classList.remove("hide");
                    ac_info.children[0].children[0].setAttribute("aria-hidden","false");

                }
            }
        });
    });
});

const logout_all = document.getElementById("logout-all");

logout_all.addEventListener("click", function(e){

    e.preventDefault;
    console.log("sme tu");
    let ajax = new XMLHttpRequest();

    ajax.onload = function(){
        if (this.readyState == 4 && this.status == 200){
            if (this.response == 1){

                ac_info.children[0].classList.remove("hide");
                ac_info.children[0].children[0].setAttribute("aria-hidden","false");
                window.location.reload();

            }else if(this.response == 0){

                ac_info.children[1].classList.remove("hide");
                ac_info.children[1].children[0].setAttribute("aria-hidden","false");

            }
        }
    };

    ajax.open("GET", "user.php?action=logout-from-all", true);
    ajax.send();
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