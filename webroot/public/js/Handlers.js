
$(document).ready(function(){

    // Lookup MC Number Handler
    $("#fetchMcInfoButton").click(function (){

        var searchNumber = $('#id_search_for').val();
        var searchType = "DOT";

        if ($('#lookup_mc').is(":checked") === false && $('#lookup_dot').is(":checked") === false) {
            alert("Need to pick one of them");
            return false;
        } else if ($('#lookup_mc').is(":checked")) {
            searchType = "MC";
        }

        //alert("You are searching for [" + searchType + "]: " + searchNumber);
        
        $.ajax({
            
                type: 'POST',
                dataType: 'json',
                url: '/ajax/fetchmc',
                async: false,

                // you can use an object here
                data: { type: searchType, number: searchNumber },
                success: function(json) {

                    var status = json['status'];

                    if (status === "VALID") {
                        alert("We're good!");
                        alert(json['data']['lname'] + ' ' + json['data']['us_dot']);
                    } else {
                        alert("FAIL!");
                    }


                    //alert(json['status']);
                    //alert(json['data']['lname'] + ' ' + json.data.us_dot);
                }
        });

        

        // you might need to do this, to prevent anchors from following
        // or form controls from submitting
        return false;
    });

});