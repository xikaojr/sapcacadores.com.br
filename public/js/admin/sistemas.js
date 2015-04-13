Sistemas = {
    intervalBarra: null,
    /**
     * Inputs de um form
     */
    inputsForm: "input,select,textarea",
    /**
     * Botoes de um form
     */
    inputsFormBotoes: ":submit,:button,:reset",
    aplicarMascaras: function () {
        $(".datepicker").datepicker(
                {dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                }
        );
//        $(".datepicker").mask("99/99/9999");
        $(".mask-date").mask("99/99/9999");
        $(".cpf").mask("999.999.999-99");
        $(".ano").mask("9999");
        $(".cnpj").mask("99.999.999/9999-99");
        $(".cep").mask("99999-999");
        $(".ddi").mask("99");
        $(".altura").mask("9.99");
        $(".peso").mask("999.99");
        $(".fone").mask("(99) 9999-9999?9");
        $(".hora").mask("99:99");
        $(".email").css("text-transform", "lowercase");

        $(".numeros").bind("keyup blur focus", function (e) {
            if ($(this).hasClass("fone")) {
                return false;
            }
            Expressoes.somenteNumeros($(this));
        });
    },
    removerMascaras: function () {
//$(".date, .cpf, .ano, .cnpj, .cep, .ddi, .fone, .hora").unmask();
        //$(".date, .datemask").unmask("99/99/9999");
        $(".cpf").unmask("999.999.999-99");
        $(".ano").unmask("9999");
        $(".cnpj").unmask("99.999.999/9999-99");
        $(".cep").unmask("99999-999");
        $(".ddi").unmask("99");
        //$(".fone").unmask("(99) 9999-9999?9");
        $(".hora").unmask("99:99");
        $(".numeros").bind("keyup blur focus", function (e) {
            return false;
        });
    },
    validaEmail: function (email)
    {
        if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)))
            return false;
        return true;
    },
    alert: function (msg, callback) {
        bootbox.dialog(msg, [{
                "label": "Confirmar!",
                "class": "btn-small btn-success",
                "callback": callback}]);
    },
    /**
     * Preenche uma combo de municipios, baseado na uf
     *
     * @param uf string uf a ser pesquisada
     * @param combo jQuery instancia da combo a ser populada
     * @param selecionado
     * @return void
     */
    getMunicipios: function (uf, combo, selecionado) {

        try {
            if (uf != "") {
                $.getJSON(baseUrl + "/default/utilidades/get-municipios", {
                    uf: uf
                },
                function (data) {
                    var count = data.length;
                    combo.html("");
                    for (var i = 0; i < count; i++) {

                        sel = (undefined != selecionado && selecionado == data[i].id) ? " selected='selected' " : "";
                        combo.append(
                                '<option '
                                + sel
                                + 'value="' + data[i].id + '">'
                                + data[i].descricao
                                + '</option>');
                    }

                    // $(".chzn-select").trigger("liszt:updated");
                });
            } else {
                combo.empty();
                combo.append('<option value="0">' + Itarget.lang.outros.selecioneEstado + '</option>');
            }
        } catch (e) {
            console.log(e);
        }
    },
    getMunicipiosMult: function (tipo, campoUf, selectMunicipios, municipioAtual) {
        var uf = "", campoMunicipios = "", id = "";
        if ($(campoUf).val() != "") {

            uf = $(campoUf).find("option:selected").html().trim();
            id = $(campoUf).attr("id").split("_");
            id = id[id.length - 1];
            campoMunicipios = $("select[name='endereco" + tipo + "[" + id + "][municipio_id]']");
            if (selectMunicipios) {
                campoMunicipios = selectMunicipios;
            }

            if (uf.length == 2) {
                Sistemas.getMunicipios(uf, campoMunicipios, municipioAtual);
            }
        }
    },
    cep: function (cep, key, tipo, codigoBrasil) {
        $('#endereco-' + tipo + 'logradouro' + key).val('Carregando...');
        $('#endereco-' + tipo + 'bairro' + key).val('Carregando...');
        $('#endereco-' + tipo + 'pais_id' + key).val(codigoBrasil);
        $.post(
                "/utilidades/cep",
                {cep: cep},
        function (data) {
            if (parseInt(data.resultado) == 1) {
                $('#endereco-' + tipo + 'tipo_logradouro_id' + key).val(data.tipo_logradouro);
                $('#endereco-' + tipo + 'logradouro' + key).val(data.logradouro);
                $('#endereco-' + tipo + 'bairro' + key).val(data.bairro);
                $('#endereco-' + tipo + 'pais_id' + key).val(codigoBrasil);
                $('#endereco-' + tipo + 'uf_id' + key).val(data.uf_id);
                $('#endereco-' + tipo + 'uf' + key).val(data.uf);
                Sistemas.getMunicipios(data.uf, $('#endereco-' + tipo + 'municipio_id' + key), data.municipio_id);
                $('#endereco-' + tipo + 'municipio_id' + key).val(data.municipio_id);
                $('#endereco-' + tipo + 'municipio_desc' + key).val(data.cidade);
            } else {
                $('#endereco-' + tipo + 'tipo_logradouro_id' + key).val('');
                $('#endereco-' + tipo + 'logradouro' + key).val('');
                $('#endereco-' + tipo + 'bairro' + key).val('');
                $('#endereco-' + tipo + 'pais_id' + key).val('');
                $('#endereco-' + tipo + 'uf_id' + key).val('');
                $('#endereco-' + tipo + 'municipio_id' + key).val('');
                $('#endereco-' + tipo + 'uf' + key).val(data.uf);
            }
            // chama o pais para campo cidade internacional
            $('#endereco-' + tipo + 'pais_id' + key).change();
        }, "json");
        return false;
    },
    cepGeneric: function (cep, hashId) {
        $('#' + hashId + '-' + 'logradouro').val('Carregando...');
        $('#' + hashId + '-' + 'bairro').val('Carregando...');
        $.post(
                baseUrl + "/default/utilidades/cep",
                {cep: cep},
        function (data) {
            if (parseInt(data.resultado) == 1) {
                $('#' + hashId + '-logradouro').val(data.tipo + ' ' + data.rua);
                $('#' + hashId + '-bairro').val(data.bairro);
                $('#' + hashId + '-uf').val(data.estado);
                Sistemas.getMunicipios(data.estado, $('#' + hashId + '-municipio'), data.cidade);
            } else {
                $('#' + hashId + '-logradouro').val('');
                $('#' + hashId + '-bairro').val('');
                $('#' + hashId + '-uf').val(data.uf);
            }
        }, "json");
        return false;
    },
    adicionarEndereco: function (tipo, codigoBrasil) {

        var dados = null;
        dados = $(".endereco" + tipo + ":first").clone();
        dados.find(Sistemas.inputsForm)
                .not(Sistemas.inputsFormBotoes)
                .val("")
                .removeAttr("checked")
                .removeClass("error");
        dados.find('.campos-empresa').css('display', 'none');
        $(".add_endereco").remove();

        $(".endereco" + tipo + ":first").parent().append(dados);
        Sistemas.atualizarNomeCamposEnderecos(tipo, codigoBrasil);

        // $(".chzn-container").remove();
        //$('select').removeClass('chzn-select').removeClass('chzn-done').addClass('chzn-select');
        //$('.chzn-select').chosen({no_results_text: "Sem resultados"});
        Sistemas.removerMascaras();
        Sistemas.aplicarMascaras();
        //$(".date").datepicker("destroy");
        return false;
    },
    removerEndereco: function (botao, tipo, codigoBrasil) {
        if ($(".endereco" + tipo).length < 2) {
            window.scrollTo(0, 0);
            $.mobile.silentScroll(0);
            Sistemas.alert("Pelo menos um endereco deve ser informado");
        } else {
            $(botao).parent().parent().remove();
        }

        Sistemas.atualizarNomeCamposEnderecos(tipo, codigoBrasil);
        Sistemas.removerMascaras();
        Sistemas.aplicarMascaras();
        // $(".date").datepicker("destroy");
        return false;
    },
    atualizarNomeCamposEnderecos: function (tipo, codigoBrasil) {
// Quantidade de enderecos
        var contador = 0;
        $(".endereco" + tipo).each(function () {

            $(this).find(Sistemas.inputsForm).each(function () {

                if ($(this).attr("name")) {
                    var nomeCampo = $(this).attr("name").split("[");
                    if (nomeCampo[2]) {
                        nomeCampo[1] = nomeCampo[2];
                    }

                    if (nomeCampo[1]) {
                        nomeCampo = nomeCampo[1].replace("]", "");
                        var arrayCamposPessoasVinculo = new Array('id', 'juridica_id', 'cnpj', 'nome_empresa', 'classificacao_empresa_hidden', 'classificacao_empresa', 'divisao', 'departamento');
                        if ($.inArray(nomeCampo, arrayCamposPessoasVinculo) != -1) {
                            $(this).attr("key", contador);
                            $(this).attr("name", "pessoas_vinculo[" + contador + "][" + nomeCampo + "]");
                            $(this).attr("id", "pessoas_vinculo-" + nomeCampo + "_" + contador);
                            $(this).attr("tipo", tipo);
                        } else {
                            $(this).attr("key", contador);
                            $(this).attr("name", "endereco" + tipo + "[" + contador + "][" + nomeCampo + "]");
                            $(this).attr("id", "endereco-" + tipo + nomeCampo + "_" + contador);
                            $(this).attr("tipo", tipo);
                        }
                    }
                }
            });
            $(this).find('.campos-empresa').addClass('campos-empresa-' + contador);
            Sistemas.atualizaMascaraEndereco(tipo, contador, codigoBrasil);
            contador++;
        });
        var botaoAddEndereco = "";
        botaoAddEndereco += "<div class='local_rodape add_endereco_" + tipo + "'>";
        botaoAddEndereco += '<input class="btn" onclick="Sistemas.adicionarEndereco(\'' + tipo + '\',\'' + codigoBrasil + '\');" type="button" value="Adicionar outro endereco" />';
        botaoAddEndereco += "</div>";
        var botaoDelEndereco = "";
        botaoDelEndereco += "<div class='local_rodape del_endereco_" + tipo + "'>";
        botaoDelEndereco += '<input class="btn" onclick="Sistemas.removerEndereco(this, \'' + tipo + '\');" type="button" value="Remover o endereco acima" />';
        botaoDelEndereco += "</div>";
        $(".add_endereco_" + tipo + ", .del_endereco_" + tipo).remove();
        $(".endereco" + tipo + ":last").parent().append(botaoAddEndereco);
        $(".endereco" + tipo).append(botaoDelEndereco);
        $(".classificacaoEmpresa").focus(function () {
            var key = $(this).attr('key');
            Janelas.openDefault('Classificacao da Empresa', 'default/janelas/classificacao-empresa/', {divid: key, classficicacoes: $(this).val()}, 'janela-classificacao-empresa', '', '');
        });
    },
    atualizaMascaraEndereco: function (tipoCampo, iEndereco, codigoBrasil) {

        var cidadeInternacional = $("input[name='endereco" + tipoCampo + "[" + iEndereco + "][cidade_internacional]']");
        var cidadeNacional = $("select[name='endereco" + tipoCampo + "[" + iEndereco + "][municipio_id]']");
        var ufNacional = $("select[name='endereco" + tipoCampo + "[" + iEndereco + "][uf_id]']");
        var cep = $("input[name='endereco" + tipoCampo + "[" + iEndereco + "][cep]']");
        var telefone1 = $("input[name='endereco" + tipoCampo + "[" + iEndereco + "][fone1]']");
        var telefone2 = $("input[name='endereco" + tipoCampo + "[" + iEndereco + "][fone2]']");
        var celular = $("input[name='endereco" + tipoCampo + "[" + iEndereco + "][celular]']");
        var pais = $("select[name='endereco" + tipoCampo + "[" + iEndereco + "][pais_id]']");
        if (pais.val() != "" && pais.val() != codigoBrasil) {

            if (cidadeInternacional) {
                cidadeInternacional.parent().show();
                if (cidadeNacional) {
                    cidadeNacional.val("").parent().hide();
                }

                if (ufNacional) {
                    ufNacional.val("").parent().hide();
                }

            }

//removendo mascaras
            cep.unmask("99999-999");
            // telefone2.unmask("(99) 9999-9999?9");
            // telefone1.unmask("(99) 9999-9999?9");
            //celular.unmask("(99) 9999-9999?9");
            //removendo class
            cep.removeClass("cep");
            cep.removeClass("obrigratorio");
            telefone1.removeClass("fone");
            telefone2.removeClass("fone");
            celular.removeClass("fone");
            ufNacional.removeClass("obrigratorio");
            cidadeNacional.removeClass("obrigratorio");
            //adicionando class
            telefone1.addClass("numeros");
            telefone2.addClass("numeros");
            celular.addClass("numeros");
            cep.addClass("numeros");
            cidadeInternacional.addClass("obrigratorio");
            Sistemas.removerMascaras();
            Sistemas.aplicarMascaras();
        } else {

            if (cidadeInternacional) {
                cidadeInternacional.val("").parent().hide();
                if (cidadeNacional) {
                    cidadeNacional.parent().show();
                }
                if (ufNacional) {
                    ufNacional.parent().show();
                }

            }
//adicionando mascaras
            //  telefone1.mask("(99) 9999-9999?9");
            //  telefone2.mask("(99) 9999-9999?9");
            //celular.mask("(99) 9999-9999?9");
            cep.mask("99999-999");
            //removendo claass numeros
            telefone1.removeClass("numeros");
            telefone2.removeClass("numeros");
            celular.removeClass("numeros");
            cidadeInternacional.removeClass("obrigratorio");
            if (!cep.hasClass("cep")) {
                cep.addClass("cep");
                cep.addClass("obrigratorio");
                telefone1.addClass("fone");
                telefone2.addClass("fone");
                celular.addClass("fone");
                ufNacional.addClass("obrigratorio");
                cidadeNacional.addClass("obrigratorio");
            }
        }
    },
    /**
     * Na tela temos uma colecao de escolaridades, entao o nome dos campos devem estar no formato escolaridade[x][campo].
     * Para cada nova escolaridade inserido ou excluido, eh necessario atualizar os nomes dos campos.
     */
    atualizarNomeCamposEscolaridades: function () {
// Quantidade
        var contador = 0;
        $(".escolaridade").each(function () {

            $(this).find(Sistemas.inputsForm).each(function () {
                if ($(this).attr("name")) {
                    var nomeCampo = $(this).attr("name").split("[");
                    if (nomeCampo[2]) {
                        nomeCampo[1] = nomeCampo[2];
                    }

                    if (nomeCampo[1]) {
                        nomeCampo = nomeCampo[1].replace("]", "");
                        $(this).attr("name", "escolaridade[" + contador + "][" + nomeCampo + "]");
                        $(this).attr("id", "escolaridade-" + nomeCampo + "_" + contador);
                        $(this).attr("id", "escolaridade-" + nomeCampo + "_" + contador);
                        $(this).attr("contador", contador);
                    }
                }
            });
            $(this).find('.campos-empresa').addClass('campos-empresa-' + contador);
            $(this).find(".curso_outro").attr('id', 'curso_outro_' + contador);
            contador++;
        });
    },
    adicionarEscolaridade: function () {

        var dados = null;

        dados = $(".escolaridade:first").clone();
        dados.find(Sistemas.inputsForm)
                .not(Sistemas.inputsFormBotoes)
                .val("")
                .removeAttr("checked")
                .removeClass("error");

        dados.find('.campos-empresa').css('display', 'none');

        $(".escolaridade:first").parent().append(dados);

        $(".curso_id").val(1);
        $(".curso_outro").val('OUTROS');

        Sistemas.atualizarNomeCamposEscolaridades(true);
        contador = $(".escolaridade:last").find(Sistemas.inputsForm).not(Sistemas.inputsFormBotoes).attr('contador');

        $("#escolaridade_pessoa-curso_descricao_" + contador).attr("readonly", true);

        //   $(".chzn-container").remove();
        //   $('select').removeClass('chzn-select').removeClass('chzn-done').addClass('chzn-select');
        //   $('.chzn-select').chosen({no_results_text: "Sem resultados"});

        Sistemas.removerMascaras();
        Sistemas.aplicarMascaras();

        return false;
    },
    removerEscolaridade: function (botao) {
        if ($(".escolaridade").length < 2) {
            window.scrollTo(0, 0);
            $.mobile.silentScroll(0);
            Sistemas.alert("Pelo menos uma escolaridade deve ser informado.");
        } else {
            $(botao).parent().parent().parent().parent().remove();
        }

        contador = $(".escolaridade:last").find(Sistemas.inputsForm).not(Sistemas.inputsFormBotoes).attr('contador');
        Sistemas.atualizarNomeCamposEscolaridades(false);
        Sistemas.removerMascaras();
        Sistemas.aplicarMascaras();

        return false;
    },
    getTipoGraduacaoFaculdade: function (tipo, combo, selecionado) {
        $.getJSON(baseUrl + "/utilidades/get-tipo-graduacao-faculdades", {tipo_graduacao_id: tipo}, function (data) {
            var count = data.length;
            combo.html("");

            for (var i = 0; i < count; i++) {

                sel = (undefined != selecionado && selecionado == data[i].id) ? " selected='selected' " : "";

                combo.append(
                        '<option '
                        + sel
                        + 'value="' + data[i].id + '">'
                        + data[i].descricao
                        + '</option>');
            }
        });
    },
    validaData: function (data) {
        var regex = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/;
        if (regex.test(data)) {
            return true;
        } else {
            return false;
        }
    }
};
$(document).ready(function () {

    if (navigator.appVersion.indexOf("MSIE") != -1) {

        var versaoNavegador = navigator.appVersion.split(";")[1].replace(/^\s+|\s+$/g, "");
        if (navigator.appName == 'Microsoft Internet Explorer' && versaoNavegador != 'MSIE 8.0') {
            var forms = document.getElementsByTagName('form');
            for (var i = 0; i < forms.length; i++) {
                forms[i].addEventListener('invalid', function (e) {
                    e.preventDefault();
                    //Possibly implement your own here.
                }, true);
            }
        }
    }
});

var NUM_DIGITOS_CPF = 11;
var NUM_DIGITOS_CNPJ = 14;
var NUM_DGT_CNPJ_BASE = 8;
function unformatNumber(pNum)
{
    return String(pNum).replace(/\D/g, "").replace(/^0+/, "");
}

function unmaskNumber(pNum)
{
    return String(pNum).replace(/\D/g, "");
}

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
