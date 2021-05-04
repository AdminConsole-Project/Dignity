/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


$(document).ready(function(){

    $('#ac-form').on('submit', function(e){

        e.preventDefault();
        let post_url = $(this).attr('action');

        document.getElementById("ac-info").children[0].classList.add("hide");
        document.getElementById("ac-info").children[1].classList.add("hide");
        document.getElementById("ac-info").children[2].classList.add("hide");

        $.ajax({
            type: 'POST',
            url: post_url,
            data: $(this).serialize(),
            success: function(data){

                if (data == 1){

                    document.getElementById("ac-info").children[0].classList.remove("hide");
                    window.location.reload();

                }else if(data == 4){

                    document.getElementById("ac-info").children[1].classList.remove("hide");

                }else if(data == 0){

                    document.getElementById("ac-info").children[2].classList.remove("hide");

                }
            }
        });
    });
});

const alert_dismiss = document.querySelectorAll('[data-toggle="alert"]');

for (i = 0; i < alert_dismiss.length; i++){

    alert_dismiss[i].addEventListener("click", function(){

        this.setAttribute("aria-hidden","false");
        this.parentNode.classList.add("hide");

    });
}