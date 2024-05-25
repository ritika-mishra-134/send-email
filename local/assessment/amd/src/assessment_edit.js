define(['jquery', 'core/ajax', 'core/str', 'sweetalert2', 'core/url', 'select2-js'],
function($, ajax, str, Swal, moodleurl) {
    var index = {
        dom: {
            main: null
        },
        variable: {
            pattern: /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/,
        },
        langs: {
            nameRequired: null,
            descrptionRequired: null,
            assessmentIdRequired: null,
            assessmentPriceRequired: null,
            assessmentPartneRequired: null,
            updateSuccessfull: null,
            choose: null,
            vendorPriceRequired: null,
            validUrl: null,
            validLogo: null
        },
        action: {
            getString: function(id, paymentid) {
                str.get_strings([
                    {key: 'namerequired', component: 'local_assessment'},     
                    {key: 'descrptionrequired', component: 'local_assessment'},
                    {key: 'assessmentidrequired', component: 'local_assessment'},
                    {key: 'assessmentpricerequired', component: 'local_assessment'},
                    {key: 'assessmentpartnerequired', component: 'local_assessment'},
                    {key: 'updatesuccessfull', component: 'local_assessment'},
                    {key: 'choose', component: 'local_assessment'},
                    {key: 'vendorpricerequired', component: 'local_assessment'},
                    {key: 'validurl', component: 'local_assessment'},
                    {key: 'validlogo', component: 'local_assessment'},
                ]).then(function(s) {
                    index.langs.nameRequired = s[0];
                    index.langs.descrptionRequired = s[1];
                    index.langs.assessmentIdRequired = s[2];
                    index.langs.assessmentPriceRequired = s[3];
                    index.langs.assessmentPartneRequired = s[4];
                    index.langs.updateSuccessfull = s[5];
                    index.langs.choose = s[6];
                    index.langs.vendorPriceRequired = s[7];
                    index.langs.validUrl = s[8];
                    index.langs.validLogo = s[9];
                    index.init(id, paymentid);
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
                } else if (index.dom.assessmentId.val() == '') {
                    index.dom.assessmentIdRequired.html(index.langs.assessmentIdRequired).css({color: 'red'});
                    return false;
                } else if (index.dom.logo.files.length == 1 && (index.dom.logo.files[0].type !== 'image/png') && (index.dom.logo.files[0].type !== 'image/jpeg')) {
                    index.dom.logoRequired.html(index.langs.validLogo).css({color: 'red'});
                    return false;
                } else if (index.dom.link.val() !== '' && !index.variable.pattern.test(index.dom.link.val().trim())) {
                    index.dom.linkRequired.html(index.langs.validUrl).css({color: 'red'});
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
            ssoValues: function(id) {
                var promises = ajax.call([{
                    methodname: 'local_assessment_ssovalues',
                    args: {
                        id: id,    
                    }
                }]);
                promises[0].done(function(result) {
                    if (result.samlssoid !== null) {
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
            editAssessment: function(id, paymentid) {
                var promises = ajax.call([{
                    methodname: 'local_assessment_assessmentedit',
                    args: { 
                        id: id,
                        name: index.dom.name.val(), 
                        description: index.dom.description.val().trim(),
                        sso: index.dom.sso.val() === 0 ? 0 : index.dom.sso.val(),
                        assessmentpartner: index.dom.assessmentPartner.val(), 
                        assessmentid: index.dom.assessmentId.val(),  
                        link: index.dom.link.val(),  
                        merchantprice: index.dom.merchantPrice.val(),  
                        paymentid: paymentid,   
                        amountpaid: index.dom.assessmentPaid.val(),
                        vendorprice: index.dom.vendorPrice.val()
                    }
                }]);
                promises[0].done(function(result) {
                    if (result.status == true) {
                        if (index.dom.logo.files.length == 1) {
                            index.dom.mainForm.submit();
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title:  index.langs.updateSuccessfull,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.href = moodleurl.relativeUrl('local/assessment/assessment_list.php');
                            });
                        }   
                    }
                }).fail(function() {});
            }
        },
        init: function(id, paymentid) {
            index.dom.main = $(document).find('#assessment_form');
            index.dom.mainForm = $(document).find('#assesmentcreationForm');
            index.dom.nameRequired = index.dom.main.find('#nameRequired');
            index.dom.descriptionRequired = index.dom.main.find('#descriptionRequired');
            index.dom.assessmentPartneRequired = index.dom.main.find('#assessmentpartnerequired');
            index.dom.assessmentIdRequired = index.dom.main.find('#assessmentidrequired');
            index.dom.merchantPriceRequired = index.dom.main.find('#merchantpricerequired');
            index.dom.vendorRequired = index.dom.main.find('#vendorpricerequired');
            index.dom.linkRequired = index.dom.main.find('#linkrequired');
            index.dom.logoRequired = index.dom.main.find('#logoRequired');    
            index.dom.name = index.dom.main.find('#name');
            index.dom.description = index.dom.main.find('#assessmentdescription');
            index.dom.assessmentPartner = index.dom.main.find('#assessmntparner');
            index.dom.assessmentId = index.dom.main.find('#assessment_id');
            index.dom.sso = index.dom.main.find('#sso');
            index.dom.link = index.dom.main.find('#link');
            index.dom.merchantPrice = index.dom.main.find('#merchantprice');
            index.dom.assessmentPaid = index.dom.main.find('#assessmentpaid');    
            index.dom.logo = index.dom.mainForm.find('#assessmentlogo')[0];
            index.dom.vendorPrice = index.dom.main.find('#vendorprice');
            index.dom.assessmentPartner.select2({
                placeholder: index.langs.choose,
                allowClear: true
            });
            index.dom.sso.select2({
                placeholder: index.langs.choose,
                allowClear: true
            });
            index.dom.assessmentPaid.select2({
                placeholder: index.langs.choose,
                allowClear: true
            });      
            index.dom.main.on('click', '#editbutton', function(e) {
                e.preventDefault();
                if (index.action.validation() == true) {
                    index.action.editAssessment(id, paymentid); 
                }     
            });
            index.dom.assessmentPaid.on("change", function() {
                if (index.dom.assessmentPaid.val() == "1") {
                    index.dom.merchantPrice.prop("disabled", false);        
                } else {
                    index.dom.merchantPrice.val('');
                    index.dom.vendorPrice.val('');
                    index.dom.merchantPrice.prop("disabled", true);
                    index.dom.vendorPrice.prop("disabled", true);
                }
            }); 
            index.dom.assessmentPartner.on('change', function() {
                index.dom.sso.empty();
                index.dom.assessmentPaid.val('');
                index.dom.assessmentPaid.prepend($('<option>', {
                    value: '', // Set the value to an empty string or any other appropriate value
                    text: index.langs.choose,
                    selected: 'selected'
                }));
                index.dom.merchantPrice.val('');
                index.dom.vendorPrice.val('');
                index.action.ssoValues($(this).val());   
                index.action.paidStatus();
            });
        }
    };
    return {
        init: index.action.getString
    };
});