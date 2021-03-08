
class BtnLogin {

  constructor($form){
    this.$form = $form ;
  //  this.form = new FormLogin($form);
    this.$btn = $form.find("#btn-login") ;
   // this.loader  = new Loader($form.find('.login-loader'));
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


gBtnLogin(gBtnLogin){

    this.gBtnLogin = gBtnLogin ;
}

init(){
      this.$btn.on("click",($event)=>{
        
        
         $event.preventDefault();
         $event.stopPropagation();
         if(this.isDisabled()){
            return ;
        }

        this.loader.show();
        this.disabledAllButton();
        const $url = this.$btn.data('url');
      
        this.form.removeFormError();
        this.form.removeValidState();

        this.ajax($url);

      })
}

ajax($url){
      $.ajax({
        url : $url ,
        method : "POST",
        dataType : "json",
        data :this.$form.serialize(),
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


disabled(){
    this.$btn.addClass('disabled');
}

  activate(){
    this.$btn.removeClass('disabled');
  }

  isDisabled(){
    return this.$btn.hasClass("disabled");
  }


  disabledAllButton() {
        this.fBtnLogin.disabled();
        this.gBtnLogin.disabled();
        this.disabled();
  }


  activateAllButton(){

        this.fBtnLogin.activate();
        this.gBtnLogin.activate();
        this.activate();
  }

}

module.exports = BtnLogin ; 




/*
class BtnLogin {

  constructor($form){
    this.$form = $form ;
    this.$btn = $form.find("#btn-login") ;
    this.$loader  = this.$form.find('.login-loader');
    this.$emailFeedback = this.$form.find("#feedback-email") ;
    this.$email = this.$form.find("#email") ;
    this.$password = this.$form.find("#password") ;
    this.$passwordFeedback = this.$form.find("#feedback-password") ;
    this.$globalFeedback = this.$form.find("#feedback-global") ;
  }

  init(){
      this.$btn.on("click",($event)=>{
        
        
         $event.preventDefault();
         $event.stopPropagation();
         if(this.isDisabled()){
            return ;
        }

        this.showLoader();
        const $url = this.$btn.data('url');

         this._removeFormError();
         this._removeValidState();
         
         $.ajax({
             url : $url ,
             method : "POST",
             dataType : "json",
             data :this.$form.serialize(),
         })
         .done(( data, textStatus, jqXHR ) => {
          
            if(data && data.success){
                //redirect
                document.location = data.success.redirect_url ;
            } else {
                this.hideLoader();
                this._addFormOtherError(this.$globalFeedback,"Sorry, We connot connect to the server right now, try to check again later!");
            }
           
         })
         .fail(( jqXHR, textStatus, errorThrown ) =>{
            // console.log(jqXHR);
            const jsonResponse = jqXHR.responseJSON ;
            if(jsonResponse && jsonResponse.error){
                this._addFormError(jsonResponse);
            }else {
                this._addFormOtherError(this.$globalFeedback,"Sorry, We connot connect to the server right now, try to check again later!");
            }

            this.hideLoader();
         });

      })
  }

  _addFormError(jsonResponse){
   // console.log("show");
    //this._showFeedBack();
    const error = jsonResponse.error ;
    let $feedback = this.$globalFeedback ;
    //let $feedbackEmail = this.$email ;
    
    if(error.email){
        $feedback = this.$emailFeedback;
        $feedback.text(error.email);
        this._addElementIsInValid(this.$email);
        this._showElementFeedBack($feedback,false);
    }else if(error.password){
        $feedback= this.$passwordFeedback ;
        $feedback.text(error.password);
        this._showElementFeedBack($feedback,false);
        this._addElementIsInValid(this.$password);
        this._makeEmailValid();
    }else {
        this._addFormOtherError($feedback,error.global);
    }
    
  }

  _makeEmailValid(){
    this.$emailFeedback.text("look good");
    this._showElementFeedBack(this.$emailFeedback,true);
    this._addElementIsValid(this.$email);
  }


  _addElementIsValid($element){
      $element.removeClass('is-invalid');
      $element.addClass('is-valid');

  }

  _addElementIsInValid($element){
    $element.removeClass('is-valid');
    $element.addClass('is-invalid');

}

  _removeElementValidState($element){
    $element.removeClass('is-valid');
    $element.removeClass('is-invalid');
}

_removeValidState(){
    this._removeElementValidState(this.$email);
    this._removeElementValidState(this.$password);
}


  _removeFormError() {
   // console.log("remove");
    this._hideFeedBack();
  }

  _showFeedBack(){
   // console.log("add class");
    const $feedback = this.$form.find('.feedback');
    $feedback.each((index, element )=>{
        this._showElementFeedBack($(element));
    })
  }

  _showElementFeedBack($element,isValid=true){
    $element.addClass('show-feedback');
    if($element.is(this.$globalFeedback)){

   }else {
        if(isValid){
            $element.addClass('valid-feedback');
        }else {
            $element.addClass('invalid-feedback');
        }
   }
    
  }


  _hideFeedBack(){
    const $feedback = this.$form.find('.feedback');
    $feedback.each((index, element )=>{
        $(element).removeClass('show-feedback');
        $(element).removeClass('valid-feedback');
        $(element).removeClass('invalid-feedback');
        //remove content of feedback
        $(element).empty();
    })
  }

  showLoader() {
    this.$loader.addClass('loader-show');
  }


  hideLoader() {
    this.$loader.removeClass('loader-show');
  }

  _addFormOtherError($feedback,error){
    $feedback.text(error)
    this._showElementFeedBack($feedback,false);
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

module.exports = BtnLogin ; */