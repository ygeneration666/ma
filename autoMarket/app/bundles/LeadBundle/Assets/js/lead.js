//LeadBundle
Mautic.leadOnLoad = function (container) {
    Mousetrap.bind('a', function(e) {
        if(mQuery('#lead-quick-add').length) {
            mQuery('#lead-quick-add').modal();
        } else if (mQuery('#addNoteButton').length) {
            mQuery('#addNoteButton').click();
        }
    });

    Mousetrap.bind('t', function(e) {
        mQuery('#table-view').click();
    });

    Mousetrap.bind('c', function(e) {
        mQuery('#card-view').click();
    });

    Mousetrap.bind('n', function(e) {
        mQuery('#new-lead').click();
    });

    Mousetrap.bind('mod+enter', function(e) {
        if(mQuery('#leadnote_buttons_save').length) {
            mQuery('#leadnote_buttons_save').click();
        } else if (mQuery('#save-quick-add').length) {
            mQuery('#save-quick-add').click();
        }
    });

    //Prevent single combo keys from initiating within lead note
    Mousetrap.stopCallback = function(e, element, combo) {
        if (element.id == 'leadnote_text' && combo != 'mod+enter') {
            return true;
        }

        // if the element has the class "mousetrap" then no need to stop
        if ((' ' + element.className + ' ').indexOf(' mousetrap ') > -1) {
            return false;
        }

        // stop for input, select, and textarea
        return element.tagName == 'INPUT' || element.tagName == 'SELECT' || element.tagName == 'TEXTAREA' || (element.contentEditable && element.contentEditable == 'true');
    };

    if (mQuery(container + ' form[name="lead"]').length) {
        mQuery("*[data-toggle='field-lookup']").each(function (index) {
            var target = mQuery(this).attr('data-target');
            var field  = mQuery(this).attr('id');
            var options = mQuery(this).attr('data-options');
            Mautic.activateLeadFieldTypeahead(field, target, options);
        });

        Mautic.updateLeadFieldProperties(mQuery('#leadfield_type').val());
    }

    // Timeline filters
    var timelineForm = mQuery(container + ' #timeline-filters');
    if (timelineForm.length) {
        timelineForm.on('change', function() {
            timelineForm.submit();
        }).on('keyup', function() {
            timelineForm.delay(200).submit();
        }).on('submit', function(e) {
            e.preventDefault();
            Mautic.refreshLeadTimeline(timelineForm);
        });
    }

    //Note type filters
    var noteForm = mQuery(container + ' #note-filters');
    if (noteForm.length) {
        noteForm.on('change', function() {
            noteForm.submit();
        }).on('keyup', function() {
            noteForm.delay(200).submit();
        }).on('submit', function(e) {
            e.preventDefault();
            Mautic.refreshLeadNotes(noteForm);
        });
    }

    if (mQuery(container + ' #list-search').length) {
        Mautic.activateSearchAutocomplete('list-search', 'lead.lead');
    }

    if (mQuery(container + ' #notes-container').length) {
        Mautic.activateSearchAutocomplete('NoteFilter', 'lead.note');
    }

    if (typeof Mautic.leadEngagementChart === 'undefined') {
        Mautic.renderEngagementChart();
    }

    if (mQuery('#lead_preferred_profile_image').length) {
        mQuery('#lead_preferred_profile_image').on('change', function() {
            if (mQuery(this).val() == 'custom') {
                mQuery('#customAvatarContainer').slideDown('fast');
            } else {
                mQuery('#customAvatarContainer').slideUp('fast');
            }
        })
    }

    if (mQuery('.lead-avatar-panel').length) {
        mQuery('.lead-avatar-panel .avatar-collapser a.arrow').on('click', function() {
            setTimeout(function() {
                var status = (mQuery('#lead-avatar-block').hasClass('in') ? 'expanded' : 'collapsed');
                Cookies.set('mautic_lead_avatar_panel', status, {expires: 30});
            }, 500);
        });
    }

    if (mQuery('#anonymousLeadButton').length) {
        var searchValue = mQuery('#list-search').typeahead('val').toLowerCase();
        var string      = mQuery('#anonymousLeadButton').data('anonymous').toLowerCase();

        if (searchValue.indexOf(string) >= 0 && searchValue.indexOf('!' + string) == -1) {
            mQuery('#anonymousLeadButton').addClass('btn-primary');
        } else {
            mQuery('#anonymousLeadButton').removeClass('btn-primary');
        }
    }
};

Mautic.leadOnUnload = function(id) {
    if (id === '#app-content') {
        delete Mautic.leadEngagementChart;
    }

    if (typeof MauticVars.moderatedIntervals['leadListLiveUpdate'] != 'undefined') {
        Mautic.clearModeratedInterval('leadListLiveUpdate');
    }
};

Mautic.getLeadId = function() {
    return mQuery('input#leadId').val();
}

Mautic.activateLeadFieldTypeahead = function(field, target, options) {
    if (options) {
        var keys = [], values = [];
        //check to see if there is a key/value split
        if (typeof options == 'string') {
            options = options.split('||');
            if (options.length == 2) {
                keys = options[1].split('|');
                values = options[0].split('|');
            } else {
                values = options[0].split('|');
            }
        } else {
            values = options;
        }

        var fieldTypeahead = Mautic.activateTypeahead('#' + field, {
            dataOptions: values,
            dataOptionKeys: keys,
            minLength: 0
        });
    } else {
        var fieldTypeahead = Mautic.activateTypeahead('#' + field, {
            prefetch: true,
            remote: true,
            action: "lead:fieldList&field=" + target
        });
    }

    mQuery(fieldTypeahead).on('typeahead:selected', function (event, datum) {
        if (mQuery("#" + field + "_id").length && datum["id"]) {
            mQuery("#" + field + "_id").val(datum["id"]);
        }
    }).on('typeahead:autocompleted', function (event, datum) {
        if (mQuery("#" + field + "_id").length && datum["id"]) {
            mQuery("#" + field + "_id").val(datum["id"]);
        }
    });
};

Mautic.leadlistOnLoad = function(container) {
    if (mQuery(container + ' #list-search').length) {
        Mautic.activateSearchAutocomplete('list-search', 'lead.list');
    }

    if (mQuery('#leadlist_filters').length) {
        mQuery('#available_filters').on('change', function() {
            if (mQuery(this).val()) {
                Mautic.addLeadListFilter(mQuery(this).val());
                mQuery(this).val('');
                mQuery(this).trigger('chosen:updated');
            }
        });

        mQuery('#leadlist_filters .remove-selected').each( function (index, el) {
            mQuery(el).on('click', function () {
                mQuery(this).closest('.panel').animate(
                    {'opacity': 0},
                    'fast',
                    function () {
                        mQuery(this).remove();
                    }
                );

                if (!mQuery('#leadlist_filters li:not(.placeholder)').length) {
                    mQuery('#leadlist_filters li.placeholder').removeClass('hide');
                } else {
                    mQuery('#leadlist_filters li.placeholder').addClass('hide');
                }
            });
        });
    }

    mQuery("*[data-toggle='field-lookup']").each(function (index) {
        var target = mQuery(this).attr('data-target');
        var options = mQuery(this).attr('data-options');
        var field  = mQuery(this).attr('id');
        Mautic.activateLeadFieldTypeahead(field, target, options);
    });
};

Mautic.convertLeadFilterInput = function(el) {
    var operator = mQuery(el).val();
    // Extract the filter number
    var regExp    = /leadlist_filters_(\d+)_operator/;
    var matches   = regExp.exec(mQuery(el).attr('id'));
    var filterNum = matches[1];
    var filterId  = '#leadlist_filters_' + filterNum + '_filter';

    // Reset has-error
    if (mQuery(filterId).parent().hasClass('has-error')) {
        mQuery(filterId).parent().find('div.help-block').hide();
        mQuery(filterId).parent().removeClass('has-error');
    }

    var disabled = (operator == 'empty' || operator == '!empty') ? true : false;
    mQuery(filterId).prop('disabled', disabled);

    if (disabled) {
        mQuery(filterId).val('');
    }

    if (mQuery(filterId).is('select')) {
        var isMultiple  = mQuery(filterId).attr('multiple');
        var multiple    = (operator == 'in' || operator == '!in') ? true : false;
        var placeholder = mQuery(filterId).attr('data-placeholder');

        if (multiple && !isMultiple) {
            mQuery(filterId).attr('multiple', 'multiple');

            // Update the name
            var newName =  mQuery(filterId).attr('name') + '[]';
            mQuery(filterId).attr('name', newName);

            placeholder = mauticLang['chosenChooseMore'];
        } else if (!multiple && isMultiple) {
            mQuery(filterId).removeAttr('multiple');

            // Update the name
            var newName =  mQuery(filterId).attr('name').replace(/[\[\]']+/g,'')
            mQuery(filterId).attr('name', newName);

            placeholder = mauticLang['chosenChooseOne'];
        }

        if (multiple) {
            // Remove empty option
            mQuery(filterId).find('option[value=""]').remove();

            // Make sure none are selected
            mQuery(filterId + ' option:selected').removeAttr('selected');
        } else {
            // Add empty option
            mQuery(filterId).prepend("<option value='' selected></option>");
        }

        // Destroy the chosen and recreate
        if (mQuery(filterId + '_chosen').length) {
            mQuery(filterId).chosen('destroy');
        }

        mQuery(filterId).attr('data-placeholder', placeholder);

        Mautic.activateChosenSelect(mQuery(filterId));
    }
};

Mautic.addLeadListFilter = function (elId) {
    var filterId = '#available_' + elId;
    var label    = mQuery(filterId).text();

    //create a new filter

    var filterNum = parseInt(mQuery('.available-filters').data('index'));
    mQuery('.available-filters').data('index', filterNum + 1);

    var prototype = mQuery('.available-filters').data('prototype');
    var fieldType = mQuery(filterId).data('field-type');
    var isSpecial = (mQuery.inArray(fieldType, ['leadlist', 'tags', 'boolean', 'select', 'country', 'timezone', 'region']) != -1);

    prototype = prototype.replace(/__name__/g, filterNum);
    prototype = prototype.replace(/__label__/g, label);

    // Convert to DOM
    prototype = mQuery(prototype);

    var filterBase  = "leadlist[filters][" + filterNum + "]";
    var filterIdBase = "leadlist_filters_" + filterNum + "_";

    if (isSpecial) {
        var templateField = fieldType;
        if (fieldType == 'boolean') {
            templateField = 'select';
        }
        var template = mQuery('#templates .' + templateField + '-template').clone();
        mQuery(template).attr('name', mQuery(template).attr('name').replace(/__name__/g, filterNum));
        mQuery(template).attr('id', mQuery(template).attr('id').replace(/__name__/g, filterNum));
        mQuery(prototype).find('input[name="' + filterBase + '[filter]"]').replaceWith(template);
    }

    if (mQuery('#leadlist_filters div.panel').length == 0) {
        // First filter so hide the glue footer
        mQuery(prototype).find(".panel-footer").addClass('hide');
    }

    mQuery(prototype).find("a.remove-selected").on('click', function() {
        mQuery(this).closest('.panel').animate(
            {'opacity': 0},
            'fast',
            function () {
                mQuery(this).remove();
            }
        );
    });

    mQuery(prototype).find("input[name='" + filterBase + "[field]']").val(elId);
    mQuery(prototype).find("input[name='" + filterBase + "[type]']").val(fieldType);

    var filterEl = (isSpecial) ? "select[name='" + filterBase + "[filter]']" : "input[name='" + filterBase + "[filter]']";

    mQuery(prototype).appendTo('#leadlist_filters');

    var filter = '#' + filterIdBase + 'filter';

    //activate fields
    if (isSpecial) {
        if (fieldType == 'select' || fieldType == 'boolean') {
            // Generate the options
            var fieldOptions = mQuery(filterId).data("field-list");

            mQuery.each(fieldOptions, function(index, val) {
                mQuery('<option>').val(index).text(val).appendTo(filterEl);
            });
        }
    } else if (fieldType == 'lookup') {
        var fieldCallback = mQuery(filterId).data("field-callback");
        if (fieldCallback && typeof Mautic[fieldCallback] == 'function') {
            var fieldOptions = mQuery(filterId).data("field-list");
            Mautic[fieldCallback](filterIdBase + 'filter', elId, fieldOptions);
        }
    } else if (fieldType == 'datetime') {
        mQuery(filter).datetimepicker({
            format: 'Y-m-d H:i',
            lazyInit: true,
            validateOnBlur: false,
            allowBlank: true,
            scrollInput: false
        });
    } else if (fieldType == 'date') {
        mQuery(filter).datetimepicker({
            timepicker: false,
            format: 'Y-m-d',
            lazyInit: true,
            validateOnBlur: false,
            allowBlank: true,
            scrollInput: false,
            closeOnDateSelect: true
        });
    } else if (fieldType == 'time') {
        mQuery(filter).datetimepicker({
            datepicker: false,
            format: 'H:i',
            lazyInit: true,
            validateOnBlur: false,
            allowBlank: true,
            scrollInput: false
        });
    } else if (fieldType == 'lookup_id') {
        //switch the filter and display elements
        var oldFilter = mQuery(filterEl);
        var newDisplay = mQuery(oldFilter).clone();
        mQuery(newDisplay).attr('name', filterBase + '[display]');

        var oldDisplay = mQuery(prototype).find("input[name='" + filterBase + "[display]']");
        var newFilter = mQuery(oldDisplay).clone();
        mQuery(newFilter).attr('name', filterBase + '[filter]');

        mQuery(oldFilter).replaceWith(newFilter);
        mQuery(oldDisplay).replaceWith(newDisplay);

        var fieldCallback = mQuery(filterId).data("field-callback");
        if (fieldCallback && typeof Mautic[fieldCallback] == 'function') {
            var fieldOptions = mQuery(filterId).data("field-list");
            Mautic[fieldCallback](filterIdBase + 'filter', elId, fieldOptions);
        }
    } else {
        mQuery(filter).attr('type', fieldType);
    }

    // Remove inapplicable operator types
    var operators = mQuery(filterId).data('field-operators');

    if (typeof operators.include != 'undefined') {
        mQuery('#' + filterIdBase + 'operator option').filter(function () {
            return mQuery.inArray(mQuery(this).val(), operators['include']) == -1
        }).remove();
    } else if (typeof operators.exclude != 'undefined') {
        mQuery('#' + filterIdBase + 'operator option').filter(function () {
            return mQuery.inArray(mQuery(this).val(), operators['exclude']) > 0
        }).remove();
    }

    // Convert based on first option in list
    Mautic.convertLeadFilterInput('#' + filterIdBase + 'operator');
};

Mautic.leadfieldOnLoad = function (container) {

    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            mQuery(this).width(mQuery(this).width());
        });
        return ui;
    };

    if (mQuery(container + ' .leadfield-list').length) {
        mQuery(container + ' .leadfield-list tbody').sortable({
            handle: '.fa-ellipsis-v',
            helper: fixHelper,
            scroll: false,
            axis: 'y',
            containment: container + ' .leadfield-list',
            stop: function(i) {
                // Get the page and limit
                mQuery.ajax({
                    type: "POST",
                    url: mauticAjaxUrl + "?action=lead:reorder&limit=" + mQuery('.pagination-limit').val() + '&page=' + mQuery('.pagination li.active a span').first().text(),
                    data: mQuery(container + ' .leadfield-list tbody').sortable("serialize")});
            }
        });
    }

};

Mautic.updateLeadFieldProperties = function(selectedVal) {
    if (selectedVal == 'lookup') {
        // Use select
        selectedVal = 'select';
    }

    if (mQuery('#field-templates .'+selectedVal).length) {
        mQuery('#leadfield_properties').html('');
        mQuery('#leadfield_properties').append(mQuery('#field-templates .'+selectedVal).clone(true));
    } else {
        mQuery('#leadfield_properties').html('');
    }

    if (selectedVal == 'time') {
        mQuery('#leadfield_isListable').closest('.row').addClass('hide');
    } else {
        mQuery('#leadfield_isListable').closest('.row').removeClass('hide');
    }

    // Switch default field if applicable
    var defaultFieldType = mQuery('input[name="leadfield[defaultValue]"]').attr('type');

    if (selectedVal == 'boolean') {
        if (defaultFieldType == 'text') {
            // Convert to a select
            var newDiv      = mQuery('<div id="leadfield_defaultValue"></div>');
            var defaultBool = mQuery('#field-templates .default_bool').html();
            defaultBool     = defaultBool.replace(/default_bool_template/g, 'defaultValue');

            mQuery(defaultBool).appendTo(newDiv);

            mQuery('#leadfield_defaultValue').replaceWith(newDiv);
        }
    } else if (defaultFieldType == 'radio') {
        // Convert to input
        var html = mQuery('#field-templates .default').html();
        html     = html.replace(/default_template/g, 'defaultValue');
        mQuery('#leadfield_defaultValue').replaceWith(html);
    }
};

Mautic.updateLeadFieldBooleanLabels = function(el, label) {
    mQuery('#leadfield_defaultValue_' + label).parent().find('span').text(
        mQuery(el).val()
    );
};

Mautic.refreshLeadSocialProfile = function(network, leadId, event) {
    Mautic.startIconSpinOnEvent(event);
    var query = "action=lead:updateSocialProfile&network=" + network + "&lead=" + leadId;
    mQuery.ajax({
        showLoadingBar: true,
        url: mauticAjaxUrl,
        type: "POST",
        data: query,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                if (response.completeProfile) {
                    mQuery('#social-container').html(response.completeProfile);
                    mQuery('#SocialCount').html(response.socialCount);
                } else {
                    //loop through each network
                    mQuery.each(response.profiles, function (index, value) {
                        if (mQuery('#' + index + 'CompleteProfile').length) {
                            mQuery('#' + index + 'CompleteProfile').html(value.newContent);
                        }
                    });
                }
            }
            Mautic.stopPageLoadingBar();
            Mautic.stopIconSpinPostEvent();
        },
        error: function (request, textStatus, errorThrown) {
            Mautic.processAjaxError(request, textStatus, errorThrown);
        }
    });
};

Mautic.clearLeadSocialProfile = function(network, leadId, event) {
    Mautic.startIconSpinOnEvent(event);
    var query = "action=lead:clearSocialProfile&network=" + network + "&lead=" + leadId;
    mQuery.ajax({
        url: mauticAjaxUrl,
        type: "POST",
        data: query,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                //activate the click to remove the panel
                mQuery('.' + network + '-panelremove').click();
                if (response.completeProfile) {
                    mQuery('#social-container').html(response.completeProfile);
                }
                mQuery('#SocialCount').html(response.socialCount);
            }

            Mautic.stopIconSpinPostEvent();
        },
        error: function (request, textStatus, errorThrown) {
            Mautic.processAjaxError(request, textStatus, errorThrown);
            Mautic.stopIconSpinPostEvent();
        }
    });
};

Mautic.refreshLeadTimeline = function(form) {
    var formData = form.serialize()
    mQuery.ajax({
        showLoadingBar: true,
        url: mauticAjaxUrl,
        type: "POST",
        data: "action=lead:updateTimeline&" + formData,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                Mautic.stopPageLoadingBar();
                mQuery('#timeline-container').html(response.timeline);
                mQuery('#HistoryCount').html(response.historyCount);
            }
        },
        error: function (request, textStatus, errorThrown) {
            Mautic.processAjaxError(request, textStatus, errorThrown);
        }
    });
};

Mautic.refreshLeadNotes = function(form) {
    Mautic.postForm(mQuery(form), function (response) {
        response.target = '#NoteList';
        mQuery('#NoteCount').html(response.noteCount);
        Mautic.processPageContent(response);
    });
};

Mautic.toggleLeadList = function(toggleId, leadId, listId) {
    var action = mQuery('#' + toggleId).hasClass('fa-toggle-on') ? 'remove' : 'add';
    var query = "action=lead:toggleLeadList&leadId=" + leadId + "&listId=" + listId + "&listAction=" + action;

    Mautic.toggleLeadSwitch(toggleId, query, action);
};

Mautic.toggleLeadCampaign = function(toggleId, leadId, campaignId) {
    var action = mQuery('#' + toggleId).hasClass('fa-toggle-on') ? 'remove' : 'add';
    var query  = "action=lead:toggleLeadCampaign&leadId=" + leadId + "&campaignId=" + campaignId + "&campaignAction=" + action;

    Mautic.toggleLeadSwitch(toggleId, query, action);
};

Mautic.toggleLeadSwitch = function(toggleId, query, action) {
    var toggleOn  = 'fa-toggle-on text-success';
    var toggleOff = 'fa-toggle-off text-danger';
    var spinClass = 'fa-spin fa-spinner ';

    if (action == 'remove') {
        //switch it on
        mQuery('#' + toggleId).removeClass(toggleOn).addClass(spinClass + 'text-danger');
    } else {
        mQuery('#' + toggleId).removeClass(toggleOff).addClass(spinClass + 'text-success');
    }

    mQuery.ajax({
        url: mauticAjaxUrl,
        type: "POST",
        data: query,
        dataType: "json",
        success: function (response) {
            mQuery('#' + toggleId).removeClass(spinClass);
            if (!response.success) {
                //return the icon back
                if (action == 'remove') {
                    //switch it on
                    mQuery('#' + toggleId).removeClass(toggleOff).addClass(toggleOn);
                } else {
                    mQuery('#' + toggleId).removeClass(toggleOn).addClass(toggleOff);
                }
            } else {
                if (action == 'remove') {
                    //switch it on
                    mQuery('#' + toggleId).removeClass(toggleOn).addClass(toggleOff);
                } else {
                    mQuery('#' + toggleId).removeClass(toggleOff).addClass(toggleOn);
                }
            }
        },
        error: function (request, textStatus, errorThrown) {
            //return the icon back
            mQuery('#' + toggleId).removeClass(spinClass);

            if (action == 'remove') {
                //switch it on
                mQuery('#' + toggleId).removeClass(toggleOff).addClass(toggleOn);
            } else {
                mQuery('#' + toggleId).removeClass(toggleOn).addClass(toggleOff);
            }
        }
    });
};

Mautic.leadNoteOnLoad = function (container, response) {
    if (response.noteHtml) {
        var el = '#LeadNote' + response.noteId;
        if (mQuery(el).length) {
            mQuery(el).replaceWith(response.noteHtml);
        } else {
            mQuery('#LeadNotes').prepend(response.noteHtml);
        }

        //initialize ajax'd modals
        mQuery(el + " *[data-toggle='ajaxmodal']").off('click.ajaxmodal');
        mQuery(el + " *[data-toggle='ajaxmodal']").on('click.ajaxmodal', function (event) {
            event.preventDefault();

            Mautic.ajaxifyModal(this, event);
        });

        //initiate links
        mQuery(el + " a[data-toggle='ajax']").off('click.ajax');
        mQuery(el + " a[data-toggle='ajax']").on('click.ajax', function (event) {
            event.preventDefault();

            return Mautic.ajaxifyLink(this, event);
        });
    } else if (response.deleteId && mQuery('#LeadNote' + response.deleteId).length) {
        mQuery('#LeadNote' + response.deleteId).remove();
    }

    if (response.upNoteCount || response.noteCount || response.downNoteCount) {
        if (response.upNoteCount || response.downNoteCount) {
            var count = parseInt(mQuery('#NoteCount').html());
            count = (response.upNoteCount) ? count + 1 : count - 1;
        } else {
            var count = parseInt(response.noteCount);
        }

        mQuery('#NoteCount').html(count);
    }
};

Mautic.renderEngagementChart = function() {
    if (!mQuery("#chart-engagement").length) {
        return;
    }
    var canvas = document.getElementById("chart-engagement");
    var chartData = mQuery.parseJSON(mQuery('#chart-engagement-data').text());
    Mautic.leadEngagementChart = new Chart(canvas.getContext("2d")).Line(chartData);

    var legendHolder = document.createElement('div');
    legendHolder.innerHTML = Mautic.leadEngagementChart.generateLegend();
    mQuery('#engagement-legend').html(legendHolder.firstChild);
    Mautic.leadEngagementChart.update();
};

Mautic.showSocialMediaImageModal = function(imgSrc) {
    mQuery('#socialImageModal img').attr('src', imgSrc);
    mQuery('#socialImageModal').modal('show');
};

Mautic.leadImportOnLoad = function (container, response) {
    if (!mQuery('#leadImportProgress').length) {
        Mautic.clearModeratedInterval('leadImportProgress');
    } else {
        Mautic.setModeratedInterval('leadImportProgress', 'reloadLeadImportProgress', 3000);
    }
};

Mautic.reloadLeadImportProgress = function() {
    if (!mQuery('#leadImportProgress').length) {
        Mautic.clearModeratedInterval('leadImportProgress');
    } else {
        // Get progress separate so there's no delay while the import batches
        Mautic.ajaxActionRequest('lead:getImportProgress', {}, function(response) {
            if (response.progress) {
                if (response.progress[0] > 0) {
                    mQuery('.imported-count').html(response.progress[0]);
                    mQuery('.progress-bar-import').attr('aria-valuenow', response.progress[0]).css('width', response.percent + '%');
                    mQuery('.progress-bar-import span.sr-only').html(response.percent + '%');
                }
            }
        });

        // Initiate import
        mQuery.ajax({
            showLoadingBar: false,
            url: window.location + '?importbatch=1',
            success: function(response) {
                Mautic.moderatedIntervalCallbackIsComplete('leadImportProgress');

                if (response.newContent) {
                    // It's done so pass to process page
                    Mautic.processPageContent(response);
                }
            }
        });
    }
};

Mautic.removeBounceStatus = function (el, dncId) {
    mQuery(el).removeClass('fa-times').addClass('fa-spinner fa-spin');

    Mautic.ajaxActionRequest('lead:removeBounceStatus', 'id=' + dncId, function() {
        mQuery('#bounceLabel' + dncId).tooltip('destroy');
        mQuery('#bounceLabel' + dncId).fadeOut(300, function() { mQuery(this).remove(); });
    });
};

Mautic.toggleLiveLeadListUpdate = function () {
    if (typeof MauticVars.moderatedIntervals['leadListLiveUpdate'] == 'undefined') {
        Mautic.setModeratedInterval('leadListLiveUpdate', 'updateLeadList', 5000);
        mQuery('#liveModeButton').addClass('btn-primary');
    } else {
        Mautic.clearModeratedInterval('leadListLiveUpdate');
        mQuery('#liveModeButton').removeClass('btn-primary');
    }
};

Mautic.updateLeadList = function () {
    var maxLeadId = mQuery('#liveModeButton').data('max-id');
    mQuery.ajax({
        url: mauticAjaxUrl,
        type: "get",
        data: "action=lead:getNewLeads&maxId=" + maxLeadId,
        dataType: "json",
        success: function (response) {
            if (response.leads) {
                if (response.indexMode == 'list') {
                    mQuery('#leadTable tbody').prepend(response.leads);
                } else {
                    var items = mQuery(response.leads);
                    mQuery('.shuffle-grid').prepend(items);
                    mQuery('.shuffle-grid').shuffle('appended', items);
                    mQuery('.shuffle-grid').shuffle('update');

                    mQuery('#liveModeButton').data('max-id', response.maxId);
                }
            }

            if (typeof IdleTimer != 'undefined' && !IdleTimer.isIdle()) {
                // Remove highlighted classes
                if (response.indexMode == 'list') {
                    mQuery('#leadTable tr.warning').each(function() {
                        var that = this;
                        setTimeout(function() {
                            mQuery(that).removeClass('warning', 1000)
                        }, 5000);
                    });
                } else {
                    mQuery('.shuffle-grid .highlight').each(function() {
                        var that = this;
                        setTimeout(function() {
                            mQuery(that).removeClass('highlight', 1000, function() {
                                mQuery(that).css('border-top-color', mQuery(that).data('color'));
                            })
                        }, 5000);
                    });
                }
            }

            if (response.maxId) {
                mQuery('#liveModeButton').data('max-id', response.maxId);
            }

            Mautic.moderatedIntervalCallbackIsComplete('leadListLiveUpdate');
        },
        error: function (request, textStatus, errorThrown) {
            Mautic.processAjaxError(request, textStatus, errorThrown);

            Mautic.moderatedIntervalCallbackIsComplete('leadListLiveUpdate');
        }
    });
};

Mautic.toggleAnonymousLeads = function() {
    var searchValue = mQuery('#list-search').typeahead('val');
    var string      = mQuery('#anonymousLeadButton').data('anonymous').toLowerCase();

    if (searchValue.toLowerCase().indexOf('!' + string) == 0) {
        searchValue = searchValue.replace('!' + string, string);
        mQuery('#anonymousLeadButton').addClass('btn-primary');
    } else if (searchValue.toLowerCase().indexOf(string) == -1) {
        if (searchValue) {
            searchValue = searchValue + ' ' + string;
        } else {
            searchValue = string;
        }
        mQuery('#anonymousLeadButton').addClass('btn-primary');
    } else {
        searchValue = mQuery.trim(searchValue.replace(string, ''));
        mQuery('#anonymousLeadButton').removeClass('btn-primary');
    }
    searchValue = searchValue.replace("  ", " ");
    Mautic.setSearchFilter(null, 'list-search', searchValue);
};

Mautic.getLeadEmailContent = function (el) {
    Mautic.activateLabelLoadingIndicator('lead_quickemail_templates');
    mQuery('#MauticSharedModal .btn-primary').prop('disabled', true);
    Mautic.ajaxActionRequest('lead:getEmailTemplate', {'template': mQuery(el).val()}, function(response) {
        mQuery('#MauticSharedModal .btn-primary').prop('disabled', false);
        CKEDITOR.instances['lead_quickemail_body'].setData(response.body);
        mQuery('#lead_quickemail_subject').val(response.subject);
        Mautic.removeLabelLoadingIndicator();
    });
};

Mautic.updateLeadTags = function () {
    Mautic.activateLabelLoadingIndicator('lead_tags_tags');
    var formData = mQuery('form[name="lead_tags"]').serialize();
    Mautic.ajaxActionRequest('lead:updateLeadTags', formData, function(response) {
        if (response.tags) {
            mQuery('#lead_tags_tags').html(response.tags);
            mQuery('#lead_tags_tags').trigger('chosen:updated');
        }
        Mautic.removeLabelLoadingIndicator();
    });
};

Mautic.createLeadTag = function(el) {
    var newFound = false;
    mQuery('#' + mQuery(el).attr('id') + ' :selected').each(function(i, selected) {
        if (!mQuery.isNumeric(mQuery(selected).val())) {
            newFound = true;
        }
    });

    if (!newFound) {
        return;
    }

    Mautic.activateLabelLoadingIndicator(mQuery(el).attr('id'));

    var tags = JSON.stringify(mQuery(el).val());

    Mautic.ajaxActionRequest('lead:addLeadTags', {tags: tags}, function(response) {
        if (response.tags) {
            mQuery('#' + mQuery(el).attr('id')).html(response.tags);
            mQuery('#' + mQuery(el).attr('id')).trigger('chosen:updated');
        }

        Mautic.removeLabelLoadingIndicator();
    });
};

Mautic.leadBatchSubmit = function() {
    if (Mautic.batchActionPrecheck()) {
        if (mQuery('#lead_batch_remove').val() || mQuery('#lead_batch_add').val() || mQuery('#lead_batch_dnc_reason').length) {
            var ids = Mautic.getCheckedListIds(false, true);

            if (mQuery('#lead_batch_ids').length) {
                mQuery('#lead_batch_ids').val(ids);
            } else if (mQuery('#lead_batch_dnc_reason').length) {
                mQuery('#lead_batch_dnc_ids').val(ids);
            }

            return true;
        }
    }

    mQuery('#MauticSharedModal').modal('hide');

    return false;
};

Mautic.updateLeadFieldValues = function (field) {
    Mautic.activateLabelLoadingIndicator('campaignevent_properties_field');

    field = mQuery(field);
    var fieldAlias = field.val();
    Mautic.ajaxActionRequest('lead:updateLeadFieldValues', {'alias': fieldAlias}, function(response) {
        if (typeof response.options != 'undefined') {
            var valueField = mQuery('#campaignevent_properties_value');
            var valueFieldAttrs = {
                'class': valueField.attr('class'),
                'id': valueField.attr('id'),
                'name': valueField.attr('name'),
                'autocomplete': valueField.attr('autocomplete'),
                'value': valueField.attr('value')
            };

            if (!mQuery.isEmptyObject(response.options)) {
                var newValueField = mQuery('<select/>')
                    .attr('class', valueFieldAttrs['class'])
                    .attr('id', valueFieldAttrs['id'])
                    .attr('name', valueFieldAttrs['name'])
                    .attr('autocomplete', valueFieldAttrs['autocomplete'])
                    .attr('value', valueFieldAttrs['value']);
                mQuery.each(response.options, function(optionKey, optionVal) {
                    var option = mQuery("<option/>")
                        .attr('value', optionKey)
                        .text(optionVal);
                    newValueField.append(option);
                });
                valueField.replaceWith(newValueField);
            } else {
                var newValueField = mQuery('<input/>')
                    .attr('type', 'text')
                    .attr('class', valueFieldAttrs['class'])
                    .attr('id', valueFieldAttrs['id'])
                    .attr('name', valueFieldAttrs['name'])
                    .attr('autocomplete', valueFieldAttrs['autocomplete'])
                    .attr('value', valueFieldAttrs['value']);
                valueField.replaceWith(newValueField);
            }
        }
        Mautic.removeLabelLoadingIndicator();
    });
};

Mautic.toggleTimelineMoreVisiblity = function(el) {
    if (mQuery(el).is(':visible')) {
        mQuery(el).slideUp('fast');
        mQuery(el).next().text(mauticLang['showMore']);
    } else {
        mQuery(el).slideDown('fast');
        mQuery(el).next().text(mauticLang['hideMore']);
    }
};
