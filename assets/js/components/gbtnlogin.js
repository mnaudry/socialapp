const thisBooleanValue = require("es-abstract/2015/thisBooleanValue");
const thisNumberValue = require("es-abstract/2015/thisNumberValue");

    class GBtnLogin {

        constructor($form){
            this.googleUser = {} ;
            this.btnId = 'g-login-btn-id' ;
            this.$btn = $form.find("#" + this.btnId);
            //console.log(this.$btn);
           
            this.cookiepolicy = 'single_host_origin' ;
            this.client_id = this.$btn.data('appid');
            this.$token = $form.find('input[name="_csrf_token"]').val();
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

            gapi.load('auth2', () =>{
                // Retrieve the singleton for the GoogleAuth library and set up the client.
                
                let auth2 = gapi.auth2.init({
                client_id: this.client_id ,
                cookiepolicy: this.cookiepolicy,
                // Request scopes in addition to 'profile' and 'email'
                //scope: 'additional_scope'
                }).then((auth2)=>{
                    
                    this.onInit(auth2);
                    }
                    ,(error)=>{
                    
                    this.onError(error);
                    }
                );


            });
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

        onFailure(response){
            //console.log(error);
            //popup_closed_by_user
            this.loader.hide();
            this.activateAllButton();
            if(response.error === "popup_closed_by_user" || response.error === "access_denied")
                this.form.addFormOtherError("Connexion impossible. Vous devez accorder les autorisations nécessaires à Lobaa");
            else 
                this.form.addFormOtherError("Sorry, We connot connect to the server right now, try to check again later!");
        }

        onSuccess(response){
           // console.log(response.getAuthResponse());
            const $url = this.$btn.data('url');

            $.ajax({
                url : $url ,
                method : "POST",
                dataType : "json",
                data :{'_csrf_token' : this.$token , 'code' : response.code },
               // data :{'_csrf_token' : this.$token , 'accessToken' : response.getAuthResponse().access_token },
               // data :{'_csrf_token' : this.$token , 'id_token' : response.getAuthResponse().id_token },
            })
            .done(( data, textStatus, jqXHR ) => {
             
               if(data && data.success){
                   //redirect
                   document.location = data.success.redirect_url ;
               } else {
                   this.loader.hide();
                   this.form.addFormOtherError("Sorry, We connot connect to the server right now, try to check again later!");
                   this.activateAllButton();
               }
              
            })
            .fail(( jqXHR, textStatus, errorThrown ) =>{
               // console.log(jqXHR);
               const jsonResponse = jqXHR.responseJSON ;
               if(jsonResponse && jsonResponse.error){
                   this.form.addFormError(jsonResponse);           
               }else {
                   this.form.addFormOtherError("Sorry, We connot connect to the server right now, try to check again later!");
               }
   
               this.loader.hide();
               this.activateAllButton();
            });
        }

        onInit(auth2){

            //console.log("ok perfect");

            //console.log(auth2);
           //    console.log(auth2);
           this.$btn.on("click",($event)=>{

                if(this.isDisabled()){
                     return ;
                }
                this.loader.show();
                this.disabledAllButton();
                this.form.removeFormError();
                this.form.removeValidState();

                // retry this in the next release is the best method but not working well with knpu oauth2
                auth2.grantOfflineAccess().then((resp)=>{
                   // console.log(resp);
                    this.signIn(resp);
                },(resp)=>{

                    this.onFailure(resp);

                });
            })

          /* auth2.attachClickHandler(this.btnId, {}, (response)=> {
                this.onSuccess(response);
            }, (response) => {
                this.onFailure(response);
            });*/

        }

        onError(error){
            console.log(error.details);
            this.disabled();
            //this.loader.hide();
           /// this.activateAllButton();
           // this.form.addFormOtherError("Sorry, We connot connect to the server right now, try to check again later!");
        }

        signIn(response){
            console.log(response);
            if(response.error){
                this.onFailure(response);
            }else {
                if(response.code){
                    this.onSuccess(response);
                }
            }
             
        }

    }

    module.exports = GBtnLogin ; 