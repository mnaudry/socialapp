class Loader {
    constructor($loader){
        this.$loader  = $loader ;
    }

    show() {
        this.$loader.addClass('loader-show');
      }
    
    
    hide() {
        this.$loader.removeClass('loader-show');
    }
}


module.exports = Loader ; 