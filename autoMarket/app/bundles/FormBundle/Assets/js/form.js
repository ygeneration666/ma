//FormBundle
Mautic.formOnLoad = function (container) {
    if (mQuery(container + ' #list-search').length) {
        Mautic.activateSearchAutocomplete('list-search', 'form.form');
    }

    if (mQuery('#mauticforms_fields')) {
        //make the fields sortable
        mQuery('#mauticforms_fields').sortable({
            items: '.mauticform-row',
            handle: '.reorder-handle',
            stop: function(i) {
                mQuery.ajax({
                    type: "POST",
                    url: mauticAjaxUrl + "?action=form:reorderFields",
                    data: mQuery('#mauticforms_fields').sortable("serialize") + "&formId=" + mQuery('#mauticform_sessionId').val()
                })
            }
        });

        mQuery('#mauticforms_fields .mauticform-row').on('mouseover.mauticformfields', function() {
           mQuery(this).find('.form-buttons').removeClass('hide');
        }).on('mouseout.mauticformfields', function() {
            mQuery(this).find('.form-buttons').addClass('hide');
        }).on('dblclick.mauticformfields', function(event) {
            event.preventDefault();
            mQuery(this).find('.btn-edit').first().click();
        });
    }

    if (mQuery('#mauticforms_actions')) {
        //make the fields sortable
        mQuery('#mauticforms_actions').sortable({
            items: '.mauticform-row',
            handle: '.reorder-handle',
            stop: function(i) {
                mQuery.ajax({
                    type: "POST",
                    url: mauticAjaxUrl + "?action=form:reorderActions",
                    data: mQuery('#mauticforms_actions').sortable("serialize") + "&formId=" + mQuery('#mauticform_sessionId').val()
                });
            }
        });

        mQuery('#mauticforms_actions .mauticform-row').on('mouseover.mauticformactions', function() {
            mQuery(this).find('.form-buttons').removeClass('hide');
        }).on('mouseout.mauticformactions', function() {
            mQuery(this).find('.form-buttons').addClass('hide');
        }).on('dblclick.mauticformactions', function(event) {
            event.preventDefault();
            mQuery(this).find('.btn-edit').first().click();
        });
    }


    if (mQuery('#mauticform_formType').length && mQuery('#mauticform_formType').val() == '') {
        mQuery('body').addClass('noscroll');
    }

    if (typeof Mautic.formSubmissionChart === 'undefined') {
        Mautic.renderSubmissionChart();
    }
};

Mautic.updateFormFields = function () {
    Mautic.activateLabelLoadingIndicator('campaignevent_properties_field');

    var formId = mQuery('#campaignevent_properties_form').val();
    Mautic.ajaxActionRequest('form:updateFormFields', {'formId': formId}, function(response) {
        if (response.fields) {
            var select = mQuery('#campaignevent_properties_field');
            select.find('option').remove();
            var fieldOptions = {};
            mQuery.each(response.fields, function(key, field) {
                var option = mQuery('<option></option>')
                    .attr('value', field.alias)
                    .text(field.label);
                select.append(option);
                fieldOptions[field.alias] = field.options;
            });
            select.attr('data-field-options', JSON.stringify(fieldOptions));
            select.trigger('chosen:updated');
            Mautic.updateFormFieldValues(select);
        }
        Mautic.removeLabelLoadingIndicator();
    });
};

Mautic.updateFormFieldValues = function (field) {
    field = mQuery(field);
    var fieldValue = field.val();
    var options = jQuery.parseJSON(field.attr('data-field-options'));
    var valueField = mQuery('#campaignevent_properties_value');
    var valueFieldAttrs = {
        'class': valueField.attr('class'),
        'id': valueField.attr('id'),
        'name': valueField.attr('name'),
        'autocomplete': valueField.attr('autocomplete'),
        'value': valueField.attr('value')
    };

    if (typeof options[fieldValue] !== 'undefined' && !mQuery.isEmptyObject(options[fieldValue])) {
        var newValueField = mQuery('<select/>')
            .attr('class', valueFieldAttrs['class'])
            .attr('id', valueFieldAttrs['id'])
            .attr('name', valueFieldAttrs['name'])
            .attr('autocomplete', valueFieldAttrs['autocomplete'])
            .attr('value', valueFieldAttrs['value']);
        mQuery.each(options[fieldValue], function(key, optionVal) {
            var option = mQuery("<option></option>")
                .attr('value', optionVal)
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
};

Mautic.formOnUnload = function(id) {
    if (id === '#app-content') {
        delete Mautic.formSubmissionChart;
    }
};

Mautic.formFieldOnLoad = function (container, response) {
    //new field created so append it to the form
    if (response.fieldHtml) {
        var newHtml = response.fieldHtml;
        var fieldId = '#mauticform_' + response.fieldId;
        if (mQuery(fieldId).length) {
            //replace content
            mQuery(fieldId).replaceWith(newHtml);
            var newField = false;
        } else {
            //append content
            mQuery(newHtml).insertBefore('#mauticforms_fields .mauticform-button-wrapper');
            var newField = true;
        }
        //activate new stuff
        mQuery(fieldId + " a[data-toggle='ajax']").click(function (event) {
            event.preventDefault();
            return Mautic.ajaxifyLink(this, event);
        });
        //initialize tooltips
        mQuery(fieldId + " *[data-toggle='tooltip']").tooltip({html: true});

        //initialize ajax'd modals
        mQuery(fieldId + " a[data-toggle='ajaxmodal']").on('click.ajaxmodal', function (event) {
            event.preventDefault();

            Mautic.ajaxifyModal(this, event);
        });

        mQuery('#mauticforms_fields .mauticform-row').off(".mauticform");
        mQuery('#mauticforms_fields .mauticform-row').on('mouseover.mauticformfields', function() {
            mQuery(this).find('.form-buttons').removeClass('hide');
        }).on('mouseout.mauticformfields', function() {
            mQuery(this).find('.form-buttons').addClass('hide');
        }).on('dblclick.mauticformfields', function(event) {
            event.preventDefault();
            mQuery(this).find('.btn-edit').first().click();
        });

        //show fields panel
        if (!mQuery('#fields-panel').hasClass('in')) {
            mQuery('a[href="#fields-panel"]').trigger('click');
        }

        if (newField) {
            mQuery('.bundle-main-inner-wrapper').scrollTop(mQuery('.bundle-main-inner-wrapper').height());
        }

        if (mQuery('#form-field-placeholder').length) {
            mQuery('#form-field-placeholder').remove();
        }
    }
};

Mautic.formActionOnLoad = function (container, response) {
    //new action created so append it to the form
    if (response.actionHtml) {
        var newHtml = response.actionHtml;
        var actionId = '#mauticform_action_' + response.actionId;
        if (mQuery(actionId).length) {
            //replace content
            mQuery(actionId).replaceWith(newHtml);
            var newField = false;
        } else {
            //append content
            mQuery(newHtml).appendTo('#mauticforms_actions');
            var newField = true;
        }
        //activate new stuff
        mQuery(actionId + " a[data-toggle='ajax']").click(function (event) {
            event.preventDefault();
            return Mautic.ajaxifyLink(this, event);
        });
        //initialize tooltips
        mQuery(actionId + " *[data-toggle='tooltip']").tooltip({html: true});

        //initialize ajax'd modals
        mQuery(actionId + " a[data-toggle='ajaxmodal']").on('click.ajaxmodal', function (event) {
            event.preventDefault();

            Mautic.ajaxifyModal(this, event);
        });

        mQuery('#mauticforms_actions .mauticform-row').off(".mauticform");
        mQuery('#mauticforms_actions .mauticform-row').on('mouseover.mauticformactions', function() {
            mQuery(this).find('.form-buttons').removeClass('hide');
        }).on('mouseout.mauticformactions', function() {
            mQuery(this).find('.form-buttons').addClass('hide');
        }).on('dblclick.mauticformactions', function(event) {
            event.preventDefault();
            mQuery(this).find('.btn-edit').first().click();
        });

        //show actions panel
        if (!mQuery('#actions-panel').hasClass('in')) {
            mQuery('a[href="#actions-panel"]').trigger('click');
        }

        if (newField) {
            mQuery('.bundle-main-inner-wrapper').scrollTop(mQuery('.bundle-main-inner-wrapper').height());
        }

        if (mQuery('#form-action-placeholder').length) {
            mQuery('#form-action-placeholder').remove();
        }
    }
};

Mautic.onPostSubmitActionChange = function(value) {
    if (value == 'return') {
        //remove required class
        mQuery('#mauticform_postActionProperty').prev().removeClass('required');
    } else {
        mQuery('#mauticform_postActionProperty').prev().addClass('required');
    }

    mQuery('#mauticform_postActionProperty').next().html('');
    mQuery('#mauticform_postActionProperty').parent().removeClass('has-error');
};

Mautic.renderSubmissionChart = function (chartData) {
    if (!mQuery('#submission-chart').length) {
        return;
    }
    if (!chartData) {
        chartData = mQuery.parseJSON(mQuery('#submission-chart-data').text());
    } else if (chartData.stats) {
        chartData = chartData.stats;
    }

    var ctx = document.getElementById("submission-chart").getContext("2d");
    var options = {};

    if (typeof Mautic.formSubmissionChart === 'undefined') {
        Mautic.formSubmissionChart = new Chart(ctx).Line(chartData, options);
    } else {
        Mautic.formSubmissionChart.destroy();
        Mautic.formSubmissionChart = new Chart(ctx).Line(chartData, options);
    }
};

Mautic.updateSubmissionChart = function(element, amount, unit) {
    var formId = Mautic.getEntityId();
    var query = "amount=" + amount + "&unit=" + unit + "&formId=" + formId;

    Mautic.getChartData(element, 'form:updateSubmissionChart', query, 'renderSubmissionChart');
}

Mautic.selectFormType = function(formType) {
    if (formType == 'standalone') {
        mQuery('#actions-tab').removeClass('hide');
        mQuery('#actions-container').removeClass('hide')
        mQuery('.page-header h3').text(mauticLang.newStandaloneForm);
    } else {
        mQuery('#actions-tab').addClass('hide');
        mQuery('#actions-container').addClass('hide');
        mQuery('.page-header h3').text(mauticLang.newCampaignForm);
    }

    mQuery('#mauticform_formType').val(formType);

    mQuery('body').removeClass('noscroll');

    mQuery('.form-type-modal').remove();
    mQuery('.form-type-modal-backdrop').remove();
};
