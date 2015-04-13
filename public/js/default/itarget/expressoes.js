var Expressoes = {
    somenteNumeros: function(obj) {
        $(obj).val($(obj).val().replace(/[^\d]/gi, ''));
    },
    somenteTextos: function(obj) {
        $(obj).val($(obj).val().replace(/[\d]/gi, ''));
    }
};