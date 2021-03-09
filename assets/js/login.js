import '../css/login.scss';
const $ =  require('jquery');

const Login = require('./components/login');

$( document ).ready(function() {

    const $form = $('#login-form');

    const login = new Login($form);

    login.initFbLogin();
    login.initGLogin();
    login.initLogin();
});







