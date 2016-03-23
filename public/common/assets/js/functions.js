$(document).ready(function(){
    if($(".sel2").is('*')){
        $.each($(".sel2"),function(){
            var _this = $(this);
            select2Enable(_this);
        });
    }
    
    if($(".element-remove").is('*')){
        $(document).on('click','.element-remove',function(e){
            e.preventDefault();
        
            var _this = $(this);
            var getMessage = (_this.attr('data-message') !== '' && _this.attr('data-message') !== undefined ? _this.attr('data-message') : false);
            var returnUrl = (_this.attr('data-return-url') !== undefined && _this.attr('data-return-url') !== '' ? _this.attr('data-return-url') : false);
            if($("#element-remove-global-data").is('*')){
                var globalEl = $("#element-remove-global-data");
                getMessage = globalEl.attr('data-message');
                returnUrl = globalEl.attr('data-return-url');
            }
            var message = (getMessage ? getMessage : 'Are you sure you want to remove this item?');
            if(confirm(message)){
                $.ajax({
                    type: "POST",
                    url: $(_this).attr('data-destroy-url'),
                    dataType: 'json',
                    data: {'_method': 'DELETE'},
                    success: function(data){
                        document.location.href = (returnUrl ? returnUrl : document.location.href);
                        
                        if(_this.attr('data-callback-success') !== undefined){
                            var fn = window[_this.attr('data-callback-success')];
                            if (typeof fn === "function"){
                                return fn(_this);
                            }
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    }
    
    
    if($(".element-remove-image").is('*')){
        $(document).on('click','.element-remove-image',function(e){
            e.preventDefault();
        
            var _this = $(this);
            var getMessage = (_this.attr('data-message') !== '' && _this.attr('data-message') !== undefined ? _this.attr('data-message') : false);
            var returnUrl = (_this.attr('data-return-url') !== undefined && _this.attr('data-return-url') !== '' ? _this.attr('data-return-url') : false);
            var message = (getMessage ? getMessage : 'Are you sure you want to remove this item?');
            if($(_this).attr('data-destroy-url') === undefined){
                return clearUploadedImage();
            }
            
            if(confirm(message)){
                $.ajax({
                    type: "POST",
                    url: $(_this).attr('data-destroy-url'),
                    dataType: 'json',
                    data: {
                        '_method': 'DELETE'
                    },
                    success: function(data){
                        if(returnUrl){
                            document.location.href = (returnUrl ? returnUrl : document.location.href);
                        }
                        
                        if(_this.attr('data-callback-success') !== undefined){
                            var fn = window[_this.attr('data-callback-success')];
                            if (typeof fn === "function"){
                                return fn(_this,data);
                            }
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    }
    
    if($(".element-checked").is('*')){
        $(document).on('click','.element-checked',function(e){
            var checkedElements = $('.element-checked:checked').size();
            if(checkedElements > 0){
                $("#with-elements-checked").removeClass('hide');
                
                var string = $('#elements-checked-action').attr('data-selected-text');
                var text = string.replace('{totalSelected}',checkedElements);
                $('form.elements-checked-form').find(".select2-selection__placeholder").html(text);
            }
            else{
                $("#with-elements-checked").addClass('hide');
            }
        });
        
        $(document).on('submit','.elements-checked-form',function(e){
            e.preventDefault();
            
            var returnUrl = $("#element-remove-global-data").attr('data-return-url');
            var form = $(this);
            var selected = form.find('select[name="action"] option:selected');
            
            var ids = [];
            $.each($('.element-checked:checked'),function(i,item){
                ids[i] = $(this).val();
            });
            
            $.ajax({
                type: 'POST',
                url: selected.attr('data-action'),
                data: {
                    '_METHOD': selected.attr('data-method'),
                    'ids' : ids
                },
                dataType: 'json',
                success: function(data){
                    var returnTo = (returnUrl ? returnUrl : document.location.href);
                    if(data.message !== undefined){
                        data.message['redirect_to'] = returnTo;
                        messageNotification(data);
                    }
                    else{
                        if(returnUrl){
                            document.location.href = returnTo;
                        }
                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    console.log(xhr.responseText);
                }
            });
        });
        
        $(document).on('click','.btn-cancel-elements',function(e){
            e.preventDefault();
            $('.element-checked:checked').attr('checked',false);
            $("#with-elements-checked").addClass('hide');
        });
    }
    
    if($(".image-preview").is('*')){
        imagePreview();
    }
    
    if($(".password-generator").is('*')){
        randomPasswordGenerator();
    }
    
    if($(".pickdate").is('*')){
        $('.pickdate').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true
        });
    }
    
    if($(".pickdaterange").is('*')){
        $('.input-daterange input').each(function() {
            $(this).datepicker("clearDates");
        });
    }
});

function randomPasswordGenerator(){
    $(document).on('click','.generate-random-password',function(e){
        e.preventDefault();

        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        var passwordLength = ($(this).attr('data-password-length') === undefined ? 10 : $(this).attr('data-password-length'));
        
        for( var i=0; i < passwordLength; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        $(this).parent().parent().find('.password-generator-input').val(text);
    });
}

function select2Enable(_this){
    _this.select2({
        placeholder: _this.attr('placeholder'),
        allowClear: (_this.attr('data-allow-clear') ? true : false),
        tags: (_this.attr('data-with-tags') ? true : false),
        maximumSelectionLength: (_this.attr('data-max-tags') ? _this.attr('data-max-tags') : 0)
    });

    if(_this.attr('data-name') !== undefined && _this.attr('multiple') === 'multiple'){
        var selected = _this.attr('data-selected').split(',');
        var id = 'selectedMultiple_'+_this.attr('data-name');

        _this.after('<input type="hidden" id="'+id+'" name="'+_this.attr('data-name')+'" value="'+_this.attr('data-selected')+'">');
        if(_this.attr('data-selected').length > 0){
            //_this.select2('val',selected);
            _this.val(selected).change();
        }

        _this.on('change',function(item){
            $('#'+id).val($(this).val());
        });
    }
    else{
        if(_this.attr('data-selected') !== undefined){
            var id = 'selectedSingle_'+_this.attr('data-name');
            _this.after('<input type="hidden" id="'+id+'" name="'+_this.attr('data-name')+'" value="'+_this.attr('data-selected')+'">');

            var selected = _this.attr('data-selected').split(',');
            _this.val(selected).change();

            _this.on('change',function(item){
                $('#'+id).val($(this).val());
            });
        }
    }
    $('.select2').css('width','100%');
}

function URLToArray(url) {
    var request = {};
    var pairs = url.substring(url.indexOf('?') + 1).split('&');
    for (var i = 0; i < pairs.length; i++) {
        if(!pairs[i])
            continue;
        var pair = pairs[i].split('=');
        request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
     }
     return request;
}

function URLToSerializeArray(url) {
    var request = [];
    var pairs = url.substring(url.indexOf('?') + 1).split('&');
    for (var i = 0; i < pairs.length; i++) {
        if(!pairs[i])
            continue;
        var pair = pairs[i].split('=');
        request.push({ name: decodeURIComponent(pair[0]), value: decodeURIComponent(pair[1]) });
     }
     return request;
}

function serializedArrayToURL(data){
    var url = [];
    $.each(data,function(i,item){
        url[i] = item.name+'='+item.value;
    });
    return url.join('&');
}

function clearUploadedImage(obj){
    $(".single-fileupload-preview-image-preview").html('<a href="#" class="single-fileupload-remove single-fileupload-remove-preview hide"><i class="fa fa-times-circle"></i></a><small class="single-fileupload-preview-text text-muted">Imaginea va fi incarcata aici!</small><span class="single-fileupload-preview"></span>');
    
    obj = $(".single-fileupload");
    obj.wrap('<form>').closest('form').get(0).reset();
    obj.unwrap();
    
    obj.after('<input type="hidden" name="cancelUpload" value="1" id="cancelUpload">');
}