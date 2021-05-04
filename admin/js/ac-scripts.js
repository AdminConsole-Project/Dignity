/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


const collapse = document.querySelectorAll('[data-toggle="ac-collapse"]')

for (i = 0; i < collapse.length; i++) {

    collapse[i].addEventListener("click", function(){

        this.addEventListener("mousedown", function(e){

            e.preventDefault();

        });

        const content = this.nextElementSibling;

        if (content.style.maxHeight){

            content.classList.add("ac-collapsing");

            content.style.maxHeight = null;
            this.setAttribute("aria-expanded", false);

            collapsing(content);

        } else {

            content.classList.add("ac-collapsing");

            content.style.maxHeight = content.scrollHeight + "px";
            this.setAttribute("aria-expanded", true);

            collapsing(content);
        }
    });

    function collapsed(){

        const content = collapse[i].nextElementSibling;
        const expanded = collapse[i].getAttribute("aria-expanded");

        if (expanded == "true"){

            content.style.maxHeight = content.scrollHeight + "px";

        }else if(expanded == "false"){

            content.style.maxHeight = null;

        }
    }

    function collapsing(element){

        element.addEventListener('transitionend', function() {

            this.classList.remove("ac-collapsing");

        });
    }

    collapsed();
}

document.getElementById("navbar-sidebar-collapse").addEventListener("click", function(){

    document.getElementById("ac-content").classList.toggle("active");
    document.getElementById("ac-sidebar").classList.toggle("active");
    document.getElementById("ac-navbar").classList.toggle("active");
    document.getElementById("navbar-sidebar-collapse").classList.toggle("active");

});

document.getElementById("mobile-sidebar-collapse").addEventListener("click", function(){

    document.getElementById("ac-content").classList.toggle("active");
    document.getElementById("ac-sidebar").classList.toggle("active");
    document.getElementById("ac-navbar").classList.toggle("active");
    document.getElementById("navbar-sidebar-collapse").classList.toggle("active");

});

window.addEventListener('online', function(){

    document.getElementById("ac-of-detector").children[1].classList.add("hide");
    document.getElementById("ac-of-detector").children[0].classList.remove("hide");

    window.setTimeout(function(){

        document.getElementById("ac-of-detector").children[0].classList.add("hide");

    }, 5000);
});

window.addEventListener('offline', function(){

    document.getElementById("ac-of-detector").children[0].classList.add("hide");
    document.getElementById("ac-of-detector").children[1].classList.remove("hide");

});

document.getElementById("ac-navbar-logo").src = "img/logo-ow.svg";
document.getElementById("ac-relogin-logo").src = "img/logo-ob.svg";