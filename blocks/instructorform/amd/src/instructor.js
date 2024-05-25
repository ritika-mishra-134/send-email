define(['jquery', 'core/ajax', 'core/str', 'sweetalert2'
],
function($, ajax, str, Swal) {

    var index = {
        dom: {
            main: null
        },
        variable: {
            imageExtensions: ['image/jpeg', 'image/png', 'image/jpg'],
            documentExtensions: ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'],
            checkEmailPattern: /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,

        },
        langs: {
            enterfirstName: null,
            enterlastName: null,
            enterEmail: null,
            enterValidEmail: null,
            enterMobileNumber: null,
            enterValidMobileNumber: null,
            enterExpertise: null,
            uploadResume: null,
            validFile: null,
            validFormat: null,
            validLength: null
            
        },
        action: {
            getString: function() {
                str.get_strings([
                    {key: 'firstname', component: 'block_instructorform'},
                    {key: 'lastname', component: 'block_instructorform'},
                    {key: 'enteremail', component: 'block_instructorform'},
                    {key: 'entervalidemail', component: 'block_instructorform'},
                    {key: 'entermobilenumber', component: 'block_instructorform'},
                    {key: 'entervalidmobilenumber', component: 'block_instructorform'},
                    {key: 'enterexpertise', component: 'block_instructorform'},
                    {key: 'uploadresume', component: 'block_instructorform'},
                    {key: 'validfile', component: 'block_instructorform'},
                    {key: 'validfromat', component: 'block_instructorform'},
                    {key: 'length', component: 'block_instructorform'}
                ]).then(function(s) {
                    index.langs.enterfirstName = s[0];
                    index.langs.enterlastName = s[1];
                    index.langs.enterEmail = s[2];
                    index.langs.enterValidEmail = s[3];
                    index.langs.enterMobileNumber = s[4];
                    index.langs.enterValidMobileNumber = s[5];
                    index.langs.enterExpertise = s[6];
                    index.langs.uploadResume = s[7];
                    index.langs.validFile = s[8];
                    index.langs.validFormat = s[9];
                    index.langs.validLength = s[10];
                    index.init();
                }).fail(function(e) {});
            },
            industryDetails: function() {
                var promises = ajax.call([{
                    methodname: 'block_instructorform_register_instructor',
                    args: {
                        firstname: index.dom.firstName.val().trim(),
                        lastname: index.dom.lastName.val().trim(),
                        email: index.dom.email.val().trim(),
                        phone: index.dom.phone.val().trim(),
                        expertise: index.dom.expertise.val().trim(),
                    }
                }]);
                promises[0].done(function(result) {
                    if (result.status == true) {
                        if(index.dom.instructorForm.submit()) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Thankyou for contacting us. We will connect with you shortly ',
                                showConfirmButton: false,
                                timer: 1500
                              })
                        }
                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: result.message,
                            showConfirmButton: false,
                            timer: 1500
                          })
                   }
                }).fail(function() {});
            },
            
            specialCharactersNotAllowed: function(e) {
                var charCode = e.which || e.keyCode;
                if ((charCode >= 65 && charCode <= 90) ||
                    (charCode >= 97 && charCode <= 122) ||
                    (charCode >= 48 && charCode <= 57) || charCode === 32) {
                    return true;
                } else {
                    e.preventDefault();
                }
            },
            onlyDigitAllow: function(e) {
                var k = e.which;
                var allow = k >= 48 && k <= 57; // 0-9

                if (!allow) {
                    e.preventDefault();
                }
            }
        },

        
        init: function() {
            index.dom.main = $(document).find('#form1');
            index.dom.instructorForm = index.dom.main.find('#instructor-regform');
            index.dom.requiredFname = index.dom.main.find('#fname-required');
            index.dom.requiredLname = index.dom.main.find('#lname-required');
            index.dom.requiredEmail = index.dom.main.find('#email-required');
            index.dom.phoneRequired = index.dom.main.find('#phone-required');
            index.dom.expRequired = index.dom.main.find('#expertise-required');
            index.dom.resumeRequired = index.dom.main.find('#resume-required');
            

            index.dom.firstName = index.dom.main.find('#firstname');
            index.dom.lastName = index.dom.main.find('#lastname');
            index.dom.email = index.dom.main.find('#email');
            index.dom.phone = index.dom.main.find('#mobile');
            index.dom.expertise = index.dom.main.find('#expertise');
            index.dom.resume = index.dom.main.find('#resume')[0];
              
            // Form Validation Part .
            index.dom.main.on('click', '#btn-save-changes', function(e) {

                if (index.dom.firstName.val().trim() == '') {
                    index.dom.requiredFname.html(index.langs.enterfirstName).css({color: 'red'});
                    return false;
                } else if (index.dom.firstName.val().trim().length > 100) {
                    index.dom.requiredFname.html(index.langs.validLength).css({color: 'red'});
                    return false;
                } else if (index.dom.lastName.val().trim() == '') {
                    index.dom.requiredLname.html(index.langs.enterlastName).css({color: 'red'});
                    return false;
                } else if (index.dom.lastName.val().trim().length > 100) {
                    index.dom.requiredLname.html(index.langs.validLength).css({color: 'red'});
                    return false;
                } else if (index.dom.email.val().trim() == '') {
                    index.dom.requiredEmail.html(index.langs.enterEmail).css({color: 'red'});
                    return false;
                } else if (!index.variable.checkEmailPattern.test(index.dom.email.val().trim())) {
                    index.dom.requiredEmail.html(index.langs.enterValidEmail).css({color: 'red'});
                    return false;
                } else if (index.dom.phone.val().trim() == '') {
                    index.dom.phoneRequired.html(index.langs.enterMobileNumber).css({color: 'red'});
                    return false;
                } else if (index.dom.phone.val().length < 10) {
                    index.dom.phoneRequired.html(index.langs.enterValidMobileNumber).css({color: 'red'});
                    return false;
                }
                 else if (index.dom.expertise.val().trim() == '') {
                    index.dom.expRequired.html(index.langs.enterExpertise).css({color: 'red'});
                    return false;
                } else if (index.dom.resume.files.length != 1) {
                    index.dom.resumeRequired.html(index.langs.uploadResume).css({color: 'red'});
                    return false;
                } else if (index.dom.resume.files[0].size < 5120) { // Check if file size is less than 5MB
                    index.dom.resumeRequired.html(index.langs.validFile).css({color: 'red'}); // Display error message
                    return false;
                } else if (!index.variable.documentExtensions.includes(index.dom.resume.files[0].type)) {
                        index.dom.resumeRequired.html(index.langs.validFormat).css({color: 'red'});
                        return false;
                } 
                else {
                    index.action.industryDetails();
                }
            });
            index.dom.main.on('keypress', '#mobile', function(e) {
                index.action.onlyDigitAllow(e);
            });
            index.dom.main.on('keypress', '#firstname, #lastname, #expertise', function(e) {
                index.action.specialCharactersNotAllowed(e);
            });
            index.dom.main.on('input', '#mobile', function(e) {
                // Get the input element
                var input = e.target;
            
                // Get the current input value
                var inputValue = input.value;
            
                // Check if the input length is greater than 10
                if (inputValue.length > 10) {
                    // If the input length is greater than 10, trim the input to 10 characters
                    input.value = inputValue.slice(0, 10);
                }
            });

        }
    };
    return {
        init: index.action.getString
    };
});