$(document).ready(function() {
                
        
        
    $( "#stock" ).keyup(function() {
        var str = $(this).val();
        str = str.replace(/[^a-zA-Z .-]+/g, '');
        $("#stock").val(str);

    });
        
    $( "li" ).each(function() {
        $( this ).addClass( "foo" );
    });

    
});