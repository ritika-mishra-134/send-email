define(['jquery', 'core/ajax', 'core/str', 'select2-js'],
function($, ajax, str) {
    var index = {
        dom: {
            main: null
        },
        variable: {
            pattern: /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/,
        },
        langs: {
            nameRequired: null,
            logoRequired: null,
            descrptionRequired: null,
            assessmentIdRequired: null,
            assessmentPriceRequired: null,
            assessmentPartneRequired: null,
            none: null,
            choose: null,
            vendorPriceRequired: null,
            validUrl: null,
            validLogo: null
        },
        action: {
            getString: function() {
                str.get_strings([
                    {key: 'namerequired', component: 'local_assessment'},    
                    {key: 'logorequired', component: 'local_assessment'},   
                    {key: 'descrptionrequired', component: 'local_assessment'},
                    {key: 'assessmentidrequired', component: 'local_assessment'},
                    {key: 'assessmentpricerequired', component: 'local_assessment'},
                    {key: 'assessmentpartnerequired', component: 'local_assessment'},
                    {key: 'none', component: 'local_assessment'},
                    {key: 'choose', component: 'local_assessment'},
                    {key: 'vendorpricerequired', component: 'local_assessment'},
                    {key: 'validurl', component: 'local_assessment'},
                    {key: 'validlogo', component: 'local_assessment'},
                ]).then(function(s) {
                    index.langs.nameRequired = s[0];
                    index.langs.logoRequired = s[1];
                    index.langs.descrptionRequired = s[2];
                    index.langs.assessmentIdRequired = s[3];
                    index.langs.assessmentPriceRequired = s[4];
                    index.langs.assessmentPartneRequired = s[5];
                    index.langs.none = s[6];
                    index.langs.choose = s[7];
                    index.langs.vendorPriceRequired = s[8];
                    index.langs.validUrl = s[9];
                    index.langs.validLogo = s[10];
                    index.init();
                }).fail(function() {});
            },
            validation: function() {
                if (index.dom.name.val().trim() == '') {
                    index.dom.nameRequired.html(index.langs.nameRequired).css({color: 'red'});
                    return false;
                } else if (index.dom.description.val().trim() == '') {
                    index.dom.descriptionRequired.html(index.langs.descrptionRequired).css({color: 'red'});
                    return false;
                } else if (index.dom.assessmentPartner.val() === null) {
                    index.dom.assessmentPartneRequired.html(index.langs.assessmentPartneRequired).css({color: 'red'});
                    return false;
                } else if (index.dom.link.val() !== '' && !index.variable.pattern.test(index.dom.link.val().trim())) {
                    index.dom.linkRequired.html(index.langs.validUrl).css({color: 'red'});
                    return false;
                } else if (index.dom.assessmentId.val() == '') {
                    index.dom.assessmentIdRequired.html(index.langs.assessmentIdRequired).css({color: 'red'});
                    return false;
                } else if (index.dom.logo.files.length != 1) {
                    index.dom.logoRequired.html(index.langs.logoRequired).css({color: 'red'});
                    return false;
                } else if ((index.dom.logo.files[0].type !== 'image/png') && (index.dom.logo.files[0].type !== 'image/jpeg')) {
                    index.dom.logoRequired.html(index.langs.validLogo).css({color: 'red'});
                    return false;
                } else if (index.dom.assessmentPaid.val() == "1" && index.dom.merchantPrice.val() == '') {
                    index.dom.merchantPriceRequired.html(index.langs.assessmentPriceRequired).css({color: 'red'});
                    return false;
                } else if (index.dom.assessmentPaid.val() == "1" && !index.dom.vendorPrice.prop('disabled') && index.dom.vendorPrice.val() == '' ) {
                    index.dom.vendorRequired.html(index.langs.vendorPriceRequired).css({color: 'red'});
                    return false;
                } else {
                    return true;
                }
            },
            createAssessment: function() {
                var promises = ajax.call([{
                    methodname: 'local_assessment_createassessment',
                    args: {
                        name: index.dom.name.val().trim(),
                        description: index.dom.description.val().trim(),
                        assessmentpartner: index.dom.assessmentPartner.val(),
                        assessmentid: index.dom.assessmentId.val() === null ? 0 : index.dom.assessmentId.val(),
                        sso: index.dom.sso.val() === null ? 0 : index.dom.sso.val(),
                        link: index.dom.link.val(),
                        merchantprice: index.dom.merchantPrice.val(),
                        assessmentpaid: index.dom.assessmentPaid.val(),
                        vendorprice: index.dom.vendorPrice.val()
                    }
                }]);
                promises[0].done(function(result) {
                    if (result.status == true) {
                        index.dom.main.submit();
                    } else {
                        var errorDiv = $("<div class='alert alert-danger'>"+result.message+"</div>");
                        index.dom.main.prepend(errorDiv);
                    }
                }).fail(function() {});    
            },
            ssoValues: function(id) {
                var promises = ajax.call([{
                    methodname: 'local_assessment_ssovalues',
                    args: {
                        id: id,    
                    }
                }]);
                promises[0].done(function(result) {
                    if (result.samlssoid != 0 && result.samlssoid !== null ) {
                        index.dom.sso.append('<option value="'+result.samlssoid+'">'+result.name+'</option>',
                        '<option value="0">'+index.langs.none+'</option>');
                    } else {
                        index.dom.sso.append('<option value="0">'+index.langs.none+'</option>');
                    }
                }).fail(function() {});               
            },
            paidStatus: function() {
                var promises = ajax.call([{
                    methodname: 'local_assessment_paymentdetails',
                    args: {
                        id: index.dom.assessmentPartner.val(),
                    }
                }]);
                promises[0].done(function(result) {
                    if (result.paid == false) {
                        index.dom.assessmentPaid.prop("disabled", true);
                        index.dom.merchantPrice.prop("disabled", true);
                        index.dom.vendorPrice.prop("disabled", true);
                    } else {
                        index.dom.assessmentPaid.prop("disabled", false);
                        if (result.vendor == true) {
                            index.dom.vendorPrice.prop("disabled", false);
                            index.dom.merchantPrice.prop("disabled", false);
                        } else {
                            index.dom.merchantPrice.prop("disabled", true);
                            index.dom.vendorPrice.prop("disabled", true);
                        }
                    }
                }).fail(function() {});
            },
            validateDecimalPlaces: function(input, maxDecimalPlaces) {
                var inputValue = $(input).val();
                var decimalCount = (inputValue.split('.')[1] || '').length;
                if (decimalCount > maxDecimalPlaces) {
                    var truncatedValue = parseFloat(inputValue).toFixed(maxDecimalPlaces);
                    $(input).val(truncatedValue);
                }
            },
        },
        init: function() {
            index.dom.main = $(document).find('#assesmentcreationForm');
            index.dom.nameRequired = index.dom.main.find('#nameRequired');
            index.dom.descriptionRequired = index.dom.main.find('#descriptionRequired');
            index.dom.logoRequired = index.dom.main.find('#logoRequired');
            index.dom.assessmentPartneRequired = index.dom.main.find('#assessmentpartnerequired');
            index.dom.assessmentIdRequired = index.dom.main.find('#assessmentidrequired');
            index.dom.merchantPriceRequired = index.dom.main.find('#merchantpricerequired');   
            index.dom.vendorRequired = index.dom.main.find('#vendorpricerequired');
            index.dom.linkRequired = index.dom.main.find('#linkrequired');
            index.dom.name = index.dom.main.find('#name');
            index.dom.description = index.dom.main.find('#assessmentdescription');
            index.dom.assessmentPartner = index.dom.main.find('#assessmntparner');
            index.dom.assessmentId = index.dom.main.find('#assessment_id');
            index.dom.sso = index.dom.main.find('#sso');
            index.dom.link = index.dom.main.find('#link');
            index.dom.assessmentPrice = index.dom.main.find('#assessmentprice');
            index.dom.logo = index.dom.main.find('#assessmentlogo')[0];
            index.dom.assessmentPaid = index.dom.main.find('#assessmentpaid');   
            index.dom.merchantPrice = index.dom.main.find('#merchantprice');
            index.dom.vendorPrice = index.dom.main.find('#vendorprice');
            index.dom.assessmentPaid.prop("disabled", true);
            index.dom.assessmentPartner.select2({
                placeholder: index.langs.choose,
                allowClear: true
            });
            index.dom.sso.select2({
                placeholder: index.langs.choose,
                allowClear: true
            });
            index.dom.assessmentPaid.select2({
                laceholder: index.langs.choose,
                allowClear: true
            });
            index.dom.main.on('click', '#assessmentcreation', function(e) {
                e.preventDefault();
                if (index.action.validation() == true) {
                    index.action.createAssessment();
                }
            });
            index.dom.assessmentPaid.on("change", function() {
                if (index.dom.assessmentPaid.val() == "1") {
                    index.dom.merchantPrice.prop("disabled", false);
                } else {
                    index.dom.merchantPrice.prop("disabled", true);
                    index.dom.vendorPrice.prop("disabled", true);
                }
            });
            index.dom.assessmentPartner.on('change', function() {
                index.dom.sso.empty();
                index.dom.assessmentPaid.val('').trigger('change');
                index.action.ssoValues($(this).val());
                index.dom.merchantPrice.val('');
                index.dom.vendorPrice.val('');
                index.action.paidStatus();
            });  
            index.dom.merchantPrice.on('input', function() {
                index.action.validateDecimalPlaces(this, 2);
            }); 
            index.dom.vendorPrice.on('input', function() {
                index.action.validateDecimalPlaces(this, 2);
            });
        }
    };
    return {
        init: index.action.getString
    };
});
