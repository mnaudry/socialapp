const GBtnLogin = require('./gbtnlogin');

const BtnLogin = require('./btnlogin');

const FbBtnLogin = require('./fbbtnlogin');

const FormLogin = require('./formlogin');

const Loader = require('./loader');



class Login {
    constructor($form){
        this.form = new FormLogin($form);
        this.loader = new Loader($form.find('.login-loader'));
        this.gBtn = new GBtnLogin($form);
        this.btn = new BtnLogin($form);
        this.fBtn = new FbBtnLogin($form);
        //this.btn.init();
       // this.f_btn.init();
    }

    initFbLogin(){

        this.fBtn.form(this.form) ;
        this.fBtn.loader(this.loader);
        this.fBtn.btnLogin(this.btn) ;
        this.fBtn.gBtnLogin(this.gBtn) ;
        this.fBtn.init();
    }


    initLogin(){

        this.btn.form(this.form) ;
        this.btn.loader(this.loader);
        this.btn.fBtnLogin(this.fBtn) ;
        this.btn.gBtnLogin(this.gBtn) ;
        this.btn.init();
    }

    initGLogin(){

        this.gBtn.form(this.form) ;
        this.gBtn.loader(this.loader);
        this.gBtn.btnLogin(this.btn) ;
        this.gBtn.fBtnLogin(this.fBtn) ;
        this.gBtn.init();

    }
}


module.exports = Login ; 