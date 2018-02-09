// This file is the previous one that come sfrom the previous website, i do not use htis
// Send client entered Data to the server script using Ajax -> Check if establishment is Available and ask for some information
// Receiv Data returned by the server and handle them -> Display harvested information and Google Map
// var dataToSend = {"product_id":"62","product_quantity":"65"};
$(document).ready(function(){

    moreCriteria();

    $('#signaler-btn').click(function(){
        var address = $('#address-autocomplete').val();
        window.location = 'signalement.php?address=' + address;
    });
});

function moreCriteria(){
    //Click on "Plus de critères"
    var isBtnClicked = 0;
    $('#more_criteria_link').click(function(){
        isBtnClicked = !isBtnClicked;
        isBtnClicked ? $('#more_criteria_link').html('Moins de critères') : $('#more_criteria_link').html('Plus de critères');
        $('#more_criteria').toggle();
        clearFields();
    });

    $('#search-btn').click(function(){
        var form_values = new Object();
        form_values.queryType = "more_criteria";
        form_values.form = get_form_values();
        // console.log(form_values);
        if(is_valid_form(form_values)){
            sendData(form_values);
        }
    });
}

function get_form_values(){
    var tmp_form = [];
    tmp_form.push($('#more_criteria_nom').val());
    tmp_form.push($('#more_criteria_ville').val());
    tmp_form.push($('#more_criteria_activities').val());
    // console.log(tmp_form);
    return tmp_form;
}

function is_valid_form(form){
    //Traiter le formulaire ici
    return 1;
}

function clearFields(){
    $('#nom_erp').html('');
    $('#nom_erp_title').html('');
    $('#access_text').html('');
}

function displayReturnedRows(recvData){

    clearFields();

    //If row(s) is not returned, if an address was not found from db
    if((recvData._returnRow.length == 0) && (recvData._returnRow[0] == null)){
        $('#access_text').html('Cet ERP ne s\'est pas déclaré conforme vis-à-vis de l\'accessibilité.');
        return 1;
    }

    $('#nom_erp_title').html(recvData._returnRow.length + ' ERP à cette adresse:');

    var arr = [];
    var len = (recvData._returnRow).length;

    for (var i = 0; i < len; i++)
    {
        arr = recvData._returnRow[i];

        var tmpString = $('#nom_erp').html();
        $('#nom_erp').html(tmpString + '<p>L\'ERP <span class="nom_erp_inmportant">"'+
            arr.liste_ERP_nom_erp +'"</span> s\'est déclarée conforme vis-à-vis de l\'accessibilité'+
            (!arr.listeERP_date_valid_adap ? '' : ' et celle-ci sera effective à partir du <span class="nom_erp_inmportant">' + arr.listeERP_date_valid_adap) +'</span>.</p>');
    }
}

function sendData(dataToSend) {

    $.ajax({
        method: 'POST',
        url: 'isAvailable.php',
        // datatype: 'json',
        //Query of our JSON data object
        data: {
            passedData: dataToSend
        },
        success: function(returnedData) {
            //Decoding the JSON object returned by the server
            var recvData = JSON.parse(returnedData);
            // console.log(recvData);
            //Add server returned content into our view
            //Process to displaying content into different divs
            displayReturnedRows(recvData);
        },
        //If any error
        error: function() {
            clearFields();
            $("#errorMessage").html('Désolé, une erreur est survenue. Veuillez Réessayer plus tard.');
        }
    });
}

var autocomplete;
var map;

function initAutocomplete() {
    //Get address text input field
    var input = document.getElementById('address-autocomplete');

    var options = {
        //Return Only professional results
        // types: ['establishment'],
        //return Only establishments in France
        componentRestrictions: {
            country: 'fr'
        }
    };
    //New instance of autocomplete obj with passed address and setted options
    autocomplete = new google.maps.places.Autocomplete(input, options);
    autocomplete.addListener('place_changed', addrProcessing);
}


//------------------------------------------------------------------------------------------------------//
//  Coffee Function ;) : Create cleanObject, Display map of entered address, sendData to the server     //
//------------------------------------------------------------------------------------------------------//
function addrProcessing() {

    function createPlaceObj() {
        var place = autocomplete.getPlace();
        // console.log(place);

        return place;
    }

    function createCleanObject(place) {
        var cleanObject = new Object();

        // ----------------  Create Addresse Components index  ---------------- //
        function creatAddrCompIndex() {
            var tmpData = []; //Temp Array for adding data and then store it into object attribut
            for (var i = 0; i < place.address_components.length; i++) { //Loop over Address Components indexes
                tmpData.push(place.address_components[i]['long_name']); //Only Add 'long_name' index to tmp Array
            }

            return tmpData;
        }
        // ----------------  Create Geometry index  ---------------- //
        function createGeometryIndex() {
            var tmpData = [place.geometry.viewport.f['b'], place.geometry.viewport.b['b']];
            // console.log(tmpData);
            return tmpData;
        }


        //Attributing returned values from functions to the cleanObject attributs
        cleanObject.queryType = "standard"; //Simple query with given address
        cleanObject.addrComp = creatAddrCompIndex();
        cleanObject.geometry = createGeometryIndex();
        return cleanObject;
    }

    var cleanObject = createCleanObject(createPlaceObj());

    initMap(cleanObject.geometry);

    sendData(cleanObject);
}
