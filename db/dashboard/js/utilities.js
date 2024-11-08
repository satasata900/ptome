$(document).ready(function(){

    // Don't allow spaces
    $(".no-space").on({
        keydown: function(e) {
            if (e.which === 32)
                return false;
        },
        change: function() {
            this.value = this.value.replace(/\s/g, "");
        }
    });

    //Allow only inputs
    $(".numeric-input").keypress(function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    $('.no-paste').on("paste",function(e) {
        e.preventDefault();
    });

    $("input").on("keyup",function(){
        let clearButton = $(this).parent().find(".clear-input-btn");
        if($(this).val())
            clearButton.show(200);
        else
            clearButton.hide(200);
    });

    $(".clear-input-btn").on('click',function(){
        let targetInput = $(this).parent().find('input').first();
        clearInput(targetInput);
        $(this).hide(200);
    });

    function clearInput(targetInput) {
        targetInput.val('');
    }

    function checkInputsState(){

        $.each($('input'),function(){
            let clearButton = $(this).parent().find('.clear-input-btn');
            if($(this).val()){
                clearButton.show(200);
            }
        });

    }

    checkInputsState();

});
