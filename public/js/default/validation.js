/* 
 * -------------------------  Validação de formulario  -------------------------
 */

var dados = new Array();
var alertText = 'Os campos vermelhos são obrigatôrios';
var backgoundColor = '#ffc4c5';

// campos obrigatórios --------------------------------------------------------- 

dados[0] = ['pessoas', 'passaporte'];
dados[1] = ['pessoas', 'nome'];
dados[2] = ['pessoas', 'cpf'];
dados[3] = ['pessoas', 'categoria_profissional_id'];
dados[4] = ['endereco', 'logradouro'];
dados[5] = ['endereco', 'bairro'];
dados[6] = ['endereco', 'pais_id'];
dados[7] = ['endereco', 'uf_id'];
dados[8] = ['endereco', 'tipo'];
dados[9] = ['endereco', 'municipio_id'];
dados[10] = ['pessoas', 'nome_crachar'];
dados[11] = ['pessoas', 'nome_empresa'];
dados[12] = ['pessoas', 'email'];

Validation = {
    required: function () {

        var invalid = 0;

        if ($('input[name="pessoas[passaporte]"]').length)
        {
            dados.splice(2, 1);
        }

        if ($('input[name="pessoas[cpf]"]').length)
        {
            dados.splice(0, 1);
        }

        $(dados).each(function (k, v) {

            tabela = v[0];
            campo = v[1];
            object = null;

            // elemento input
            objectInput = $('input[name="' + tabela + '[' + campo + ']"]').length ? $('input[name="' + tabela + '[' + campo + ']"]') : $('input[name="' + tabela + '[' + campo + '][]"]');
            // elemento textarea
            objectTextArea = $('textarea[name="' + tabela + '[' + campo + ']"]').length ? $('textarea[name="' + tabela + '[' + campo + ']"]') : $('textarea[name="' + tabela + '[' + campo + '][]"]');
            // elemento select
            objectSelect = $('select[name="' + tabela + '[' + campo + ']"]').length ? $('select[name="' + tabela + '[' + campo + ']"]') : $('select[name="' + tabela + '[' + campo + '][]"]');

            // verifica qual elemento existe!
            if (objectInput.length) {
                object = objectInput;
            } else if (objectTextArea.length) {
                object = objectTextArea;
            } else if (objectSelect.length) {
                object = objectSelect;
            }

            if (object != null) {
                // verifica se o elemento tem mais de uma dimensão
                if (object.length > 1) {
                    object.each(function () {
                        invalid += Validation.validate($(this));
                    });
                } else {
                    invalid += Validation.validate(object);
                }
            }

        });

        //-------------------------- tratamento obrigatorio --------------------

        if (invalid > 0) {
            jAlert(alertText);
            return false;
        }

        return true;

    },
    requiredForm: function (form) {

        var invalid = 0;

        $(form + ' input.required, ' + form + ' select.required, ' + form + 'textarea.required').each(function (k, v) {

            // elemento input
            objectInput = $(this);

            // verifica qual elemento existe!
            if (objectInput.length) {
                object = objectInput;
            }

            if (object != null) {
                // verifica se o elemento tem mais de uma dimensão
                if (object.length > 1) {
                    object.each(function () {
                        if ($(this).is(':visible'))
                            invalid += Validation.validate($(this));
                    });
                } else {
                    if (object.is(':visible'))
                        invalid += Validation.validate(object);
                }
            }

        });

        if (invalid > 0) {
            return false;
        }

        return true;
    },
    validate: function (object) {
        invalid = 0;
        //--------------------------- muda cor dos campos ---------------------
        if (object.val() == '') {
            object.css({'background': backgoundColor});
            invalid++;
        } else {
            object.css({'background': '#fff'});
        }

        //--------------------------- evendo de keyup ----------------------
        object.keyup(function () {
            if ($(this).val() == '') {
                $(this).css({'background': backgoundColor});
            } else {
                $(this).css({'background': '#fff'});
            }
        });

        //--------------------------- evendo onchange ----------------------
        object.change(function () {
            if ($(this).val() == '') {
                $(this).css({'background': backgoundColor});
            } else {
                $(this).css({'background': '#fff'});
            }
        });
        //--------------------------- evendo click ----------------------
        object.focus(function () {
            if ($(this).val() == '') {
                $(this).css({'background': backgoundColor});
            } else {
                $(this).css({'background': '#fff'});
            }
        });

        return invalid;
    }

}


//--------------------------- Validacoes CPF e CNPJ ----------------------------

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

var NUM_DIGITOS_CPF = 11;
var NUM_DIGITOS_CNPJ = 14;
var NUM_DGT_CNPJ_BASE = 8;

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
    pCpf = pCpf.replace(".", "");
    pCpf = pCpf.replace(".", "");
    pCpf = pCpf.replace("-", "");

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

function isData(data) {
    var _ = data.split('/');
    d = new Date(_[2] + '-' + _[1] + '-' + _[0]);
    d = d.getFullYear();
    return isNaN(d) ? false : true;
}

function isTime(time) {
    var regex = /^(((2[0-3])|[01][0-9]):[0-5][0-9])|([24]:[0][0])$/;
    return regex.test(time);
}