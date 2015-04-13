CKEDITOR.plugins.add( 'charcount',
{
   init : function( editor )
   {
      var defaultLimit = editor.config.maxLength;
     //var defaultFormat = '<span class="cke_charcount_count">%count%</span> of <span class="cke_charcount_limit">%limit%</span> characters';
      var defaultFormat = editor.config.defaultFormat;
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
         
         function updateCounter( )
         {
         
            var text = jQuery.trim(strip_tags(editor.getData()));
            text = text.replace(/\n/g, "");

            var count = strlen(text);
                
            if( count == lastCount ) {
               return true;
            } else {
               lastCount = count;
            }
            if( !limitReachedNotified && count > limit ){
               limitReached( editor, text );
            } else if( !limitRestoredNotified && count < limit ){
               limitRestored( editor );
            }
            
            if(undefined != format) {
                var html = format.replace('%count%', count).replace('%limit%', limit);
                counterElement(editor).innerHTML = html;
            }
         }
         
         function limitReached( editor, text )
         {
            
            editor.setUiColor( '#FFC4C4' );
            
            limitReachedNotified = true;
            limitRestoredNotified = false;
            
            exibirAvisoMaxCaracAting(editor);
            //limitRestored(editor);
            editor.execCommand('undo');
            
         }
         
         function limitRestored( editor )
         {
            limitRestoredNotified = true;
            limitReachedNotified = false;
            editor.setUiColor( '#C4C4C4' );
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
            
            
         }, editor, null, 100 );
         
         editor.on( 'dataReady', function( event )
         {
            var text = jQuery.trim(strip_tags(editor.getData()));
            text = text.replace(/\n/g, "");

            var count = strlen(text);

            if( count > limit ){
               limitReached( editor, text );
            }
            updateCounter(event.editor);
         }, editor, null, 100 );
         
         editor.on( 'key', function( event )
         {
            updateCounter(event.editor);
         }, editor, null, 100 );
         
         editor.on( 'focus', function( event )
         {
            editorHasFocus = true;
            editor = event.editor;
            intervalId = window.setInterval(function (editor) {
                 updateCounter(editor)
            }, 1000, event.editor);
         }, editor, null, 100 );
         
         editor.on( 'blur', function( event )
         {
            editorHasFocus = false;
            if( intervalId )
               clearInterval(intervalId);
         }, editor, null, 100 );
      }
   }
});
