(function($) {
    $.fn.finder_box = function(options) {

        var defaults = {
            keyCode: '113',
            container: '#finder_box',
            url: '',
            params: '',
            camposparams: '',
            fill: '',
            title: '',
            width: 600,
            height: 400,
            position: "top",
            urlChange: '',
            focusAfter: null,
            zIndex: 110000
        };

        var options = $.extend(defaults, options);

        return this.each(function() {
            //init
            options.focusAfter = options.focusAfter ? options.focusAfter : '#' + $(this).attr('id');

            $(this).keydown(function(e) {
                if (e.keyCode == options.keyCode) {

                    if (options.params != '' && options.params != undefined) {
                        cont = 0;
                        $.each(options.params, function(i, value) {
                            options.params[i] = $(options.camposparams[cont++]).val();
                        });

                        options.params['fill'] = options.fill;
                        options.params['focusAfter'] = options.focusAfter;
                        
                        $(options.container).load(options.url, options.params, function() {
                            $('#finderSearchFrm input[type!="hidden"]:first').focus();
                        });

                    } else {
                        $(options.container).load(options.url, {'fill': options.fill, 'focusAfter': options.focusAfter}, function() {
                            $('#finderSearchFrm input[type!="hidden"]:first').focus();
                        });

                    }

                    $(options.container).dialog({
                        title: options.title,
                        width: options.width,
                        height: options.height,
                        position: options.position,
                        zIndex: options.zIndex
                    });

                }
            });

            $(this).change(function(e) {

                var field_id = $(this).attr('id');
                var field_value = $(this).val();

                if (field_value.length > 0)
                {
                    if (options.params != '' && options.params != undefined) {
                        options.params[field_id] = field_value;
                    } else {
                        options.params = JSON.parse('{"' + field_id + '":' + field_value + '}');
                    }
                    $.post(
                            options.urlChange,
                            options.params,
                            function(json) {
                                $.each(options.fill, function(field, value) {

                                    if (value instanceof Object) {
                                        $(value).val(json.dados[field]);
                                    } else {
                                        $(field).val(json.dados[value]);
                                    }


                                });
                            }, "json"
                            );
                }
                else
                {
                    $.each(options.fill, function(field, value) {
                        $(field).val('');
                    });
                }
            });
        });
    };
})(jQuery);