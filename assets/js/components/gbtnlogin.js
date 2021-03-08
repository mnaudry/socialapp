    class GBtnLogin {

        constructor($form){
            this.googleUser = {} ;
            this.$btn = $form.find('#g-login-btn-id');
        }

        static get client_id(){
            return '50754451830-t7qmcl622tbho5k20riecrp0psap01bq.apps.googleusercontent.com' ;
        }

        static get cookiepolicy() {
            return 'single_host_origin';
        }


        form(form){
            this.form = form ;
        }


        loader(loader){
            this.loader = loader ;
        }


        fBtnLogin(fBtnLogin){

            this.fBtnLogin = fBtnLogin ;
        }


        btnLogin(btnLogin){

            this.btnLogin = btnLogin ;
        }

        init(){

           /* gapi.load('auth2', () =>{
                // Retrieve the singleton for the GoogleAuth library and set up the client.
                let auth2 = gapi.auth2.init({
                client_id: GoogleBtnLogin.client_id,
                cookiepolicy: GoogleBtnLogin.cookiepolicy,
                // Request scopes in addition to 'profile' and 'email'
                //scope: 'additional_scope'
                });
            });*/
        }


        disabledAllButton() {
            this.btnLogin.disabled();
            this.fBtnLogin.disabled();
            this.disabled();
        }


        activateAllButton(){

            this.btnLogin.activate();
            this.fBtnLogin.activate();
            this.activate();
        }


        disabled(){
            this.$btn.addClass('disabled');
        }
        
        activate(){
            this.$btn.removeClass('disabled');
        }
        
        isDisabled(){
            return this.$btn.hasClass("disabled");
        }

    }

    module.exports = GBtnLogin ; 