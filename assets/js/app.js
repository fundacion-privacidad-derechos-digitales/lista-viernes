const $ = require('jquery');
const Inputmask = require('inputmask');

$(document).ready(() => {
    const inputs = document.getElementsByClassName('phone-mask')
    const im = new Inputmask('999 999 999');
    im.mask(inputs);
});