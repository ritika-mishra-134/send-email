define(['jquery', 'core/ajax', 'core/str',  "core/templates"],
function($, ajax, str, templates) {
    var index = {
        dom: {
            main: null
        },
        langs: {
            noBlogs: null
        },
        action: {
            getString: function() {
                str.get_strings([
                    {key: 'noblogs', component: 'local_blogs'},    
                ]).then(function(s) {
                    index.langs.noBlogs = s[0];
                    index.init();
                }).fail(function() {});
            },
            blogDetails: function(blogtype, page) {
                var promises = ajax.call([
                    {
                        methodname: 'local_blogs_blogdetails',
                        args: {
                            blogtype : blogtype,
                            currentpage: page
                        }
                    }
                ]);
                promises[0].done(function(result) {
                    var data = result;
                    if (data.one.length > 0) {
                        templates.render("local_blogs/blogcard", {data: data}).then(function (html, js) {
                            index.dom.all.html(html);
                        }).fail(function (b) {
                        });
                    } else {
                        index.dom.all.html('<div class="col-md-12 label" style="font-size: large; font-size: large;color: #DF9CFA;margin-top: 30px;padding-top: 10px;">'+index.langs.noBlogs+'</div>');
                    }
                    
                }).fail(function(result) {
                });
            }
        },
        init: function() {
            index.action.blogDetails('all', 1);
            
            index.dom.main = $(document).find('#article');
            index.dom.all = index.dom.main.find('#all');
            
            index.dom.main.on('click', '.btn-blog', function(e) {
                var dataBlog = $(this).attr('data-blog');
                index.action.blogDetails(dataBlog, 1);
                
            });
            
            index.dom.main.on('click', 'li.page-item.next .page-link', function(e) {
                var activeItem = $("li.page-item.active");
                // Get the current page number from the active item
                var currentPage = parseInt(activeItem.val());
                // Calculate the next page number
                var nextPage = currentPage + 1;
                // Find the next page item
                var nextItem = $("li.page-item").filter(function() {
                    return $(this).val() == nextPage;
                });
                
                // If next item exists, update classes
                if (nextItem.length > 0) {
                    activeItem.removeClass('active');
                    nextItem.addClass('active');
                }
                index.action.blogDetails($('.btn-blog.active').attr('data-blog'), nextPage);
            });
            
            index.dom.main.on('click', 'li.page-item.previous .page-link', function(e) {
                var activeItem = $("li.page-item.active");
                // Get the current page number from the active item
                var currentPage = parseInt(activeItem.val());
                // Calculate the next page number
                var nextPage = currentPage - 1;
                // Find the next page item
                var nextItem = $("li.page-item").filter(function() {
                    return $(this).val() == nextPage;
                });
                
                // If next item exists, update classes
                if (nextItem.length > 0) {
                    activeItem.removeClass('active');
                    nextItem.addClass('active');
                }
                index.action.blogDetails($('.btn-blog.active').attr('data-blog'), nextPage);
            });
        }
    };
    return {
        init: index.action.getString
    };
});