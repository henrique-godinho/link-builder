const urlParams = new URLSearchParams(window.location.search);
const matchId = urlParams.get('id');
const matchName = urlParams.get('match');
const league = urlParams.get('league');

let match = document.createElement('header');
match.innerText = matchName;
document.body.appendChild(match);

let betslip = document.createElement('div');
betslip.className = 'betslip';
betslip.id = 'betslip';
betslip.innerText = 'Selected Odds';
document.body.appendChild(betslip);

let contract_ids = [];

fetch(`matchDetails.php?id=${matchId}&league=${league}`)
    .then(response => {
        if(!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        return response.json()
    })
    .then(data => {

        let accordion = document.createElement('div');
            accordion.className = 'accordion';
            accordion.id = 'accordion';

        for (const key in data) {
            const element = data[key];
            const contracts = element['contracts'];
            
            let header = document.createElement('h3');
            header.className = 'accordion-label';
            header.innerText = element['marketName'];


            let accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';
 
    
            // Iterate through contracts array
            contracts.forEach(contract => {
                let name = document.createElement('p');
                name.innerText = contract['name'];
    
                let id = contract['id'];
    
                let price = document.createElement('button');
                price.innerHTML = contract['fractional_price'];
                price.className = 'odds';
                price.id = id;
    
                // Append name, id, and price to div
                
                accordionItem.appendChild(name);
                name.appendChild(price);
            });
            
            accordion.appendChild(header);
            accordion.appendChild(accordionItem);
            // Append div to body
            document.body.appendChild(accordion);
            
        }

        $(document).ready(function () {
            $('#accordion').accordion({
                collapsible: true,
                heightStyle: "content",
                icons: {"header": "ui-icon-triangle-1-e", "activeHeader": "ui-icon-triangle-1-s"}
            });

            $('#generateLinkForm').submit(function() {
                event.preventDefault();
                let formData = $('#generateLinkForm').serializeArray();
            
                $.ajax({
                    type: 'POST',
                    url: 'link_builder.php',
                    data: {contract_ids: contract_ids, formData: formData, match_id: matchId},
                    success: function (response) {
                        $('#dialog').dialog('close');
                        

                        
                        $('<div></div>').html(response)
                        .dialog({
                        title: 'Generated URL',
                        modal: true,
                        width: 900,
                        buttons: {
                        Ok: function() {
                        $(this).dialog('close');
                        }
                    }
                });

                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });
        });
    })
    .catch(error => {
        console.error(error);
    });


$(document).on('click', '.odds', function() {
    let selected_market_id = $(this).attr('id');
    let selected_market = $('#betslip').find('.selected_market[id="' + selected_market_id + '"]');
    let continueButton = $('<button></button>').text('Continue').addClass('continue-button');


    if (selected_market.length === 0) {
        // Create a new selected_market div
        selected_market = $('<div></div>').addClass('selected_market').attr('id', selected_market_id);

        // Add market name
        let marketName = $('.ui-accordion-header-active').text();
        selected_market.append(marketName);

        // Clone optionName
        let optionName = $(this).parent().clone();
        selected_market.append(optionName);

        // Add delete button
        let deleteButton = $('<button></button>').text('Delete').addClass('deleteButton');
        selected_market.append(deleteButton);

        // Append selected_market to #betslip
        $('#betslip').append(selected_market);

    }

    if ($('#betslip').children().length <= 1) {
            $('#betslip').append(continueButton);

            $('.continue-button').click(function() {

                $('.selected_market').each(function() {

                    let marketId = $(this).attr('id');
                    if (contract_ids.indexOf(marketId) === -1) {
                        
                        contract_ids.push($(this).attr('id'));
                    }
                });

                $('#dialog').dialog('open');

            });
            
    } 
});

$(document).on('click', '.deleteButton', function() {
    // Remove the parent .selected_market when the delete button is clicked
    let deletedContractId = $(this).parent().attr('id');
    $(this).parent().remove();

    let index = contract_ids.indexOf(deletedContractId);
    if(index !== -1) {
        contract_ids.splice(index, 1);
    }

    if ($('#betslip').children().length <= 1 ) {
        $('.continue-button').remove();
    }

});


$(document).ready(function () {
    $('#dialog').dialog({
        modal: true,
        autoOpen: false,
        resizable: false
    });
});



