import '../css/logout.scss';
const $ =  require('jquery');

$( document ).ready(function() {

    if($('#fb-root').length){
        console.log("yes facebook logout");
        FB.getLoginStatus((response) => {   
            if(response ){
                if(response.status === "connected"){
                    FB.logout(function(response) {
                     // console.log(response);
                      document.location = document.getElementById('fb-root').dataset.redirect;
                    });
    
                }else {
                    document.location = document.getElementById('fb-root').dataset.redirect;    
                }
            }
    
          });
    }
   

})
