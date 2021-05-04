/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


const add_media = document.querySelectorAll('[data-add="media"]');

for (i = 0; i < add_media.length; i++){

    add_media[i].addEventListener("click", function(e){

        e.preventDefault();

        let location = this.getAttribute("aria-value");
        let type = this.getAttribute("aria-type");

        if (type === "image"){

            tinymce.get("editor").execCommand('mceInsertContent', false, '<img src="'+location+'" alt="Image">');

        }else if(type === "video"){

            tinymce.get("editor").execCommand('mceInsertContent', false, '<video src="'+location+'" controls controlslist="nodownload"></video>');

        }else if(type === "audio"){

            tinymce.get("editor").execCommand('mceInsertContent', false, '<audio src="'+location+'" controls controlslist="nodownload"></audio>');

        }
    });
}