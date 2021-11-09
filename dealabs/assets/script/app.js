import '@fortawesome/fontawesome/index';
import '../styles/app.css'
import 'bootstrap';

const $ = require('jquery')
require('popper.js')
require('@popperjs/core')
require('bootstrap')

$('.dropdown').click(function(){
    $('.dropdown-menu').toggleClass('show');
});

document.addEventListener('DOMContentLoaded', function() {
    // Popups
    let div = $('.popup-div')
    div.show();
    setTimeout(() => {  div.fadeOut(); }, 3000);
});

$('.js-loginEditButton').click(function(){
    $('.js-loginEditText').toggleClass('hide');
    $('.js-loginEditControls').toggleClass('hide');
});

$('.js-descriptionEditButton').click(function(){
    $('.js-descriptionEditText').toggleClass('hide');
    $('.js-descriptionEditControls').toggleClass('hide');
});

$('.js-emailEditButton').click(function(){
    $('.js-emailEditText').toggleClass('hide');
    $('.js-emailEditControls').toggleClass('hide');
});

$('.js-passwordEditButton').click(function(){
    $('.js-passwordEditText').toggleClass('hide');
    $('.js-passwordEditControls').toggleClass('hide');
});

$('.js-deleteEditButton').click(function(){
    $('.js-deleteEditText').toggleClass('hide');
    $('.js-deleteEditControls').toggleClass('hide');
});