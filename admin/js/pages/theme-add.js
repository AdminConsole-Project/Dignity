/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


let ac_template_div = document.querySelector("#ac-upload-template");
ac_template_div.removeAttribute("id");
let ac_template = ac_template_div.parentNode.innerHTML;
ac_template_div.parentNode.removeChild(ac_template_div);

let ac_dropzone_max_file_size = document.querySelector("#uploader-max-size").value;

let ac_dropzone = new Dropzone(document.body,{
    url: "themes.php?action=add",
    autoProcessQueue: false,
    maxFiles: 1,
    maxFilesize: ac_dropzone_max_file_size,
    acceptedFiles: '.zip',

    previewsContainer: "#ac-upload-list",
    previewTemplate: ac_template,
    clickable: "#ac-upload-browse",
    init: function() {

        let uploader = '<div id="ac-uploader-drop" class="d-none"><div class="uploader-drop-content"><div class="uploader-drop-text"></div></div></div>';

        uploader_create = document.createElement("div");
        uploader_create.innerHTML = uploader;

        document.body.insertBefore(uploader_create, document.body.firstChild);

        let uploader_drop_text = document.querySelector("#uploader-drop-text").value;
        document.querySelector("#ac-uploader-drop").querySelector(".uploader-drop-text").innerHTML = uploader_drop_text;
    }
});

ac_dropzone.on("addedfile", function(file){

    if (document.querySelector("#ac-upload-list").querySelector(".drag-here")){

        document.querySelector("#ac-upload-list").querySelector(".drag-here").remove();

    }
});

ac_dropzone.on("sending", function(file){

    document.querySelector("#ac-uploader-counter").parentNode.parentNode.classList.remove("d-none");
    document.querySelector("#ac-uploader-counter-total").innerHTML = ac_dropzone.files.length;

});

ac_dropzone.on("success", function(file, response){

    let count = document.querySelector("#ac-uploader-counter").innerHTML;

    count++;

    document.querySelector("#ac-uploader-counter").innerHTML = count;

    document.getElementById("ac-info").children[0].classList.add("hide");
    document.getElementById("ac-info").children[1].classList.add("hide");
    document.getElementById("ac-info").children[2].classList.add("hide");
    document.getElementById("ac-info").children[3].classList.add("hide");
    document.getElementById("ac-info").children[4].classList.add("hide");

    if (response == 1){

        file.previewTemplate.querySelector(".fa-check-circle").parentNode.classList.remove("invisible");
        file.previewTemplate.querySelector(".fa-check-circle").parentNode.classList.add("visible");

        document.getElementById("ac-info").children[0].classList.remove("hide");

    }else if(response == 0){

        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.remove("invisible");
        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.add("visible");

        file.previewTemplate.querySelector(".progress-bar").classList.add("bg-danger");

        document.getElementById("ac-info").children[4].classList.remove("hide");

    }else if(response == 2){

        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.remove("invisible");
        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.add("visible");

        file.previewTemplate.querySelector(".progress-bar").classList.add("bg-warning");

        document.getElementById("ac-info").children[2].classList.remove("hide");

    }else if(response == 3){

        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.remove("invisible");
        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.add("visible");

        file.previewTemplate.querySelector(".progress-bar").classList.add("bg-warning");

        document.getElementById("ac-info").children[3].classList.remove("hide");

    }else if(response == 4){

        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.remove("invisible");
        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.add("visible");

        file.previewTemplate.querySelector(".progress-bar").classList.add("bg-info-dark");

        document.getElementById("ac-info").children[1].classList.remove("hide");

    }
});

ac_dropzone.on("error", function(file){

    file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.remove("invisible");
    file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.add("visible");

    file.previewTemplate.querySelector(".progress").remove();

});

ac_dropzone.on("maxfilesexceeded", function(file){

    file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.remove("invisible");
    file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.add("visible");

    file.previewTemplate.querySelector(".progress").remove();

});

ac_dropzone.on("drop", function(e){

    e.preventDefault();

    return false;
});

document.querySelector('#ac-upload-start').addEventListener("click", function(){

    ac_dropzone.processQueue();

});

document.querySelector("#ac-uploader-drop").addEventListener("drop", function() {

    document.querySelector("#ac-uploader-drop").classList.add("d-none")

});

document.body.addEventListener("dragover", function() {

    document.querySelector("#ac-uploader-drop").classList.remove("d-none");

});

document.body.addEventListener("dragenter", function() {

    document.querySelector("#ac-uploader-drop").classList.remove("d-none");

});

document.querySelector("#ac-uploader-drop").addEventListener("dragleave", function() {

    document.querySelector("#ac-uploader-drop").classList.add("d-none");

});