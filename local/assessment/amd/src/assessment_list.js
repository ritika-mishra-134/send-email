define(['jquery', 'core/ajax', 'core/str', 'sweetalert2', 'core/url',
'datatables.net',
'datatables.net-bs4',
'datatables.net-buttons',
'datatables.net-buttons-bs4',
'datatables.net-buttons-html',
'datatables.net-buttons-flash',
'datatables.net-responsive',
'datatables.net-responsive-bs4'],
function($, ajax, str, Swal, moodleurl) {
    var index = {
        dom: {
            main: null
        },
        variable: {
            dataTableReference: null,
        },
        url: {
            edit: function(id) {
                return moodleurl.relativeUrl('local/assessment/assessment_edit.php', {id: id});
            },
            // delete: function(id) {
            //     index.action.deleteAssessment(id);
            // }
        },
        langs: {
            edit: null,
        },
        action: {
            getString: function() {
                str.get_strings([
                    {key: 'edit', component: 'local_assessment'},
                ]).then(function(s) {
                    index.langs.edit = s[0];
                    index.init();
                }).fail(function(e) {});
            },
            // deleteAssessment: function(id) {
            //     var promises = ajax.call([{
            //         methodname: 'local_assessment_assessment_delete',
            //         args: {
            //             id: id,                        
            //         }
            //     }]);
            //     promises[0].done(function(result) {
            //         if (result.status == true) {
            //             Swal.fire({
            //                 position: 'center',
            //                 icon: 'success',
            //                 title: index.langs.deletedSuccessfully,
            //                 showConfirmButton: false,
            //                 timer: 1500
            //             }).then(function() {
            //                 index.variable.dataTableReference.ajax.reload();
            //             });
            //         }
            //         }).fail(function() {});       
            // }
        },
        init: function() {
            index.dom.table = $(document).find('#assessment_list');   
            index.variable.dataTableReference = index.dom.table.DataTable({
                pagingType: "simple_numbers",
                processing: true,
                serverSide: true,
                ordering: false,
                searching: true,
                lengthMenu: [10, 25, 50, 100],
                ajax: function(data, callBack) {
                    var promises = ajax.call([{
                        methodname: 'local_assessment_assessmentlist',
                        args: data
                    }]);
                    promises[0].done(callBack).fail(function(){});
                },
                columns: [{
                    data: 'name',
                }, {
                    data: 'description',
                }, {
                    data: 'assessmentpartner',
                }, {
                    data: 'sso',
                }, {
                    data: 'amount',
                }, {
                    data: 'logo_availability',
                }, { data: 'id',
                    render: function(data) {
                        return '<button class="btn btn-primary edit" id="'+data+'" value="'+data+'">' +
                                    index.langs.edit +
                                '</button>';
                                // +
                                // '<button class="btn btn-danger delete" id="'+data+'" value="'+data+'">' +
                                //     index.langs.delete +
                                // '</button>';
                }
            }],
        });
        // index.dom.table.on('click', '.delete', function(e) {
        //     index.url.delete($(this).val().trim());
        // });
        index.dom.table.on('click', '.edit', function(e) {
            window.location.href = index.url.edit($(this).val());
        });
    }
};
return {
    init: index.action.getString
};
});