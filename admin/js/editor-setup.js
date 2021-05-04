/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


tinymce.init({
    selector: '#editor',
    toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | paste pastetext | link image media table',
	plugins: [
      'advlist anchor hr charmap image link lists media nonbreaking paste table visualblocks visualchars wordcount',
    ],
    removed_menuitems: 'newdocument, restoredraft, fontsizeselect, forecolor, backcolor, lineheight, fontsizes, fontformats',
    browser_spellcheck: true,
    branding: false,
    height: 500,
    entity_encoding : 'raw',
    table: 'responsive',
    convert_urls: false,
    document_base_url: '../../../../',
    contextmenu: false
});