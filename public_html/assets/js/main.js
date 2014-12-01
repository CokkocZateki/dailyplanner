$(document).ready(function(){

    //Form Processor
    $(document).on('submit', '.class-forms', function(e) {
        e.preventDefault();
        var dis = $(this);
        var disBtns = $(this).find('.modal-footer input, .modal-footer button, button[type="submit"], input[type="submit"]');
        var rI = dis.find('.required');
        var vF = true;

        rI.each(function(){
            if (vF && ($(this).val() == '' || $(this).find('option:selected') == '')) {
                promptMessage('error', 'Fill empty required fields.');
                $(this).focus();
                vF = false;
            } else {
                if (dis.attr('id') == 'add-task-form') {
                    if (vF && ($(this).hasClass('duration-input') == true)) {
                        var durationReference = dis.find('#dp_min_remaining');
                        if ($(this).val() > 240 || $(this).val() < 0) {
                            promptMessage('error', 'Duration value should not exceed 240 minutes and be less than 0.');
                            vF = false;
                        } else {
                            if ($(this).val() > durationReference.val()) {
                                promptMessage('error', 'Desired duration should not exceed remaining '+durationReference.val()+' minutes');
                                vF = false;
                            }    
                        }
                        
                    }                    
                }
            }
        });

        if (vF) {
            disBtns.addClass('disabled').attr('disabled', 'disabled');
            promptMessage('info', 'Loading...');
            var dataString = dis.serialize();
            var urlPath = dis.attr('action');
            if(dis.hasClass('task-form') == true) {
                var dateInput = $('#dp_date').val();
            }
            $.ajax({
                type: 'POST',
                url: urlPath,
                data: dataString,
                dataType: 'json',
                success: function(data, textStatus, XMLHttpRequest){
                    if(data.status == 'success') {

                        if (data.foredit == 'yes') {
                        } else if(data.fordeleted == 'yes'){
                            $('#delete-task-modal').modal('hide');
                        } else {
                            dis.find('input[type="text"], input[type="email"], input[type="password"]').val('');
                            dis.find('textarea').val('');
                            dis.find('select option:eq(0)').attr('selected', 'selected');
                            dis.find('#custom-tag').removeAttr('style');
                        }

                        if (data.updateThisPage == 'yes') {
                            var apps = $('#appointments')
                            if(apps.find('#no-record').html() != '') {
                                apps.find('#no-record').fadeOut().remove();
                                fetchAppointments(dateInput);
                                triggerWeekdays();
                            }

                        } else {
                            dis.find('.modal-footer').prepend('<a href="'+data.link+'" class="btn btn-default pull-left">Go to Week Group</a>');
                        }
                    }
                    if(data.redirect) {
                        setTimeout('window.location = "'+data.redirect+'"',500);
                    }
                    promptMessage(data.status, data.message);
                    disBtns.removeClass('disabled').removeAttr('disabled');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    promptMessage('error', 'Try refreshing the page.');
                    disBtns.removeClass('disabled').removeAttr('disabled');
                }

            });             

        }
    });

    fetchAppointments = function(date) {
        $.getJSON( '/users/getAppointmentsOnCreateUpdate?dateFull='+date+'', function( data ) {

            var items = [];
            if (data != '') {
                $.each( data, function( key, item ) {
                    items.push('<tr class="app-datas">');

                        items.push( '<td class="hidden-vals hide"><span id="hv-id">'+item.id+'</span><span id="hv-date">'+item.date+'</span><span id="hv-title">'+item.title+'</span><span id="hv-description">'+item.description+'</span><span id="hv-duration">'+item.duration+'</span><span id="hv-color">'+item.color+'</span><span id="hv-timeslot">'+item.timeslot+'</span></td>' );

                        items.push('<td>'+item.date+'</td><td>'+item.title+'</td><td>'+item.description+'</td><td>'+item.duration+' minutes</td><td><div class="label" style="background-color: '+item.color+'; ?>">Label</div></td><td>'+item.timeslot+'</td><td><a href="#" class="btn btn-xs btn-success m-r-xs" id="edit-app" data-toggle="modal" data-target="#edit-task-modal">Edit</a><a href="#" class="btn btn-xs btn-dark" id="delete-app" data-toggle="modal" data-target="#delete-task-modal">Delete</a></td>');
                    items.push('</tr>');
                });

                $('#appointments .app-datas').remove();
                $('#appointments').append(items.join("")).hide().fadeIn();
            } else {
                $('#appointments .app-datas').remove();
                $('#appointments').append('<tr id="no-record"><td colspan="7" class="text-center">No Appointments</td></tr>').hide().fadeIn();                
            }
        });

    };

    promptMessage = function(t, m, d) {

        var p = $('body .pcprompt');

        if (p.html() == undefined) {
            var promptScript = '<div class="pcprompt alert alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><span id="message"><strong>Warning!</strong> Better check yourself, you\'re not looking too good.</span></div>'
            $(promptScript).appendTo($('body'));
            var p = $('body .pcprompt');
            createMessage(p, t, m, d);
        } else {
            createMessage(p, t, m, d);    
        }
    };

    createMessage = function(prompt, type, message, dismiss){

        var messageDiv = prompt.find('#message');
        var messageOutput = '';
        var alertType = '';
        if (type == 'error') {
            alertType = 'danger';
            messageOutput = '<strong>Error!</strong> '+message+'';
        } else if (type == 'warning') {
            alertType = 'warning';
            messageOutput = '<strong>Warning!</strong> '+message+'';
        } else if (type == 'info') {
            alertType = 'info';
            messageOutput = '<strong>Attention!</strong> '+message+'';
        } else if (type == 'success') {
            alertType = 'success';
            messageOutput = '<strong>Success!</strong> '+message+'';
        }
        prompt.removeClass('alert-warning').removeClass('alert-danger').removeClass('alert-info').removeClass('alert-success');
        prompt.addClass('alert-'+alertType+'');
        messageDiv.html(messageOutput);
        prompt.slideDown(200, function(){
            setTimeout(function(){
                if(prompt.html() != undefined) {
                    prompt.slideUp();
                }
            }, 15000);
        });        

    }

    $(document).on('change', '#custom-color-select', function(e){

        e.preventDefault();
        var dis = $(this);
        var disCustom = dis.find('option:selected').attr('class');
        var disVal = dis.find('option:selected').val();
        var disWrap = dis.closest('div');
        //Init
        disWrap.find('#custom-tag').removeAttr('style');
        disWrap.find('#dp_color_custom').removeClass('active').hide().val('');

        if (disCustom == 'cus-opt') {
            disWrap.find('#dp_color_custom').addClass('active').fadeIn().focus();
        } else {
            disWrap.find('#custom-tag').css('background-color',''+disVal+'');
        }

    });

    $(document).on('blur', '#dp_color_custom', function(){

        var dis = $(this);
        var disWrap = dis.closest('div');

        if (dis.hasClass('active')) {
            if (dis.val() == '') {
                disWrap.find('#custom-tag').removeAttr('style');
            } else {
                if (dis.val().length < 6) {
                    promptMessage('warning', 'Alpha numeric value should be more than 5');
                    dis.focus();
                }
                disWrap.find('#custom-tag').css('background-color','#'+dis.val()+'');
                disWrap.find('#custom-color-select option:last-child').val('#'+dis.val()+'');
            }
        }
    });

    $(document).on('focus', '#dp_date', function(){

         $(this).closest('form').find('#dp_duration').val();       

    });

    checkDuration = function(d){

        var dataString = d.closest('form').serialize();
        d.closest('form').find('#dp_duration').css('opacity', '.3');
        promptMessage('info', 'Retreiving new remaining minutes');

        $.ajax({
            type: 'POST',
            url: '/users/checkDateMinutes',
            data: dataString,
            dataType: 'json',
            success: function(data, textStatus, XMLHttpRequest){
                if(data.status == 'success') {

                    d.closest('form').find('#dp_min_remaining').val(data.setminutes);
                    d.closest('form').find('#dp_duration').css('opacity', '1');

                    if (data.setminutes == 0) {
                        promptMessage('warning', 'You do not have remaining minutes for this timeslot. Try chaging the timeslot and the date.');
                    } else {
                        
                        promptMessage('info', data.message);
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                promptMessage('error', 'Try refreshing the page.');
            }

        });         

    };

    $(document).on('focus', '#dp_duration', function(){
        checkDuration($(this));
    });

    $(document).on('click', '.dp_timeslot', function(){
        checkDuration($(this));
    });

    weekDateSchedFetcher = function(dt, sl){
        var datePassed = dt.html();
        $.getJSON( '/users/getWeekDateSched?date='+datePassed+'&slot='+sl+'', function( data ) {
            weekDateSchedLoader(data, dt, sl);
        });
    };

    triggerWeekdays = function() {
        //Init
        $('.slots-container').html('');
        $('.weekDays').each(function(){
            weekDateSchedFetcher($(this), 'AM');
            weekDateSchedFetcher($(this), 'PM');
        });
    };

    triggerWeekdays();


    convertToPixel = function(min) {
        var f = min * 100;
        var d = f / 240;
        return d;
    };

    updateTargetElement = function(el, t){

        var id = el.attr('tag-id');
        var tagDate = el.attr('tag-date');
        var tagTimeSlot = el.attr('tag-timeslot');
        var elMinutes = el.attr('tag-minutes');
        var date = t.closest('.wdGrp').find('.weekDays').html();
        var slot = t.closest('.slots-container');
        var timeslot = 'AM';
        var vD = true;
        var totalMins = 0;
        var newMinutes = 0;

        if (slot.hasClass('pmSlots') == true) {
            timeslot = 'PM'
        }    

        if (slot.html() != '') {
            slot.find('.tags-label').each(function(){
                totalMins = parseInt(newMinutes) + parseInt($(this).attr('tag-minutes'));
                newMinutes = totalMins;
            });  
            var toCombined = parseInt(newMinutes) + parseInt(elMinutes);
            if(toCombined > 240) {
                vD = false;
                el.draggable({ revert: true });
                promptMessage('warning', 'Total number of minutes exceed the allowed minutes per timeslot.');
            }
        } 

        if (timeslot == tagTimeSlot && tagDate == date) {
            vD = false;
            el.draggable({ revert: true });
        }
        
        if(vD) {

            var dataString = {};
            dataString['id'] = id;
            dataString['date'] = date;
            dataString['timeslot'] = timeslot;

            $.ajax({
                type: 'POST',
                url: '/users/updateTaskByDrag',
                data: dataString,
                dataType: 'json',
                success: function(data, textStatus, XMLHttpRequest){
                    if(data.status == 'success') {
                        triggerWeekdays();
                        fetchAppointments(tagDate);
                    }
                    promptMessage(data.status, data.message);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    promptMessage('error', 'Try refreshing the page.');
                }

            });
        }
        
    };

    declareDraggable = function(slot) {

        slot.find('.tags-label').each(function(){
            $(this).draggable({ 
                snap: '.slots-container',
                revert: 'invalid'
            });
        });

    };

    weekDateSchedLoader = function(f, el, slot){
        var disWrap = el.closest('.wdGrp');
        var slotWrap = '';
        if (slot == 'AM') {
            slotWrap = disWrap.find('.amSlots');
        } else {
            slotWrap = disWrap.find('.pmSlots');
        }
        if (f == '') {
        } else {
            items = [];
            $.each( f, function( key, item ) {
                var hFD = convertToPixel(item.duration);
                items.push( '<div tag-id="'+item.id+'" tag-timeslot="'+item.timeslot+'" tag-date="'+item.date+'" tag-minutes="'+item.duration+'" data-toggle="tooltip" data-placement="top" data-title="'+item.title+' for '+item.duration+' minutes" class="tags-label" style="background-color: '+item.color+';width: '+hFD+'%;" title="'+item.title+' for '+item.duration+' minutes">'+item.title+' for '+item.duration+' minutes</div>' );
            });

            slotWrap.append(items.join("")).hide().fadeIn(1000);
            declareDraggable(slotWrap);
        }
    };

    $( ".amSlots, .pmSlots" ).droppable({
        drop: function( event, ui ) {
            updateTargetElement(ui.draggable, $(this));
        }
    });

    $(document).on('click', '#edit-app', function(){

        var dis = $(this);
        var hVWrap = dis.closest('.app-datas').find('.hidden-vals');
        //Init
        var disID = hVWrap.find('#hv-id').html();
        var disDate = hVWrap.find('#hv-date').html();
        var disTitle = hVWrap.find('#hv-title').html();
        var disDescription = hVWrap.find('#hv-description').html();
        var disDuration = hVWrap.find('#hv-duration').html();
        var disColor = hVWrap.find('#hv-color').html();
        var disTimeslot = hVWrap.find('#hv-timeslot').html();

        //Declare variables
        $('#edit-task-modal').find('#dp_id').val(disID);
        $('#edit-task-modal').find('#dp_date').val(disDate);
        $('#edit-task-modal').find('#dp_title').val(disTitle);
        $('#edit-task-modal').find('#dp_description').val(disDescription);
        $('#edit-task-modal').find('#dp_duration').val(disDuration);

        $('#edit-task-modal').find('#custom-color-select option').each(function(){
            if ($(this).val() == disColor) {
                $(this).attr('selected', 'selected');
                $('#edit-task-modal').find('#custom-color-select').next().css('background-color', ''+disColor+'');
            }
        });

        $('#edit-task-modal').find('#dp_duration').val(disDuration).focus();

        //$('#edit-task-modal').find('.dp_timeslot').removeAttr('checked');

        $('#edit-task-modal').find('.radio .dp_timeslot').each(function(){
            if ($(this).val() == disTimeslot) {
                $(this).attr('checked', 'checked');
            }
        });

    });

    $(document).on('click', '#delete-app', function(){

        var dis = $(this);
        var hVWrap = dis.closest('.app-datas').find('.hidden-vals');
        //Init
        var disID = hVWrap.find('#hv-id').html();
        var disDate = hVWrap.find('#hv-date').html();
        //Declare variables
        $('#delete-task-modal').find('#dp_id').val(disID);
        $('#delete-task-modal').find('#dp_date').val(disDate);

    });

    $(document).on('mouseover', '.slots-container', function(){
        $(this).css('border','1px dashed #999');
    });

    $(document).on('mouseout', '.slots-container', function(){
        $(this).css('border','none');
    });

});