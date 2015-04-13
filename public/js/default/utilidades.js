Utilidades = {
    tempoLoadMsg: null,
    chamaLoad: function () {
        $("#prealod-request").html(Itarget.lang.outros.carregando).animate({
            top: "40px"
        }, 300);
        Utilidades.tempoLoadMsg = setInterval(function () {
            $("#prealod-request").html(Itarget.lang.outros.aindaTrabalhando);
        }, 5000);
    },
    fechaLoad: function () {
        $("#prealod-request").animate({
            top: "-28px"
        }, 300).html('');
        clearInterval(Utilidades.tempoLoadMsg);
    },
    ucwords: function (str)
    {
        return (str + '').replace(/^(.)|\s(.)/g, function ($1) {
            return $1.toUpperCase();
        });
    },
    novaJanela: function (url, tituloJanela, largura, altura)
    {
        var titulo = tituloJanela ? tituloJanela : Itarget.lang.tituloJanelas.recibo;
        var esquerda = (screen.width - largura) / 2;
        var topo = (screen.height - altura) / 2;
        if (largura != undefined && largura > 0 && altura != undefined && altura > 0) {
            window.open(url, titulo, 'height=' + altura + ', width=' + largura + ', top=' + topo + ', left=' + esquerda);
        } else {
            window.open(url, titulo, "");
        }
    },
    /**
     * Cria o elento passado pelo parametro se o mesmo nao existir
     **/
    criaElemento: function (id, tag)
    {
        if (!$("#" + id).is(tag)) {
            var elemento = document.createElement(tag);
            elemento.id = id;
            $("body").append(elemento);
        }
    },
    /**
     * Pega as cidades de acordo com o estado informado e monta no select
     **/
    getCidades: function (selectIdEstado, selectIdCidades, idSelected, change) {

        $(selectIdEstado).change(function () {

            if ($(this).val()) {

                $(selectIdCidades).empty().html("<option value=''>" + Itarget.lang.outros.aguarde + "</option>").attr("disabled", "disabled");
                if (undefined == baseUrl) {
                    var baseUrl = '/';
                }

                $.getJSON(baseUrl + 'Utilidades/get-municipios/uf/' + $(this).val(), function (data) {

                    $(selectIdCidades).removeAttr("disabled").empty().html("<option value=''>" + Itarget.lang.outros.selecione + "</option>");
                    $.each(data, function (i, item) {
                        if (item) {
                            if (!idSelected)
                                $(selectIdCidades).append("<option value='" + item.MNC_COD_IBGE + "'>" + item.MNC_DESCRICAO + "</option>");
                            else
                            if (idSelected == item.MNC_COD_IBGE || idSelected == item.MNC_DESCRICAO)
                                $(selectIdCidades).append("<option selected value='" + item.MNC_COD_IBGE + "'>" + item.MNC_DESCRICAO + "</option>");
                            else
                                $(selectIdCidades).append("<option value='" + item.MNC_COD_IBGE + "'>" + item.MNC_DESCRICAO + "</option>");
                        }
                    });
                });
            } else {
                $(selectIdCidades).empty().html("<option value=''>" + Itarget.lang.outros.selecioneEstado + "</option>");
            }
        });
        if (change)
            $(selectIdEstado).change();
    },
    /**
     * Passa o foco do campo para o proximo campo do formulario
     **/
    nextForm: function (elementos, bntSave) {
        $(elementos).bind('keydown', 'return', function () {

            var inputs = $(this).parents("form").eq(0).find(":input").not("input[type='hidden']").filter(":visible");
            var idx = inputs.index(this);
            if (idx == inputs.length - 1) {
                //$(bntSave).focus(); return;
            } else {
                inputs[idx + 1].focus();
            }
        });
    },
    getEndereco: function (cep, campoUf, campoCidade, callback) {

        $.getJSON(baseUrl + "Utilidades/get-endereco/cep/" + cep, {}, function (info) {

            Utilidades.getCidades(campoUf, campoCidade, info.CIDADE, false);
            if (callback) {
                callback(info);
            }
        });
    },
    criarBotaoJqueryUi: function (name, atributos) {

        name = name ? name : alert('O name deve ser preenchido!');
        atributos = atributos ? atributos : '';
        var text = '<div class="ui-dialog-buttonset">';
        text += '<button ' + atributos + ' onmouseover="$(this).addClass(\'ui-state-hover\')" onmouseout="$(this).removeClass(\'ui-state-hover\')" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">';
        text += '<span class="ui-button-text">' + name + '</span>';
        text += '</button>';
        text += '</div>';
        return text;
    },
    validarEmail: function (mail) {
        var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
        if (typeof (mail) == "string") {
            if (er.test(mail)) {
                return true;
            }
        } else if (typeof (mail) == "object") {
            if (er.test(mail.val())) {
                return true;
            }
        } else {
            return false;
        }
    },
    validarCpf: function (value) {
        value = value.replace('.', '');
        value = value.replace('.', '');
        cpf = value.replace('-', '');
        while (cpf.length < 11)
            cpf = "0" + cpf;
        var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
        var a = [];
        var b = new Number;
        var c = 11;
        for (i = 0; i < 11; i++) {
            a[i] = cpf.charAt(i);
            if (i < 9)
                b += (a[i] * --c);
        }
        if ((x = b % 11) < 2) {
            a[9] = 0
        } else {
            a[9] = 11 - x
        }
        b = 0;
        c = 11;
        for (y = 0; y < 10; y++)
            b += (a[y] * c--);
        if ((x = b % 11) < 2) {
            a[10] = 0;
        } else {
            a[10] = 11 - x;
        }
        if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg))
            return false;
        return true;
    },
    validarCnpj: function (cnpj) {
        cnpj = jQuery.trim(cnpj); // retira espaços em branco
        cnpj = cnpj.replace('/', '');
        cnpj = cnpj.replace('.', '');
        cnpj = cnpj.replace('.', '');
        cnpj = cnpj.replace('-', '');
        var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
        digitos_iguais = 1;
        if (cnpj.length < 14 && cnpj.length < 15) {
            return false;
        }
        for (i = 0; i < cnpj.length - 1; i++) {
            if (cnpj.charAt(i) != cnpj.charAt(i + 1)) {
                digitos_iguais = 0;
                break;
            }
        }

        if (!digitos_iguais) {
            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0, tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) {
                    pos = 9;
                }
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0)) {
                return false;
            }
            tamanho = tamanho + 1;
            numeros = cnpj.substring(0, tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) {
                    pos = 9;
                }
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1)) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    },
    validarData: function validaData(data) {
        var regex = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/;
        if (regex.test(data)) {
            return true;
        } else {
            return false;
        }
    },
    validarCep: function validaCep(cep) {
        var regex = /^\d{2}[.]\d{3}[-]\d{3}$/;
        if (regex.test(cep)) {
            return true;
        } else {
            return false;
        }
    }

}

/*********************/
/* VALIDA CPF E CNPJ *
 /*********************/
var NUM_DIGITOS_CPF = 11;
var NUM_DIGITOS_CNPJ = 14;
var NUM_DGT_CNPJ_BASE = 8;
String.prototype.lpad = function (pSize, pCharPad)
{
    var str = this;
    var dif = pSize - str.length;
    var ch = String(pCharPad).charAt(0);
    for (; dif > 0; dif--)
        str = ch + str;
    return (str);
}

String.prototype.trim = function ()
{
    return this.replace(/^\s*/, "").replace(/\s*$/, "");
}

function unformatNumber(pNum)
{
    return String(pNum).replace(/\D/g, "").replace(/^0+/, "");
}

function formatCpfCnpj(pCpfCnpj, pUseSepar, pIsCnpj)
{
    if (pIsCnpj == null)
        pIsCnpj = false;
    if (pUseSepar == null)
        pUseSepar = true;
    var maxDigitos = pIsCnpj ? NUM_DIGITOS_CNPJ : NUM_DIGITOS_CPF;
    var numero = unformatNumber(pCpfCnpj);
    numero = numero.lpad(maxDigitos, '0');
    if (!pUseSepar)
        return numero;
    if (pIsCnpj)
    {
        reCnpj = /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/;
        numero = numero.replace(reCnpj, "$1.$2.$3/$4-$5");
    }
    else
    {
        reCpf = /(\d{3})(\d{3})(\d{3})(\d{2})$/;
        numero = numero.replace(reCpf, "$1.$2.$3-$4");
    }
    return numero;
}

function dvCpfCnpj(pEfetivo, pIsCnpj)
{
    if (pIsCnpj == null)
        pIsCnpj = false;
    var i, j, k, soma, dv;
    var cicloPeso = pIsCnpj ? NUM_DGT_CNPJ_BASE : NUM_DIGITOS_CPF;
    var maxDigitos = pIsCnpj ? NUM_DIGITOS_CNPJ : NUM_DIGITOS_CPF;
    var calculado = formatCpfCnpj(pEfetivo, false, pIsCnpj);
    calculado = calculado.substring(2, maxDigitos);
    var result = "";
    for (j = 1; j <= 2; j++)
    {
        k = 2;
        soma = 0;
        for (i = calculado.length - 1; i >= 0; i--)
        {
            soma += (calculado.charAt(i) - '0') * k;
            k = (k - 1) % cicloPeso + 2;
        }
        dv = 11 - soma % 11;
        if (dv > 9)
            dv = 0;
        calculado += dv;
        result += dv
    }

    return result;
}

function isCpf(pCpf)
{
    var numero = formatCpfCnpj(pCpf, false, false);
    var base = numero.substring(0, numero.length - 2);
    var digitos = dvCpfCnpj(base, false);
    var algUnico, i;
    if (numero != base + digitos)
        return false;
    algUnico = true;
    for (i = 1; i < NUM_DIGITOS_CPF; i++)
    {
        algUnico = algUnico && (numero.charAt(i - 1) == numero.charAt(i));
    }
    return (!algUnico);
}

function isCnpj(pCnpj)
{
    var numero = formatCpfCnpj(pCnpj, false, true);
    var base = numero.substring(0, NUM_DGT_CNPJ_BASE);
    var ordem = numero.substring(NUM_DGT_CNPJ_BASE, 12);
    var digitos = dvCpfCnpj(base + ordem, true);
    var algUnico;
    if (numero != base + ordem + digitos)
        return false;
    algUnico = numero.charAt(0) != '0';
    for (i = 1; i < NUM_DGT_CNPJ_BASE; i++)
    {
        algUnico = algUnico && (numero.charAt(i - 1) == numero.charAt(i));
    }
    if (algUnico)
        return false;
    if (ordem == "0000")
        return false;
    return (base == "00000000"
            || parseInt(ordem, 10) <= 300 || base.substring(0, 3) != "000");
}

function isCpfCnpj(pCpfCnpj)
{
    var numero = pCpfCnpj.replace(/\D/g, "");
    if (numero.length > NUM_DIGITOS_CPF)
        return isCnpj(pCpfCnpj)
    else
        return isCpf(pCpfCnpj);
}

/********************************/


function disableKey(event) {
    if (!event)
        event = window.event;
    if (!event)
        return;
    var keyCode = event.keyCode ? event.keyCode : event.charCode;
    if (keyCode == 116 || keyCode == 115 || event.ctrlKey || event.altKey) {
        window.status = "F5 key detected! Attempting to disabling default response.";
        window.setTimeout("window.status='';", 2000);
        // Standard DOM (Mozilla):
        if (event.preventDefault)
            event.preventDefault();
        //IE (exclude Opera with !event.preventDefault):
        if (document.all && window.event && !event.preventDefault) {
            event.cancelBubble = true;
            event.returnValue = false;
            event.keyCode = 0;
        }

        return false;
    }

}

function setEventListener(eventListener) {
    if (document.addEventListener)
        document.addEventListener('keypress', eventListener, true);
    else if (document.attachEvent)
        document.attachEvent('onkeydown', eventListener);
    else
        document.onkeydown = eventListener;
    if (!document.getElementById)
        return;
    var el = document.getElementById("Msg");
    if (el)
        el.innerHTML = "Event handler added.";
}

function unsetEventListener(eventListener) {
    if (document.removeEventListener)
        document.removeEventListener('keypress', eventListener, true);
    else if (document.detachEvent)
        document.detachEvent('onkeydown', eventListener);
    else
        document.onkeydown = null;
    if (!document.getElementById)
        return;
    var el = document.getElementById("Msg");
    if (el)
        el.innerHTML = "Event handler removed.";
}