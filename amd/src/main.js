define([
        "jquery",
        "core/str",
        "core/modal_factory",
        "core/templates",
        'core/fragment',
        "core/ajax"
    ],
    function($, Str, ModalFactory, Template, Fragment, Ajax) {
        return {
            init: function() {
                var currentSection = 0;
                var mainModal;
                var contextId = 70;

                /**
                 * Get the minimal module form for the popup
                 */
                var getModuleForm = function(data) {
                    console.log('getModuleForm', data);
                    return Fragment.loadFragment(
                        'local_activitychooser',
                        'minimal_form',
                        contextId,
                        {
                            "name": data.name,
                            "courseid": data.courseid,
                            "section": data.section
                        }
                    )
                        .then(function(html, js) {
                            var form = $(html);
                            form.find('.collapsible').each(function(i) {
                                if (i === 0) {
                                    return;
                                }
                                $(this).hide();
                            });
                            Template.replaceNodeContents($('.modal-body'), form, js);
                            return;
                        }.bind(this))
                        .then(function() {

                            // Submit on enter.
                            $(document).on('keypress', function(e) {
                                if (e.which !== 13) {
                                    return;
                                }

                                $('#mform1').submit();
                            });

                            // Make sure the form change checker is disabled otherwise it'll
                            // stop the user from navigating away from the page once the modal
                            // is hidden.
                            Y.use('moodle-core-formchangechecker', function() {
                                M.core_formchangechecker.reset_form_dirty_state();
                            });
                            return;
                        })
                        .fail(Notification.exception);
                };

                /**
                 * Add or remove a specific moduleId from favourited items.
                 * @param moduleId
                 */
                var moduleToggleFavourited = function(moduleId) {
                    return Ajax.call([
                        {
                            methodname: "local_activitychooser_toggle_starred",
                            args: {"activityid": moduleId}
                        }
                    ])[0]
                        .then(function() {
                            return getTemplateContext(currentSection);
                        })
                        .then(function(context) {
                            return Template.render('local_activitychooser/modulechooser', context);
                        })
                        .then(function(body) {
                            mainModal.setBody(body);
                        });
                };

                var getTemplateContext = function(sectionNum) {
                    var course = document.body.className.match(/course-(\d+)/)[1];
                    return Ajax.call([
                        {
                            methodname: "local_activitychooser_get_activites",
                            args: {
                                "sectionnum": sectionNum,
                                "course": course
                            }
                        }
                    ])[0].then(function(data) {
                        var convertItemsToRows = function(items, maxCells) {
                            var rows = [];
                            var row = [];
                            items.forEach(function(f) {
                                if (row.length < maxCells) {
                                    row.push(f);
                                } else {
                                    rows.push(row);
                                    row = [f];
                                }
                            });
                            if (row.length > 0) {
                                rows.push(row);
                            }
                            return rows;
                        };

                        var allRows = convertItemsToRows(data.all, 2);
                        var recommendedRows = convertItemsToRows(data.recommended, 2);
                        var starredRows = convertItemsToRows(data.starred, 4);

                        var allExpanded = !!$('#collapsed-all').hasClass('show');

                        return {
                            allRows: allRows,
                            recommendedRows: recommendedRows,
                            starredRows: starredRows,
                            allExpandable: recommendedRows.length || starredRows.length,
                            allExpanded: allExpanded
                        };
                    });
                };

                Str.get_string("addactivity", "local_activitychooser")
                    .then(function(addStr) {
                        var buttonHtml = '' +
                            '<button class="alternative-modchooser btn link">' +
                            '<i class="icon fa fa-plus fa-fw "></i>' + addStr + '</button>';
                        $('.section-modchooser-link').parent().append(buttonHtml);

                        var title = addStr;
                        var body = '<div></div>';

                        return ModalFactory.create({
                            type: ModalFactory.types.DEFAULT,
                            title: title,
                            body: body,
                            large: true
                        });
                    })
                    .then(function(modal) {
                        mainModal = modal;
                        $('.alternative-modchooser').click(function() {
                            var sectionNum = $(this).closest("li.section").attr('id').split('-')[1];
                            modal.show();

                            currentSection = sectionNum;

                            getTemplateContext(sectionNum)
                                .then(function(context) {
                                    var modalBodyPromise = Template.render('local_activitychooser/modulechooser', context);
                                    modal.setBody(modalBodyPromise);
                                });
                        });
                    });

                // Click handler for favouriting.
                $("body").on("click", ".activitychooser-modal-body .row .toggle-favourite", function() {
                    var id = ($(this).data('id'));
                    $(this).addClass('spinning');
                    moduleToggleFavourited(id);
                });

                $("body").on("click", ".activitychooser-modal-body a.activity_creator", function(e) {
                    e.preventDefault();

                    getModuleForm({
                        courseid: $(this).data('courseid'),
                        section: $(this).data('section'),
                        name: $(this).data('name'),
                    });
                });

                $("body").on("click", ".activitychooser-modal-body .row .help-text", function() {
                    $(this).toggleClass('help-visible');
                });
            }
        };
    });