$(document).ready(function () {
    $('#typeVehicule').change(function () {
        let ajaxType = function (value) {
            $.ajax({
                method: 'post',
                type: 'post',
                url: $('#colType').attr('data-route'),
                data: {
                    type: value
                },
                success: function (data) {
                    console.log(data);
                    $('#brandVehicule').find('option').remove();
                    $('#brandVehicule').append('<option selected disabled>Choisir</option>');

                    data.brand.map(function (result) {
                        $('#brandVehicule').append('<option value="'+result['brand']+'">'+result['brand']+'</option>');
                    });

                    $('#modelVehicule').find('option').remove();
                    $('#modelVehicule').append('<option selected disabled>Choisir</option>');

                    data.model.map(function (result) {
                        $('#modelVehicule').append('<option value="'+result['model']+'">'+result['model']+'</option>');
                    });
                },
                error: function (data) {
                    console.log(data);
                }
            });
        };

        ajaxType(this.value)
    });

    $('#brandVehicule').on('change', function() {
        let ajaxBrand = function (value) {
            $.ajax({
                method: 'post',
                type: 'post',
                url: $('#colBrand').attr('data-route'),
                data: {
                    brand: value
                },
                success: function (data) {
                    console.log(data);

                    $('#modelVehicule').find('option').remove();
                    $('#modelVehicule').append('<option selected disabled>Choisir</option>');

                    data.model.map(function (result) {
                        $('#modelVehicule').append('<option value="'+result['model']+'">'+result['model']+'</option>');
                    });
                },
                error: function (data) {
                    console.log(data);
                }
            });
        };
        ajaxBrand(this.value)
    });

    // $('#modelVehicule').on('change', function() {
    //
    // });
    $('#js-event-search-submit').on('click',function () {
        let type = $('#typeVehicule').val();
        let brand = $('#brandVehicule').val();
        let model = $('#modelVehicule').val();

        let ajaxSearch = function (type,brand,model) {
            $.ajax({
                method: 'post',
                type: 'post',
                url: $('#submitVehicule').attr('data-route'),
                data: {
                    type: type,
                    brand: brand,
                    model: model
                },
                success: function (data) {
                    console.log(data);

                    // $('#modelVehicule').find('option').remove();
                    // $('#modelVehicule').append('<option selected disabled>Choisir</option>');
                    //
                    $('#ajaxSearchCard').find('.card').remove();
                     data.vehicules.map(function (result) {
                         $('#ajaxSearchCard').append('<div class="card mb-4" style="width: 18rem;">' +
                             '<img class="card-img-top" src="'+result['picture']+'" alt="Card image cap">'+
                             '<div class="card-body">'+
                             '<h5 class="card-title font-weight-bold">'+result['vehicule']['brand']+' - '+''+result['vehicule']['model']+'</h5>'+
                             '<p class="card-text">'+result['vehicule']['description']+'</p>'+
                             '<p class="font-weight-bold">'+result['vehicule']['km']+' km</p>'+
                             '<a href="'+result['path']+'" class="button">Voir plus</a>'+
                             '</div>'+
                             '</div>');
                         console.log(result);
                     });
                },
                error: function (data) {
                    console.log(data);
                }
            });
        };
        ajaxSearch(type,brand,model)
    })
});
