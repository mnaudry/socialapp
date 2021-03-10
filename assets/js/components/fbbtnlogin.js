class FbBtnLogin {

        constructor($form){
      // this.version = 'v10.0';
        //  this.autoLogAppEvents = true ;
        //  this.xfbml = false;
         // this.$form = new FormLogin($form);
          this.$btn = $form.find('#fb-login-btn-id');
          this.$token = $form.find('input[name="_csrf_token"]').val();
         // this.$loader = new Loader($form.find('.login-loader'));
        //  this.$btnLogin = new BtnLogin_fb($form);
       // console.log(FB);

        }

        form(form){
            this.form = form ;
        }


        loader(loader){
            this.loader = loader ;
        }


        btnLogin(btnLogin){

            this.btnLogin = btnLogin ;
        }


        gBtnLogin(gBtnLogin){

            this.gBtnLogin = gBtnLogin ;
        }

        init(){

           // const id = this.$btn.data('appid');
           // console.log(this.version);
           /* global.fbAsyncInit = () => {
                console.log("call this");
                FB.init({
                  appId            : id,
               //   autoLogAppEvents : this.autoLogAppEvents,
                  xfbml            : this.xfbml,
                  version          : this.version,
                  status           : true,
                });
                console.log("init finish");
            };*/


            this.$btn.on("click",($event)=>{

               if(this.isDisabled()){
                    return ;
               }
               this.loader.show();
               this.disabledAllButton();
               this.form.removeFormError();
               this.form.removeValidState();
               this.checkStateAndLogin();
         
            })
        }

        checkStateAndLogin(){
            //console.log(this.$token);
          
            FB.getLoginStatus((response) => {   
                if(response ){
                    if(response.status === "connected"){
                    // console.log(response);
                     this.ajax(response.authResponse.accessToken);
                       ////this.redirect();
                      // this.ajax();
                    }else {
                         this.login();
                    }
                }

              });

        }

        login(){
           // console.log("login");
           
            FB.login((response)=> {
                if (response.status === 'connected') {
                  // Logged into your webpage and Facebook.
                  //redirect 
                    //document.location = app_login_facebook
                   // this.redirect();
                 // this.ajax();
                    // console.log(response);
                     this.ajax(response.authResponse.accessToken);
                } else {
                    //Connexion impossible. Vous devez accorder les autorisations nécessaires à Pinterest
                    this.loader.hide();
                    this.activateAllButton();
                    this.form.addFormOtherError("Connexion impossible. Vous devez accorder les autorisations nécessaires à Lobaa");
                  
                }
              },{scope: 'public_profile,email'});
        }


        disabledAllButton() {
            this.btnLogin.disabled();
            this.gBtnLogin.disabled();
            this.disabled();
        }


        activateAllButton(){

            this.btnLogin.activate();
            this.gBtnLogin.activate();
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

        redirect(){
            const $url = this.$btn.data('url');
            document.location = $url ;
        }

        ajax(accessToken){

            const $url = this.$btn.data('url');

            $.ajax({
                url : $url ,
                method : "POST",
                dataType : "json",
                data :{'_csrf_token' : this.$token , 'accessToken' : accessToken },
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

    }

    module.exports = FbBtnLogin ; 