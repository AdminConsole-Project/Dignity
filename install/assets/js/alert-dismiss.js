/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


const alert_dismiss = document.querySelectorAll('[data-toggle="alert"]');

alert_dismiss.forEach(function(element){
    element.addEventListener("click", function(){

        this.setAttribute("aria-hidden","false");
        this.parentNode.classList.add("hide");

    });
});