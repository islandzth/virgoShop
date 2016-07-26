Wili.Framework.ValidateCreateGift = function () {
    //
    //  jQuery Validate example script
    //
    //  Prepared by David Cochran
    //
    //  Free for your use -- No warranties, no guarantees!
    //
    $(document).ready(function () {

        // Validate
        // http://bassistance.de/jquery-plugins/jquery-plugin-validation/
        // http://docs.jquery.com/Plugins/Validation/
        // http://docs.jquery.com/Plugins/Validation/validate#toptions
        $('#create-gift-form').validate({
            rules: {
                inputSponsor: {
                    minlength: 4,
                    required: true
                },
                inputEmail: {
                    required: true,
                    email: true
                },
                inputAddress: {
                    required: true,
                    minlength: 5
                },
                inputWebsite: {
                    required: true,
                    url: true
                },
                inputRepresentative: {
                    required: true,
                    minlength: 3
                },
                inputPhone: {
                    required: true,
                    minlength: 8,
                    number: true
                },
                inputRepresentativeEmail: {
                    required: true,
                    email: true
                },
                inputCompanyDescription: {
                    required: true,
                    minlength: 10
                },
                inputDealName: {
                    required: true,
                    minlength: 5
                },
                selectCategory: {
                    required: true
                },
                fileDealImages: {
                    required: true
                },
                selectGender: {
                    required: true
                },
                inputPrice: {
                    required: true,
                    minlength: 4,
                    number: true
                },
                selectProvince: {
                    required: true
                },
                datepicker: {
                    required: true
                },
                datepicker2: {
                    required: true
                },
                numberQuality: {
                    required: true,
                    number: true
                },
                inputDescription: {
                    required: true,
                    minlength: 10
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').removeClass('success').addClass('error');
            },
            success: function (element) {
                element.text('OK!').addClass('valid').closest('.form-group').removeClass('error').addClass('success');
            }
        });

    }); // end document.ready  
};