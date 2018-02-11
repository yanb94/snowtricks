$(document).ready(function () {
    
    $count = [];

    $('.my-add-input').click(function (e) {
        

        e.preventDefault();
        var prototype = $($(this).attr('data-list'));
        var listString = $(this).attr('data-inputs');
        var list = $(listString);

        list.children().length;

        if($count[listString] == undefined)
        {
            $count[listString] = list.children('fieldset').children('div').children().length
        }

        var newWidget = prototype.attr('data-prototype').toString();        
        
        newWidget = newWidget.replace(/__name__/g, $count[listString]);

        var newElem = $('<fieldset class="form-group"><div></div></fieldset>').html(newWidget);

        list.children('fieldset').children('div').append(newElem);

        $count[listString]++;

    });

    $('.my-delete-button').click(function (e) {

        e.preventDefault();
        var prototype = $($(this).attr('data-list'));
        var listString = $(this).attr('data-inputs');
        var list = $(listString);

        list.children().length;

        if($count[listString] == undefined)
        {
            $count[listString] = list.children('fieldset').children('div').children().length
        }

        if($count[listString] > 0)
        {
            list.children('fieldset').children('div').children().last().remove();
            $count[listString]--;
        }

    });

});