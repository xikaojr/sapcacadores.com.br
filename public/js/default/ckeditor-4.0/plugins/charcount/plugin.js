function str_replace(search, replace, subject, count) {
    //  discuss at: http://phpjs.org/functions/str_replace/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Gabriel Paderni
    // improved by: Philip Peterson
    // improved by: Simon Willison (http://simonwillison.net)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Onno Marsman
    // improved by: Brett Zamir (http://brett-zamir.me)
    //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // bugfixed by: Anton Ongson
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Oleg Eremeev
    //    input by: Onno Marsman
    //    input by: Brett Zamir (http://brett-zamir.me)
    //    input by: Oleg Eremeev
    //        note: The count parameter must be passed as a string in order
    //        note: to find a global variable in which the result will be given
    //   example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    //   returns 1: 'Kevin.van.Zonneveld'
    //   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
    //   returns 2: 'hemmo, mars'

    var i = 0,
            j = 0,
            temp = '',
            repl = '',
            sl = 0,
            fl = 0,
            f = [].concat(search),
            r = [].concat(replace),
            s = subject,
            ra = Object.prototype.toString.call(r) === '[object Array]',
            sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    }

    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }
        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp)
                    .split(f[j])
                    .join(repl);
            if (count && s[i] !== temp) {
                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}


function retira_acentos(palavra) {

    // nova = '';
    palavra = str_replace('&aacute;', 'a', palavra);
    palavra = str_replace('&agrave;', 'a', palavra);
    palavra = str_replace('&atilde;', 'a', palavra);
    palavra = str_replace('&atilde;', 'a', palavra);
    palavra = str_replace('&acirc;', 'a', palavra);
    palavra = str_replace('&auml', 'a', palavra);
    palavra = str_replace('&eacute;', 'a', palavra);
    palavra = str_replace('&egrave;', 'a', palavra);
    palavra = str_replace('&ecirc;', 'a', palavra);
    palavra = str_replace('&euml;', 'a', palavra);
    palavra = str_replace('&iacute;', 'a', palavra);
    palavra = str_replace('&igrave;', 'a', palavra);
    palavra = str_replace('&icirc;', 'a', palavra);
    palavra = str_replace('&iuml;', 'a', palavra);
    palavra = str_replace('&oacute;', 'a', palavra);
    palavra = str_replace('&ograve;', 'a', palavra);
    palavra = str_replace('&otilde;', 'a', palavra);
    palavra = str_replace('&ocirc;', 'a', palavra);
    palavra = str_replace('&ouml;', 'a', palavra);
    palavra = str_replace('&uacute;', 'a', palavra);
    palavra = str_replace('&ugrave;', 'a', palavra);
    palavra = str_replace('&ucirc;', 'a', palavra);
    palavra = str_replace('&uuml;', 'a', palavra);
    palavra = str_replace('&ccedil;', 'a', palavra);

    return palavra;
}

CKEDITOR.plugins.add('charcount',
        {
            init: function(editor)
            {
                var defaultLimit = 'unlimited';
                var defaultFormat = '<span class="cke_charcount_count">%count%</span> ' + Itarget.lang.de + ' <span class="cke_charcount_limit">%limit%</span> ' + Itarget.lang.caracteres;
                var limit = defaultLimit;
                var format = defaultFormat;
                var intervalId;
                var lastCount = 0;
                var limitReachedNotified = false;
                var limitRestoredNotified = false;
                if (true)
                {
                    function counterId(editor)
                    {
                        return 'cke_charcount_' + editor.name;
                    }

                    function counterElement(editor)
                    {
                        return document.getElementById(counterId(editor));
                    }

                    function getEditorData(editor)
                    {
                        var data = $.trim(editor.getData().replace(/<.*?>/g, '').replace(/\n/g, '').replace(/\r/g, '').replace(/&nbsp;/g, '').replace(/   /g, '')).toLowerCase();
                        data = retira_acentos(data);
                        return data;
                    }

                    function updateCounter(editor)
                    {
                        curData = getEditorData(editor);
                        curDataLength = curData.length;
                        count = curDataLength;
                        if (count == lastCount) {
                            return true;
                        } else {
                            lastCount = count;
                        }
                        if (!limitReachedNotified && count > limit) {
                            limitReached(editor);
                        } else if (!limitRestoredNotified && count < limit) {
                            limitRestored(editor);
                        }

                        var html = format.replace('%count%', count).replace('%limit%', limit);
                        counterElement(editor).innerHTML = html;
                    }

                    function limitReached(editor)
                    {
                        limitReachedNotified = true;
                        limitRestoredNotified = false;
                        editor.setUiColor('#FFC4C4');
                    }

                    function limitRestored(editor)
                    {
                        limitRestoredNotified = true;
                        limitReachedNotified = false;
                        editor.setUiColor('#C4C4C4');
                    }

                    editor.on('themeSpace', function(event)
                    {
                        if (event.data.space == 'bottom')
                        {
                            event.data.html += '<div id="' + counterId(event.editor) + '" class="cke_charcount"' +
                                    ' title="' + CKEDITOR.tools.htmlEncode('Character Counter') + '"' +
                                    '>&nbsp;</div>';
                        }
                    }, editor, null, 100);
                    editor.on('instanceReady', function(event)
                    {
                        if (editor.config.charcount_limit != undefined)
                        {
                            limit = editor.config.charcount_limit;
                        }

                        if (editor.config.charcount_format != undefined)
                        {
                            format = editor.config.charcount_format;
                        }


                    }, editor, null, 100);
                    editor.on('dataReady', function(event)
                    {
                        var count = $.trim(event.editor.getData().replace(/<.*?>/g, '').replace(/\n/g, '').replace(/\r/g, '').replace(/&nbsp;/g, '').replace(/   /g, '')).toLowerCase();
                        count = retira_acentos(count);
                        count = count.length;
                        if (count > limit) {
                            limitReached(editor);
                        }
                        updateCounter(event.editor);
                    }, editor, null, 100);
                    editor.on('key', function(event)
                    {
                        updateCounter(event.editor);
                    }, editor, null, 100);
                    editor.on('focus', function(event)
                    {
                        editorHasFocus = true;
                        intervalId = window.setInterval(function(editor) {
                            updateCounter(editor)
                        }, 1000, event.editor);
                    }, editor, null, 100);
                    editor.on('blur', function(event)
                    {
                        editorHasFocus = false;
                        if (intervalId)
                            clearInterval(intervalId);
                    }, editor, null, 100);
                }
            }
        });