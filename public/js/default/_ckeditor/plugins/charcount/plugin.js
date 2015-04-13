CKEDITOR.plugins.add( 'charcount',
    {
        init : function( editor )
        {
            var defaultLimit = 3500;
            var defaultFormat = '<span class="cke_charcount_count" style="margin:10px 0px 0px 10px;">%count%</span> de <span class="cke_charcount_limit">%limit%</span> caracteres';
            var limit = defaultLimit;
            var format = defaultFormat;

            var intervalId;
            var lastCount = 0;
            var limitReachedNotified = false;
            var limitRestoredNotified = false;


            if ( true )
            {
                function counterId( editor )
                {
                    return 'cke_charcount_' + editor.name;
                }

                function counterElement( editor )
                {
                    return document.getElementById( counterId(editor) );
                }

                function updateCounter( editor )
                {
                    var str = editor.getData();
                    str = str.replace(/<\s*br\/*>/gi, "\n");
                    str = str.replace(/<\s*a.*href="(.*?)".*>(.*?)<\/a>/gi, " $2 (Link->$1) ");
                    str = str.replace(/<\s*\/*.+?>/ig, "\n");
                    str = str.replace(/ {0,}/gi, "");
                    str = str.replace(/\n+\s*/gi, "\n\n");
                    str = str.replace(/<\/?[a-z][a-z0-9]*[^<>]*>/ig, "");

                    var count = str.length-2;
//                    if( count == lastCount ){
//                        return true;
//                    } else {
//                        lastCount = count-2;
//                    }

                    if( !limitReachedNotified && count > limit ){
                        limitReached( editor );
                    } else if( !limitRestoredNotified && count < limit ){
                        limitRestored( editor );
                    }

                    count = count - 2;

                    if (count < 0) {
                        count = 0;
                    }

                    var html = format.replace('%count%', count).replace('%limit%', limit);
                    counterElement(editor).innerHTML = html;
                }

                function limitReached( editor )
                {
                    limitReachedNotified = true;
                    limitRestoredNotified = false;

                    try {
                        editor.setUiColor( '#FFC4C4' );
                    } catch(e) {

                    }
                }

                function limitRestored( editor )
                {
                    limitRestoredNotified = true;
                    limitReachedNotified = false;
                    try {
                        editor.setUiColor( '#C4C4C4' );
                    } catch(e) {

                    }
                }

                editor.on( 'themeSpace', function( event )
                {
                    if ( event.data.space == 'bottom' )
                    {
                        event.data.html += '<div id="'+counterId(event.editor)+'" class="cke_charcount"' +
                        ' title="' + CKEDITOR.tools.htmlEncode( 'Character Counter' ) + '"' +
                        '>&nbsp;</div>';
                    }
                }, editor, null, 100 );

                editor.on( 'instanceReady', function( event )
                {
                    if( editor.config.charcount_limit != undefined )
                    {
                        limit = editor.config.charcount_limit;
                    }

                    if( editor.config.charcount_format != undefined )
                    {
                        format = editor.config.charcount_format;
                    }
                    
                    updateCounter(editor);


                }, editor, null, 100 );

                editor.on( 'dataReady', function( event )
                {
                    var count = event.editor.getData().length;
                    if( count > limit ){
                        limitReached( editor );
                    }
                    updateCounter(event.editor);
                }, editor, null, 100 );

                editor.on( 'key', function( event )
                {
                    //updateCounter(event.editor);
                    }, editor, null, 100 );

                editor.on( 'focus', function( event )
                {
                    editorHasFocus = true;
                    intervalId = window.setInterval(function (editor) {
                        updateCounter(editor)
                    }, 10, event.editor);
                }, editor, null, 100 );

                editor.on( 'blur', function( event )
                {
                    editorHasFocus = false;
                    if( intervalId )
                        clearInterval(intervalId);
                }, editor, null, 10 );
            }

        }

    });
