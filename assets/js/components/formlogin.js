
class FormLogin {

    constructor($form){
      this.$form = $form ;
      this.$emailFeedback = this.$form.find("#feedback-email") ;
      this.$email = this.$form.find("#email") ;
      this.$password = this.$form.find("#password") ;
      this.$passwordFeedback = this.$form.find("#feedback-password") ;
      this.$globalFeedback = this.$form.find("#feedback-global") ;
    }
  
    addFormError(jsonResponse){
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
          this.makeEmailValid();
      }else {
          this.addFormOtherError(error.global);
      }
      
    }
  
    makeEmailValid(){
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
  
  removeValidState(){
      this._removeElementValidState(this.$email);
      this._removeElementValidState(this.$password);
  }
  
  
   removeFormError() {
     // console.log("remove");
      this.hideFeedBack();
   }
  
   showFeedBack(){
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
  
  
    hideFeedBack(){
    
      this.$form.find('#noticeflash').remove();
      const $feedback = this.$form.find('.feedback');
      $feedback.each((index, element )=>{
          $(element).removeClass('show-feedback');
          $(element).removeClass('valid-feedback');
          $(element).removeClass('invalid-feedback');
          //remove content of feedback
          $(element).empty();
      })
    }
  
  
    addFormOtherError(error){
      this.$globalFeedback.text(error)
      this._showElementFeedBack(this.$globalFeedback,false);
    }
  
  }
  
  module.exports = FormLogin ; 