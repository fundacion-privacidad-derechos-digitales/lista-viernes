/**
 * App styles
 */
require('../css/common.scss');

/**
 * Import jQuery as global var
 */
window.$ = window.jQuery = require('jquery');
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/**
 * Bootstrap
 */
require('popper.js');
require('bootstrap');