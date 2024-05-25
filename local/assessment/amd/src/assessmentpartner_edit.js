define(['jquery', 'core/ajax', 'core/str',  'sweetalert2', 'core/url', 'select2-js'],
function($, ajax, str, Swal, moodleurl) {
    var index = {
        dom: {
            main: null
        },
        variable: {
            dataTableReference: null,
            pattern: /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
        },
        langs: {
            enterName: null,
            enterDescription: null,
            enterEmail: null,
            enterValidEmail: null,
            enterApiEndpoint: null,
            enterToken: null,
            updateSuccessFull: null,
            choose: null,
            validLogo: null
        },
        action: {
            getString: function(id, userid) {
                str.get_strings([
                    {key: 'namerequired', component: 'local_assessment'},
                    {key: 'descrptionrequired', component: 'local_assessment'},
                    {key: 'emailrequired', component: 'local_assessment'},
                    {key: 'validemail', component: 'local_assessment'},
                    {key: 'enterapiendpoint', component: 'local_assessment'},
                    {key: 'entertoken', component: 'local_assessment'},
                    {key: 'updatesuccessfull', component: 'local_assessment'},
                    {key: 'choose', component: 'local_assessment'},
                    {key: 'validlogo', component: 'local_assessment'},
                ]).then(function(s) {
                    index.langs.enterName = s[0];
                    index.langs.enterDescription = s[1];
                    index.langs.enterEmail = s[2];
                    index.langs.enterValidEmail = s[3];
                    index.langs.enterApiEndpoint = s[4];
                    index.langs.enterToken = s[5];
                    index.langs.updateSuccessFull = s[6];
                    index.langs.choose = s[7];
                    index.langs.validLogo = s[8];
                    index.init(id, userid);
                }).fail(function() {});
            },
            validation: function() {
                if (index.dom.name.val().trim() == '') {
                    index.dom.nameRequired.html(index.langs.enterName).css({color: 'red'});
                    return false;
                } else if (index.dom.description.val().trim() == '') {
                    index.dom.descriptionRequired.html(index.langs.enterDescription).css({color: 'red'});
                    return false;
                } else if (index.dom.email.val().trim() == '') {
                    index.dom.emailRequired.html(index.langs.enterEmail).css({color: 'red'});
                    return false;
                } else if (index.dom.logo.files.length == 1 && (index.dom.logo.files[0].type !== 'image/png') && (index.dom.logo.files[0].type !== 'image/jpeg')) {
                    index.dom.logoRequired.html(index.langs.validLogo).css({color: 'red'});
                    return false;
                } else if (!index.variable.pattern.test(index.dom.email.val().trim())) {
                    index.dom.emailRequired.html(index.langs.enterValidEmail).css({color: 'red'});
                    return false;
                } else if ((index.dom.payment.val() != 0 && index.dom.apiEndPoint.val() == "") || (index.dom.payment.val() != 0 && index.dom.token.val() == "")) {
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
            editPartner: function(id, userid) {
                var promises = ajax.call([{
                    methodname: 'local_assessment_assessmentpartner_edit',
                    args: {   
                        id: id,
                        name: index.dom.name.val(),    
                        description: index.dom.description.val(), 
                        sso: index.dom.sso.val() == 0 ? 0 : index.dom.sso.val(), 
                        payment: index.dom.payment.val() == 0 ? 0 : index.dom.payment.val() ,  
                        subpayment: index.dom.subPayment.val() == 0 ? 0 : index.dom.subPayment.val(),  
                        email: index.dom.email.val().trim(),  
                        apiendpoint: index.dom.apiEndPoint.val(),  
                        apitoken: index.dom.token.val(),       
                        userid: userid,          
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
                                title:  index.langs.updateSuccessFull,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.href = moodleurl.relativeUrl('local/assessment/assessmentpartner_list.php');
                            });
                        }
                    }
                }).fail(function() {});    
            },
        },
        init: function(id, userid) {
            index.dom.main = $(document).find('#assessment_form');
            index.dom.mainForm = $(document).find('#assesmentForm');
            index.dom.nameRequired = index.dom.main.find('#nameRequired');
            index.dom.descriptionRequired = index.dom.main.find('#descriptionRequired');
            index.dom.emailRequired = index.dom.main.find('#emailRequired');
            index.dom.apiRequired = index.dom.main.find('#apirequired');
            index.dom.tokenRequired = index.dom.main.find('#tokenrequired');
            index.dom.logoRequired = index.dom.main.find('#logoRequired');    
            index.dom.name = index.dom.main.find('#name');
            index.dom.description = index.dom.main.find('#description');
            index.dom.email = index.dom.main.find('#email');
            index.dom.apiEndPoint = index.dom.main.find('#api_endponit');
            index.dom.token = index.dom.main.find('#token');
            index.dom.sso = index.dom.main.find('#sso');
            index.dom.payment = index.dom.main.find('#payment');
            index.dom.subPayment = index.dom.main.find('#subpayment');
            index.dom.logo = index.dom.main.find('#logo')[0];

            if (index.dom.payment.val() == 0) {
                index.dom.subPayment.val('').trigger('change');
                index.dom.apiEndPoint.val('');
                index.dom.token.val('');
                index.dom.apiEndPoint.prop("disabled", true);
                index.dom.token.prop("disabled", true);
                index.dom.subPayment.prop("disabled", true);
            } else {
                index.dom.apiEndPoint.prop("disabled", false);
                index.dom.token.prop("disabled", false);
                index.dom.subPayment.prop("disabled", false);
            }
            index.dom.main.on('click', '#editbutton', function(e) {
                e.preventDefault();
                if (index.action.validation() == true) {
                    index.action.editPartner(id, userid);
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
                if (index.dom.payment.val() == 0) {
                    index.dom.subPayment.val('').trigger('change');
                    index.dom.apiEndPoint.val('');
                    index.dom.token.val('');
                    index.dom.apiEndPoint.prop("disabled", true);
                    index.dom.token.prop("disabled", true);
                    index.dom.subPayment.prop("disabled", true);
                } else {
                    index.dom.apiEndPoint.prop("disabled", false);
                    index.dom.token.prop("disabled", false);
                    index.dom.subPayment.prop("disabled", false);
                }
            });
        }
    };
    return {
        init: index.action.getString
    };
});