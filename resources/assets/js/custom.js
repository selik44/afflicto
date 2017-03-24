$(document).ready(function () {
    console.log('___________________5_____________________');





    $('.prod-rew ').on('click', '.col-read-more', function (e) {
        console.log('ggg');

        var  muComit = $(this).parent().prev().find('.cont');

        console.log(muComit);
         muComit.css({'white-space': 'normal'})

    })
});

