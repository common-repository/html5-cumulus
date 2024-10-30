jQuery(document).ready(function($) {
    $('.tcode').textillate({
        loop: true,
        minDisplayTime: 5000,
        initialDelay: 800,
        autoStart: true,
        inEffects: [],
        outEffects: [],
        in: {
            effect: 'rollIn',
            delayScale: 1.5,
            delay: 50,
            sync: false,
            shuffle: true,
            reverse: false,
            callback: function() {}
        },
        out: {
            effect: 'fadeOut',
            delayScale: 1.5,
            delay: 50,
            sync: false,
            shuffle: true,
            reverse: false,
            callback: function() {}
        },
        callback: function() {}
    });
})

jQuery(document).ready(function($) {

    $('.switch-field').on('change', 'input[type="radio"].toggle', function () {
        if (this.checked) {
            $('input[name="' + this.name + '"].checked').removeClass('checked');
            $(this).addClass('checked');
            $('.switch-field').addClass('force-update').removeClass('force-update');
        }
    });
    $('.switch-field input[type="radio"].toggle:checked').addClass('checked');

    $("[name='wheelZoom']").click(function(){
        $("[name='wheelZoom']").removeAttr('checked');
        $(this).attr({'checked':true}).prop({'checked':true});
    });

    $("[name='dragControl']").click(function(){
        $("[name='dragControl']").removeAttr('checked');
        $(this).attr({'checked':true}).prop({'checked':true});
    });

    $("[name='freezeActive']").click(function(){
        $("[name='freezeActive']").removeAttr('checked');
        $(this).attr({'checked':true}).prop({'checked':true});
    });

    $("[name='outlineMethod']").click(function(){
        $("[name='outlineMethod']").removeAttr('checked');
        $(this).attr({'checked':true}).prop({'checked':true});
    });

})

jQuery(document).ready(function($) {
    $('.color-picker').wpColorPicker();
})

jQuery(document).ready(function($) {

    if (jQuery('#outlineMethod_left').is(':checked') || jQuery('#outlineMethod_center').is(':checked')) {
        jQuery('.outlinetr').fadeIn();
    } else {
        jQuery('.outlinetr').hide();
    }

    $('input[type=radio][name=outlineMethod]').change(function() {
        if (this.value == 'outline') {
            jQuery('.outlinetr').fadeIn();
        }
        else if (this.value == 'block') {
            jQuery('.outlinetr').fadeIn();
        } else {
            jQuery('.outlinetr').hide();
        }
    });

})


jQuery(document).ready(function($){

    checkExpTime();

    $('#close-donat').on('click',function(e) {
        localStorage.setItem('h5c-close-donat', 'yes');
        $('#donat').slideUp(300);
        $('#restore-hide-blocks').show(300);
        setExpTime();
    });

    $('#close-about').on('click',function(e) {
        localStorage.setItem('h5c-close-about', 'yes');
        $('#about').slideUp(300);
        $('#restore-hide-blocks').show(300);
        setExpTime();
    });

    $('#restore-hide-blocks').on('click',function(e) {
        localStorage.removeItem('h5c-time');
        localStorage.removeItem('h5c-close-donat');
        localStorage.removeItem('h5c-close-about');
        $('#restore-hide-blocks').hide(300);
        $('#donat').slideDown(300);
        $('#about').slideDown(300);
    });

    function setExpTime() {
        var limit = 90 * 24 * 60 * 60 * 1000; // 3 месяца
        var time = localStorage.getItem('h5c-time');
        if (time === null) {
            localStorage.setItem('h5c-time', +new Date());
        } else if(+new Date() - time > limit) {
            localStorage.removeItem('h5c-time');
            localStorage.removeItem('h5c-close-donat');
            localStorage.removeItem('h5c-close-about');
            localStorage.setItem('h5c-time', +new Date());
        }
    }

    function checkExpTime() {
        var limit = 90 * 24 * 60 * 60 * 1000; // 3 месяца
        var time = localStorage.getItem('h5c-time');
        if (time === null) {

        } else if(+new Date() - time > limit) {
            localStorage.removeItem('h5c-time');
            localStorage.removeItem('h5c-close-donat');
            localStorage.removeItem('h5c-close-about');
        }
    }

});