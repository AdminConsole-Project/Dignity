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
    url: "media.php?action=upload",
    autoProcessQueue: true,
    maxFilesize: ac_dropzone_max_file_size,

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

    document.querySelector("#ac-upload-total-progress").parentNode.classList.remove("d-none");

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

    if (response == 1){

        file.previewTemplate.querySelector(".fa-check-circle").parentNode.classList.remove("invisible");
        file.previewTemplate.querySelector(".fa-check-circle").parentNode.classList.add("visible");

    }else if(response == 0){

        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.remove("invisible");
        file.previewTemplate.querySelector(".fa-exclamation-triangle").parentNode.classList.add("visible");

        file.previewTemplate.querySelector(".progress-bar").classList.add("bg-danger");

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

ac_dropzone.on("totaluploadprogress", function(progress){

    document.querySelector("#ac-upload-total-progress").style.width = progress + "%";
    document.querySelector("#ac-upload-total-progress-text").innerHTML = Math.round(progress) + "%";

});

ac_dropzone.on("drop", function(){

    if (document.querySelector("#ac-upload-list").querySelector(".drag-here")){

        document.querySelector("#ac-upload-list").querySelector(".drag-here").remove();

    }
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