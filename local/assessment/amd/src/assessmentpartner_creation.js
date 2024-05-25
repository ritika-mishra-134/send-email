define(['jquery', 'core/ajax', 'core/str', 'select2-js'],
function($, ajax, str) {
    var index = {
        dom: {
            main: null
        },
        variable: {
            pattern: /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
        },
        langs: {
            enterName: null,
            enterDescription: null,
            enterEmail: null,
            enterValidEmail: null,
            enterApiEndpoint: null,
            enterToken: null,
            enterLogo: null,
            choose: null,
            validLogo: null
        },
        action: {
            getString: function() {
                str.get_strings([
                    {key: 'namerequired', component: 'local_assessment'},
                    {key: 'emailrequired', component: 'local_assessment'},
                    {key: 'logorequired', component: 'local_assessment'},    
                    {key: 'descrptionrequired', component: 'local_assessment'},
                    {key: 'validemail', component: 'local_assessment'},
                    {key: 'enterapiendpoint', component: 'local_assessment'},
                    {key: 'entertoken', component: 'local_assessment'},
                    {key: 'choose', component: 'local_assessment'},
                    {key: 'validlogo', component: 'local_assessment'},
                ]).then(function(s) {
                    index.langs.enterName = s[0];
                    index.langs.enterEmail = s[1];
                    index.langs.enterLogo = s[2];
                    index.langs.enterDescription = s[3];  
                    index.langs.enterValidEmail = s[4];
                    index.langs.enterApiEndpoint = s[5];
                    index.langs.enterToken = s[6];
                    index.langs.choose = s[7];
                    index.langs.validLogo = s[8];
                    index.init();
                }).fail(function() {});
            },
            validation: function() {
                if (index.dom.name.val().trim() == '') {
                    index.dom.nameRequired.html(index.langs.enterName).css({color: 'red'});
                    return false;
                } else if (index.dom.description.val().trim() == '') {
                    index.dom.descriptionRequired.html(index.langs.enterDescription).css({color: 'red'});
                    return false;
                } else if (index.dom.logo.files.length != 1) {
                    index.dom.logoRequired.html(index.langs.enterLogo).css({color: 'red'});
                    return false;
                } else if ((index.dom.logo.files[0].type !== 'image/png') && (index.dom.logo.files[0].type !== 'image/jpeg')) {
                    index.dom.logoRequired.html(index.langs.validLogo).css({color: 'red'});
                    return false;
                } else if (index.dom.email.val().trim() == '') {
                    index.dom.emailRequired.html(index.langs.enterEmail).css({color: 'red'});
                    return false;
                } else if (!index.variable.pattern.test(index.dom.email.val().trim())) {
                    index.dom.emailRequired.html(index.langs.enterValidEmail).css({color: 'red'});
                    return false;
                } else if ((index.dom.payment.val() !== null && index.dom.apiEndPoint.val() == "") || (index.dom.payment.val() !== null && index.dom.token.val() == "")) {
                    if (index.dom.apiEndPoint.val() == "") {
                        index.dom.apiRequired.html(index.langs.enterApiEndpoint).css({color: 'red'});
                        return false;
                    } else if (index.dom.token.val().trim() == '') {
                        index.dom.tokenRequired.html(index.langs.enterToken).css({color: 'red'});
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            },   
            createAssessmentPartner: function() {
                var promises = ajax.call([{
                    methodname: 'local_assessment_assessment_creation',
                    args: {
                        name: index.dom.name.val().trim(),
                        description: index.dom.description.val().trim(),
                        sso: index.dom.sso.val() === null ? 0 : index.dom.sso.val(),
                        payment: index.dom.payment.val() === null ? 0 : index.dom.payment.val(),
                        subpayment: index.dom.subPayment.val() === null ? 0 : index.dom.subPayment.val(),     
                        email: index.dom.email.val(),
                        apiendpoint: index.dom.apiEndPoint.val() || "",
                        apitoken: index.dom.token.val() || "",
                    }
                }]);
                promises[0].done(function(result) {
                    if (result.status == true) {
                        index.dom.main.submit();
                    } else {
                        var errorDiv = $("<div class='alert alert-danger'>"+result.message+"</div>");
                        index.dom.mainForm.prepend(errorDiv);
                    }
                }).fail(function() {});
            }
        },  
        init: function() {
            index.dom.mainForm = $(document).find('#assessment_form');
            index.dom.main = $(document).find('#assesmentForm');
            index.dom.nameRequired = index.dom.main.find('#nameRequired');
            index.dom.descriptionRequired = index.dom.main.find('#descriptionRequired');
            index.dom.logoRequired = index.dom.main.find('#logoRequired');
            index.dom.emailRequired = index.dom.main.find('#emailRequired');
            index.dom.apiRequired = index.dom.main.find('#apirequired');
            index.dom.tokenRequired = index.dom.main.find('#tokenrequired');   
            index.dom.name = index.dom.main.find('#name');
            index.dom.description = index.dom.main.find('#description');
            index.dom.logo = index.dom.main.find('#logo')[0];
            index.dom.payment = index.dom.main.find('#payment');
            index.dom.subPayment = index.dom.main.find('#subpayment');
            index.dom.email = index.dom.main.find('#email');
            index.dom.apiEndPoint = index.dom.main.find('#api_endponit');
            index.dom.token = index.dom.main.find('#token');
            index.dom.sso = index.dom.main.find('#sso');    
            index.dom.subPayment.prop("disabled", true);
            index.dom.main.on('click', '#assessment_creation', function(e) {
                e.preventDefault();
                if (index.action.validation() == true) {
                    index.action.createAssessmentPartner();
                }
            });
            index.dom.sso.select2({
                placeholder: index.langs.choose,
                allowClear: true   
            });
            index.dom.payment.select2({
                placeholder: index.langs.choose,
                allowClear: true
            });
            index.dom.subPayment.select2({
                placeholder: index.langs.choose,
                allowClear: true
            });
            index.dom.payment.on('change', function() {
                if ($(this).val() === '') {
                    index.dom.subPayment.prop("disabled", true);
                    index.dom.apiEndPoint.prop("disabled", true);
                    index.dom.token.prop("disabled", true);
                } else {
                    index.dom.subPayment.prop("disabled", false);
                    index.dom.apiEndPoint.prop("disabled", false);
                    index.dom.token.prop("disabled", false);
                }
            });
        }
    };
    return {
        init: index.action.getString
    };
});