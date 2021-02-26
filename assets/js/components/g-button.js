    class GoogleBtnLogin {

        constructor(){
            this.googleUser = {} ;
        }

        static get client_id(){
            return '50754451830-t7qmcl622tbho5k20riecrp0psap01bq.apps.googleusercontent.com' ;
        }

        static get cookiepolicy() {
            return 'single_host_origin';
        }

        init(){

            gapi.load('auth2', () =>{
                // Retrieve the singleton for the GoogleAuth library and set up the client.
                let auth2 = gapi.auth2.init({
                client_id: GoogleBtnLogin.client_id,
                cookiepolicy: GoogleBtnLogin.cookiepolicy,
                // Request scopes in addition to 'profile' and 'email'
                //scope: 'additional_scope'
                });
            });
        }

    }

    module.exports = GoogleBtnLogin ; 