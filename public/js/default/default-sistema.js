Sistema = {
    intervalBarra: null,
    /**
     * Inputs de um form
     */
    inputsForm: "input,select,textarea",
    /**
     * Botoes de um form
     */
    inputsFormBotoes: ":submit,:button,:reset",
    preencherNomeCracha: function (val, objectNomeCracha) {
        var nome = val;
        var nomeArray = nome.split(' ');
        var count = (parseInt(nomeArray.length) - 1);
        var nomeCracha = nomeArray[0] + " " + nomeArray[count];
        objectNomeCracha.val(nomeCracha);
    },
    atualizaSistema: function () {
        $.ajax({
            type: 'POST',
            url: '/utilidades/atualiza-sistema',
            success: function (data) {
            },
            dataType: 'json'
        });
    },
    GetColumnSize: function (percent) {
        screen_res = (1350 / 4) * 0.95; // 700 is the width of table;
        col = parseInt((percent * (screen_res / 100)));
        if (percent != 100) {
            // alert('foo= ' + col-18);
            return col - 18;
        } else {
            // alert(col);
            return col;
        }
    },
    formatacaoPais: function (nome, chave, codigoDoBrasil) {
        if ($("#" + nome + "-" + chave + "pais_id").val() != codigoDoBrasil && $("#" + nome + "-" + chave + "pais_id").val() != "") {
            $("#" + chave + "uf, #" + chave + "municipio, #" + chave + "bairro, #" + chave + "numero, #" + chave + "complemento, #" + chave + "tipo").hide();
            $("#" + nome + "-" + chave + "uf_id, #" + nome + "-" + chave + "municipio_id, #" + nome + "-" + chave + "bairro, #" + nome + "-" + chave + "complemento, #" + nome + "-" + chave + "numero, #" + nome + "-" + chave + "tipo_logradouro_id").val("");
            $("#" + chave + "cidade_internacional").show();
            Sistema.removerMascaras();
            $("#" + nome + "-" + chave + "cep").removeClass("cep");
            $("#" + nome + "-" + chave + "fone1,#" + nome + "-" + chave + "fax,#" + nome + "-" + chave + "celular").removeClass("fone");
            $("#" + nome + "-" + chave + "fone1,#" + nome + "-" + chave + "fax,#" + nome + "-" + chave + "celular").addClass("numeros");
            Sistema.aplicarMascaras();
        } else if ($("#" + nome + "-" + chave + "pais_id").val() == codigoDoBrasil) {
            $("#" + chave + "cidade_internacional").val("").hide();
            $("#" + chave + "uf, #" + chave + "municipio, #" + chave + "bairro, #" + chave + "numero, #" + chave + "complemento, #" + chave + "tipo").show();
            $("input[name='" + nome + "[" + chave + "ddi1]']").val("55");
            $("input[name='" + nome + "[" + chave + "ddi_celular]']").val("55");
            $("input[name='" + nome + "[" + chave + "ddi_fax]']").val("55");
            if (!$("#" + nome + "-" + chave + "cep").hasClass("cep")) {
                $("#" + nome + "-" + chave + "cep").addClass("cep");
                $("#" + nome + "-" + chave + "fone1,#" + nome + "-" + chave + "fax,#" + nome + "-" + chave + "celular").addClass("fone");
                $("#" + nome + "-" + chave + "fone1,#" + nome + "-" + chave + "fax,#" + nome + "-" + chave + "celular").removeClass("numeros");
            }

            Sistema.removerMascaras();
            Sistema.aplicarMascaras();
        } else {
            $("#" + chave + "uf, #" + chave + "municipio, #" + chave + "bairro, #" + chave + "numero, #" + chave + "complemento, #" + chave + "tipo").hide();
            $("#" + chave + "cidade_internacional").hide();
            $("#" + nome + "-" + chave + "uf_id, #" + nome + "-" + chave + "municipio_id, #" + nome + "-" + chave + "bairro, #" + nome + "-" + chave + "complemento, #" + nome + "-" + chave + "numero, #" + nome + "-" + chave + "tipo_logradouro_id").val("");
            $("#" + chave + "cidade_internacional").val("").hide();
            Sistema.removerMascaras();
            Sistema.aplicarMascaras();
        }
    },
    buscarEndereco: function (cep, key, codigoBrasil, tipo) {

        if (!tipo)
            tipo = "endereco-";
        $('#' + tipo + 'logradouro' + key).val('Carregando endereco...');
        $('#' + tipo + 'bairro' + key).val('Carregando bairro...');
        $('#' + tipo + 'pais_id' + key).val(codigoBrasil);
        $.post(
                baseUrl + "/utilidades/cep",
                {
                    cep: cep
                },
        function (data) {
            if (parseInt(data.resultado) == 1) {
                $('#' + tipo + 'tipo_logradouro_id' + key).val(data.tipo_logradouro);
                $('#' + tipo + 'logradouro' + key).val(data.logradouro);
                $('#' + tipo + 'bairro' + key).val(data.bairro);
                $('#' + tipo + 'pais_id' + key).val(codigoBrasil);
                $('#' + tipo + 'uf_id' + key).val(data.uf_id);
                Sistema.getMunicipios(data.uf, $('#' + tipo + 'municipio_id' + key), data.municipio_id);
                $('#' + tipo + 'municipio_id' + key).val(data.municipio_id);
            } else {
                $('#' + tipo + 'tipo_logradouro_id' + key).val('');
                $('#' + tipo + 'logradouro' + key).val('');
                $('#' + tipo + 'bairro' + key).val('');
                $('#' + tipo + 'pais_id' + key).val(codigoBrasil);
                $('#' + tipo + 'uf_id' + key).val('');
                $('#' + tipo + 'municipio_id' + key).val('');
            }
        },
                "json"
                );
        return false;
    },
    atualizarEmail: function (pessoaId, title) {
        Janelas.atualizarEmailAvaliador(pessoaId, title);
    },
    popUp: function (url, janela, w, h) {
        var winl = (screen.width - w) / 2;
        var wint = (screen.height - h) / 2

        window.open(url, janela, "location=1,status=1,scrollbars=1, top=" + wint + ",left=" + winl + ", width=" + w + ", height=" + h);
        return false;
    },
    usuario: {
        mudarStatus: function (id) {
            $.post(
                    baseUrl + "/usuarios/status", {
                        id: id
                    },
            function (res) {
                var img = baseUrl + "/images/icons/";
                img += (res.status == 'S') ? 'tick.png' : 'tick-red.png';
                $("#status_user_" + id).attr("src", img);
            },
                    "json");
        }
    },
    atualizaTamanhoElementos: function (valorAltura) {
        var tamanhoEsquerda;
        var tamanhoBodyFrame;
        // Se o acesso for de fora do iframe
        if ($("#conteudo_pagina iframe").filter(':visible').is('iframe')) {
            tamanhoEsquerda = $('#esquerda').css('height').replace('px', '');
            tamanhoEsquerda = parseInt(tamanhoEsquerda);
            tamanhoBodyFrame = $("#conteudo_pagina iframe").filter(':visible').contents().find('body').css('height').replace('px', '');
            tamanhoBodyFrame = parseInt(tamanhoBodyFrame);
            if (tamanhoBodyFrame > tamanhoEsquerda) {
                $('#esquerda').css('height', tamanhoBodyFrame + 'px');
            } else if (tamanhoEsquerda > tamanhoBodyFrame) {
                $('#esquerda').css('height', 'auto');
            }

//            $("#conteudo_pagina iframe").filter(':visible').css('height', tamanhoBodyFrame + 'px');
            $("#conteudo_pagina iframe").filter(':visible').css('min-height', '410px');
            // Senao o acesso sera de dentro
        } else {

            var valorComplemento = 70;
            if (window.parent.$('#esquerda').is("div")) {
                tamanhoEsquerda = window.parent.$('#esquerda').css('height').replace('px', '');
            } else if ($('#esquerda').is("div")) {
                tamanhoEsquerda = $('#esquerda').css('height').replace('px', '');
            } else {
                tamanhoEsquerda = 400;
            }

            tamanhoEsquerda = parseInt(tamanhoEsquerda) + valorComplemento;
            if ($('.conteudo-clear').is('div')) {
                tamanhoBodyFrame = $('.conteudo-clear').css('height').replace('px', '');
            } else {
                tamanhoBodyFrame = 0;
            }

            if (tamanhoBodyFrame > tamanhoEsquerda) {
                tamanhoBodyFrame = parseInt(tamanhoBodyFrame) + valorComplemento;
            } else if (tamanhoEsquerda > tamanhoBodyFrame) {
                tamanhoBodyFrame = parseInt(tamanhoEsquerda) + valorComplemento;
            }

            if (valorAltura != undefined && valorAltura > 300)
                tamanhoBodyFrame = parseInt(valorAltura);
            window.parent.$('#esquerda').css('height', tamanhoBodyFrame + 'px');
            window.parent.$("#conteudo_pagina iframe").filter(':visible').css('height', tamanhoBodyFrame + 'px');
        }
    },
    barraEsquerda: {
        cookie: function () {
            if ($.cookie("barra-esquerda") == "hide") {
                Sistema.barraEsquerda.ocultar();
            } else {
                Sistema.barraEsquerda.exibir();
            }
        },
        acaoBotao: function () {
            if ($('.conteudo_esquerda').css('display') == 'none') {
                Sistema.barraEsquerda.exibir();
            } else {
                Sistema.barraEsquerda.ocultar();
            }
        },
        exibir: function () {
            $('#esquerda').css('width', '140px');
            $('#direita').css("margin-left", "153px");
            $('.conteudo_esquerda').show();
            Sistema.atualizaTamanhoElementos();
            $.cookie("barra-esquerda", "show");
        },
        ocultar: function () {
            Sistema.atualizaTamanhoElementos();
            $('.conteudo_esquerda').hide();
            $('#esquerda').css('width', '0px');
            $('#direita').css("margin-left", "13px");
            $.cookie("barra-esquerda", "hide");
        }
    },
    readURL: function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview-image')
                        .attr('src', e.target.result)
                        .width(200)
                        .height(200);
            };
            reader.readAsDataURL(input.files[0]);
        }
    },
    mudaStatus: function (reg, controller, grid)
    {
        var g = (grid == undefined) ? 'flexgrid-list' : grid;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/' + controller + '/muda-status',
            success: function (r) {
                if (!r.res) {
                    $("#" + g).reload();
                } else {
                    Sistema.alert(mensagem, callback);
                }
            }
        });
    },
    /**
     * Verifica se um email e valido
     */
    validaEmail: function (email)
    {
        if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)))
            return false;
        return true;
    },
    /**
     * Remove os acentos de uma string
     */
    removeAcentos: function (texto)
    {
        texto = texto.toString().replace("+", '');
        var com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ´`^¨~@';
        var sem_acento = 'aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC';
        var nova = '';
        for (i = 0; i < texto.length; i++) {
            if (com_acento.search(texto.substr(i, 1)) >= 0) {
                nova += sem_acento.substr(com_acento.search(texto.substr(i, 1)), 1);
            } else {
                nova += texto.substr(i, 1);
            }
        }
        return nova;
    },
    /**
     * Cria um frame, onde a pagina sera alocada, para que seja possivel a
     * navegacao.
     */
    resizeIframe: function (obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    },
    criarFrameLink: function (link, titulo, descricao) {

        var id = Sistema.gerarIdLink(link);
        var d = window.parent.document || window.document;
        $(".frame_conteudo", d).hide();
        $(".seleciona_recentes", d).removeClass("aba_ativa").addClass("aba_inativa");
        if (!$("#" + id, d).attr('id')) {
            iframe = "<iframe scrolling='auto' width='100%' onload='Sistema.resizeIframe(this);' name='" + id + "' frameborder='0' ";
            iframe += "vspace='0' hspace='0' marginwidth='0' marginheight='0' ";
            iframe += "scrolling='no' noresize class='frame_conteudo' ";
            iframe += "id='" + id + "' src='" + link + "'></iframe>";
            $("#conteudo_pagina", d).append(iframe);
            Sistema.criarAbaLink(link, titulo, descricao);
        } else {
            abaId = Sistema.gerarIdAba(Sistema.gerarIdLink(link));
            $("#" + abaId, d).removeClass("aba_inativa").addClass("aba_ativa");
        }

        try {
            $("#" + id, d).show();
            $("#" + id, d).iframeAutoHeight();
            Sistema.atualizaTamanhoElementos();
        } catch (e) {
            Sistema.atualizaTamanhoElementos();
        }
    },
    /**
     * Cria uma aba na lateral esquerda, para o link clicado
     */

    criarAbaLink: function (link, titulo, descricao) {
        var d = window.parent.document || window.document;
        var abaId = Sistema.gerarIdAba(Sistema.gerarIdLink(link));
        var aba = '<div class="seleciona_recentes" id="' + abaId + '">';
        aba += '<a title="' + Itarget.lang.botoes.fechar + '" href="javascript:;" onclick="Sistema.modificar_mensagem_frame(\'#' + Sistema.gerarIdLink(link) + '\',\'' + link + '\');return false;">';
        aba += '<i class="fa fa-times" style="color:#333;float:right; margin-top:-5px;"></i>';
        aba += '</a>';
        aba += '<p onclick="Sistema.criarFrameLink(\'' + link + '\');" style="cursor: pointer"><label style="color:#265cac">' + titulo + '</label><br />' + descricao + '</p>';
        aba += '</div>';
        $("#abas_recentes", d).append(aba);
    },
    /**
     * Remove o frame que foi criado para o link
     */
    removerFrameLink: function (link) {
        var d = window.parent.document || window.document;
        id = Sistema.gerarIdLink(link);
        $("#" + id, d).remove();
        Sistema.removerAbaLink(link);
        if ($('#abas_recentes').children().length > 0) {
            $('#graficos').hide();
        } else {
            $('#graficos').show();
        }

    },
    /**
     * Remove a aba que foi criada para o link
     */
    removerAbaLink: function (link) {
        var d = window.parent.document || window.document;
        abaId = Sistema.gerarIdAba(Sistema.gerarIdLink(link));
        $("#" + abaId, d).remove();
        $(".seleciona_recentes .bola :first", d).click();
        if ($('#abas_recentes').children().length > 0) {
            $('#graficos').hide();
        } else {
            $('#graficos').show();
        }

    },
    /**
     * Gera um ID unico para um link
     */
    gerarIdLink: function (link) {
        return link.replace(/\//g, '_');
    },
    /**
     * Gera um ID unico para uma aba
     */
    gerarIdAba: function (id) {
        return "aba_" + id;
    },
    /**
     * Cria um link rapido, na lateral esquerda
     */
    criarLinkRapido: function (link) {

        titulo = "";
        descricao = "";
        alvo = $("a[href='" + link + "']:first", window.parent.document);
        Sistema.carregandoLinkRapido();
        if (alvo.attr("title")) {
            titulo = alvo.attr("title");
            descricao = alvo.attr("descricao");
        }

        $.post(
                baseUrl + "/usuarios-preferencias/salvar-link-rapido",
                {
                    link: link,
                    titulo: titulo,
                    descricao: descricao
                },
        function (links) {
            Sistema.montarLinkRapido(links);
        },
                "json");
    },
    montarLinkRapido: function (links) {

        var qtdLinks = 0;
        var abas = "";
        if (links) {
            qtdLinks = links.length;
        }

        for (var i = 0; i < qtdLinks; i++) {

            id = links[i].id;
            link = links[i].link;
            titulo = links[i].titulo;
            descricao = links[i].descricao;
            abas += '<div class="seleciona_liks aba_ativa">';
            abas += '<a title="' + Itarget.lang.botoes.fechar + '" href="javascript:;" onclick="Sistema.excluirLinkRapido(' + id + ');">';
            abas += '<i class="fa fa-times" style="color:#333;float:right; margin-top:-5px;"></i>';
            abas += '</a>';
            abas += '<p onclick="Sistema.criarFrameLink(\'' + link + '\', \'' + titulo + '\', \'' + descricao + '\');" style="cursor: pointer"><label style="color:#265cac">' + titulo + '</label><br />' + descricao + '</p>';
            abas += '</div>';
        }

        // A chamada vem de um frame
        $("#abas_links_rapidos", window.parent.document).html(abas);
    },
    excluirLinkRapido: function (id) {
        Sistema.carregandoLinkRapido();
        $.post(
                baseUrl + "/usuarios-preferencias/excluir-link-rapido",
                {
                    id: id
                },
        function (links) {
            Sistema.montarLinkRapido(links);
        },
                "json");
    },
    carregandoLinkRapido: function () {
        img = "<div align='center'><img align='center' src='" + baseUrl + "/images/icons/arrow-circle-double-135.png' border='0' /></div>";
        $("#abas_links_rapidos", window.parent.document).html(img);
    },
    sizeText: function (type) {

        var diff = 2;
        var size = 0;
        $(".conteudo-clear").find('input,select,p,span,div,textarea,a,label').each(function () {

            size = parseInt($(this).css("font-size").replace("px", ""));
            if (type == 'more' && size < 20) {
                size += diff;
                if ($(this).is('select'))
                    $(this).css('height', 'auto');
            } else if (type == 'less' && size > 12) {
                size -= diff;
            }

            if ($(this).is('select') && size <= 12)
                $(this).css('height', '23px');
            $(this).css("font-size", size + "px");
        });
    },
    limparForm: function (form) {
        $("input,textarea,select", form)
                .not(":button, :submit, :reset, :hidden")
                .val("")
                .removeAttr("checked")
                .removeAttr("selected");
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
                $.getJSON(baseUrl + "/utilidades/get-municipios", {
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

                });
            } else {
                combo.empty();
                combo.append('<option value="0">' + Itarget.lang.outros.selecioneEstado + '</option>');
            }
        } catch (e) {
        }
    },
    /**
     * Bloqueia todos os campos do form para edicao
     * @param form - Instancia do form, obtida atraves do jQuery
     * @return void
     */
    bloquearForm: function (form) {
        // bloqueando os campos, exceto os botoes
        Sistema.aplicarMascaras();
        form.find(Sistema.inputsForm).not(Sistema.inputsFormBotoes).addClass("disabled").attr("disabled", "disabled");
    },
    /**
     * Bloqueia todos os campos do form para edicao
     * @param form - Instancia do form, obtida atraves do jQuery
     * @param objetos - inputs que seram desabilitados (input, select, textarea)
     * @return void
     */
    bloquearToForm: function (form, objects) {
        // bloqueando os campos, exceto os botoes
        Sistema.aplicarMascaras();
        if (objects != undefined && objects != "") {
            Sistema.inputsForm = objects;
        }

        form.find(Sistema.inputsForm).not(Sistema.inputsFormBotoes).addClass("disabled").attr("disabled", "disabled");
    },
    /**
     * Desbloqueia todos os campos do form para edicao
     * @param form - Instancia do form, obtida atraves do jQuery
     * @return void
     */
    desbloquearForm: function (form) {
        form.find(Sistema.inputsForm).not(Sistema.inputsFormBotoes).removeAttr("disabled").removeClass("disabled");
        Sistema.removerMascaras();
        Sistema.aplicarMascaras();
    },
    aplicarMascaras: function () {
        $(".cpf").mask("999.999.999-99");
        $(".cnpj").mask("99.999.999/9999-99");
        $(".ano").mask("9999");
        $(".cep").mask("99999-999");
        $(".ddi").mask("99");
        $(".date,.datemask").mask("99/99/9999");
        $(".hora").mask("99:99");
        $(".email").css("text-transform", "lowercase");
        $(".numeros").bind("keyup blur focus", function (e) {
            if ($(this).hasClass("fone")) {
                return false;
            }
            Expressoes.somenteNumeros($(this));
        });
        $(".fone").mask("(99) 9999-9999?9");
        $('.fone').keyup(function () {
            var phone, element;
            element = $(this);
            phone = element.val().replace(/\D/g, '');
            if (phone.length > 10) {
                element.unmask().mask("(99) 99999-999?9");
            } else if (phone.length > 9) {
                element.unmask().mask("(99) 9999-9999?9");
            }
        }).trigger('keyup');
        $(".date").datepicker({
            showOn: "button",
            buttonImage: baseUrl + "/images/icons/calendar.png",
            changeMonth: true,
            changeYear: true,
            buttonImageOnly: true
        });
        $('.price').priceFormat({
            prefix: '' + Itarget.lang.price + ' ',
            centsSeparator: ',',
            thousandsSeparator: '.'
        });
        $('.percent').priceFormat({
            prefix: '',
            centsSeparator: ',',
            thousandsSeparator: ''
        });
    },
    removerMascaras: function () {
        //$(".date, .cpf, .ano, .cnpj, .cep, .ddi, .fone, .hora").unmask();
        $(".date, .datemask").unmask("99/99/9999");
        $(".cpf").unmask("999.999.999-99");
        $(".ano").unmask("9999");
        $(".cnpj").unmask("99.999.999/9999-99");
        $(".cep").unmask("99999-999");
        $(".ddi").unmask("99");
        $(".fone").unmask("(99) 9999-9999?9");
        $(".hora").unmask("99:99");
        $(".numeros").bind("keyup blur focus", function (e) {
            return false;
        });
    },
    alert: function (msg, callback) {
        bootbox.alert(msg, callback);
    },
    confirm: function (mensagem, callback) {
        bootbox.confirm(mensagem, callback);
    },
    deletar: function (mensagem, params, callback) {

        bootbox.confirm(mensagem, function (result) {

            if (result) {

                var dados = [];
                params.linhas.each(function (i, valor) {
                    dados.push($(params.linhas[i]).text());
                });
                $.ajax({
                    type: 'POST',
                    data: {linhas: dados},
                    dataType: 'json',
                    url: params.url,
                    success: function (data) {
                        if (data.status) {
                            bootbox.alert(data.msg, callback);
                            if (params.flexgrid !== undefined) {
                                $(params.flexgrid).flexReload();
                            } else {
                                $("#flexgrid-list").flexReload();
                            }
                        } else {
                            bootbox.alert(data.msg);
                        }
                    }
                });
            }
        });
    },
    getMicrotime: function (get_as_float) {
        // version: 1103.1210
        // discuss at: http://phpjs.org/functions/microtime    // +   original by: Paulo Freitas
        var now = new Date().getTime() / 1000;
        var s = parseInt(now, 10);
        return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
    },
    /**
     * Retorna um inteiro aleatorio, entre min e max
     * @param min - Numero minimo
     * @param max - Numero maximo
     * @return int
     */
    getRandomInt: function (min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    /**
     * http://www.qodo.co.uk/blog/javascript-checking-if-a-date-is-valid
     * Checks a string to see if it in a valid date format
     * of (D)D/(M)M/(YY)YY and returns true/false
     */
    isValidDate: function (s) {
        // format D(D)/M(M)/(YY)YY
        var dateFormat = /^\d{1,4}[\.|\/|-]\d{1,2}[\.|\/|-]\d{1,4}$/;
        if (dateFormat.test(s)) {
            // remove any leading zeros from date values
            s = s.replace(/0*(\d*)/gi, "$1");
            var dateArray = s.split(/[\.|\/|-]/);
            // correct month value
            dateArray[1] = dateArray[1] - 1;
            // correct year value
            if (dateArray[2].length < 4) {
                // correct year value
                dateArray[2] = (parseInt(dateArray[2]) < 50) ? 2000 + parseInt(dateArray[2]) : 1900 + parseInt(dateArray[2]);
            }

            var testDate = new Date(dateArray[2], dateArray[1], dateArray[0]);
            if (testDate.getDate() != dateArray[0] || testDate.getMonth() != dateArray[1] || testDate.getFullYear() != dateArray[2]) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    },
    /**
     * Verifica se uma data eh maior que hoje
     */
    dataMaiorOuIgualAHoje: function (data) {
        data = data.split("/");
        return (new Date(data[2], data[1], data[0]) >= new Date());
    },
    /**
     * Excluir um arquivo retorno de um cliente
     *
     * @param tipo Tipo do arquivo(baixado, naoBaixado, parcialmenteBaixado)
     * @param arquivo Nome do arquivo
     * @param controleLayoutId Id do controle layout
     * @param callback Callback a ser chamado
     * @return void
     */
    excluirArquivoRetorno: function (tipo, arquivo, controleLayoutId, callback) {

        Sistema.confirm(Itarget.lang.outros.confirmacaoExclusaoArquivo, function (r) {

            if (r) {
                $.post(
                        baseUrl + "/troca-arquivos/excluir-arquivo",
                        {
                            tipo: tipo,
                            arquivo: arquivo,
                            controleLayoutId: controleLayoutId
                        },
                function (r) {
                    Sistema.alert(r.msg, function () {
                        if (undefined != callback) {
                            callback();
                        }
                    });
                }, "json");
            }

        });
    },
    /**
     * Ler um arquivo retorno de um cliente
     *
     * @param tipo Tipo do arquivo(baixado, naoBaixado, parcialmenteBaixado)
     * @param arquivo Nome do arquivo
     * @param controleLayoutId Id do controle layout
     * @param callback Callback executado apos a leitura do arquivo
     * @return void
     */
    lerArquivoRetorno: function (tipo, arquivo, controleLayoutId, callback) {

        $.post(
                baseUrl + "/troca-arquivos/ler-arquivo",
                {
                    tipo: tipo,
                    arquivo: arquivo,
                    controleLayoutId: controleLayoutId
                },
        function (r) {
            callback(r);
        }, "json");
    },
    /**
     * Ler um arquivo retorno de um cliente
     *
     * @param tipo Tipo do arquivo(baixado, naoBaixado, parcialmenteBaixado)
     * @param arquivo Nome do arquivo
     * @param controleLayoutId Id do controle layout
     * @return void
     */
    downloadArquivoRetorno: function (tipo, arquivo, controleLayoutId) {
        var t = String(Sistema.getMicrotime(true));
        t = t.split(".")[0];
        var urlDownload = baseUrl + "/troca-arquivos/download-arquivo?tipo=" + tipo + "&arquivo=" + arquivo + "&controleLayoutId=" + controleLayoutId;
        $("body").append('<iframe id="' + t + '" src="" style="display:none; visibility:hidden;"></iframe>');
        $("#" + t).attr("src", urlDownload);
    },
    /**
     * Lista os cheques a serem compensados
     * @param params Parametros para a busca (pode-se utilizar form.serialize() ou {obj: valor})
     * @param callback Callback executado apos a obtencao do resultado
     * @return void
     */
    listarChequesACompensar: function (params, callback) {

        $.post(baseUrl + "/default/janelas/listar-cheques-a-compensar",
                params,
                function (data) {
                    callback(data);
                }, "json");
    },
    /**
     * Compensa um cheque
     * @param id Id do cheque
     * @param dataCompensacao Id do cheque
     * @param callback Callback executado apos a obtencao do resultado
     * @return void
     */
    compensarCheque: function (id, dataCompensacao, callback) {

        $.post(baseUrl + "/default/janelas/compensar-cheque",
                {
                    chequeId: id,
                    dataCompensacao: dataCompensacao
                },
        function (data) {
            callback(data);
        }, "json");
    },
    /**
     * Compensa um cheque
     * @param contaPagarId Id do cheque
     * @param tipoFilha Tipo de exibicao(rateio ou imposto)
     * @param callback Callback executado apos a obtencao do resultado
     * @return void
     */
    listarImpostosRetencoesContaPagar: function (contaPagarId, tipoFilha, callback) {

        $.post(baseUrl + "/default/janelas/listar-impostos-retencoes-conta-pagar",
                {
                    contaPagarId: contaPagarId,
                    tipoFilha: tipoFilha
                },
        function (data) {
            callback(data);
        }, "json");
    },
    /**
     * Compensa um cheque
     * @param pessoaJuridicaId Id da pessoa
     * @param callback Callback executado apos a obtencao do resultado
     * @return void
     */
    listarContasPessoaJuridica: function (pessoaJuridicaId, callback) {

        $.post(baseUrl + "/default/janelas/listar-contas-pessoa-juridica",
                {
                    pessoaJuridicaId: pessoaJuridicaId
                },
        function (data) {
            callback(data);
        }, "json");
    },
    /**
     * Exclui uma conta de pessoa juridica
     * @param pessoaJuridicaId Id da pessoa
     * @param contaBancariaId Id da conta bancaria
     * @param callback callback a ser chamado
     * @return void
     */
    excluirContaPessoaJuridica: function (pessoaJuridicaId, contaBancariaId, callback) {

        $.post(baseUrl + "/default/janelas/excluir-conta-pessoa-juridica",
                {
                    pessoaJuridicaId: pessoaJuridicaId,
                    contaBancariaId: contaBancariaId
                },
        function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    cep: function (cep, callback) {

        $.post(baseUrl + "/utilidades/cep", {
            cep: cep
        },
        function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    getCategoriasSubTrabalho: function (categoriaTrabalhoId, callback) {
        $.post(baseUrl + "/utilidades/categorias-sub-trabalho", {
            categoriaTrabalhoId: categoriaTrabalhoId
        },
        function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    acessoEA: function (cliente, hash, pessoaId, redirect) {

        if ($("#form-acesso-associado").attr("id")) {
            $("#form-acesso-associado").remove();
        }

        var form = '<form target="_blank" action="http://icase.' + cliente + '/associado/auth/login" method="post" id="form-acesso-associado">'
                + '<input type="hidden" name="acesso[hash]" value="' + hash + '" />'
                + '<input type="hidden" name="acesso[pessoaId]" value="' + pessoaId + '" />'
                + '<input type="hidden" name="acesso[redirect]" value="' + redirect + '" />'
                + '</form>';
        $("body").append(form);
        $("#form-acesso-associado").submit();
    },
    acessoSite: function (cliente, login, senha, redirect) {

        if ($("#form-acesso-site").attr("id")) {
            $("#form-acesso-site").remove();
        }

        var form = '<form target="_blank" action="' + cliente + '" method="post" id="form-acesso-site">'
                + '<input type="hidden" name="login[senha]" value="' + senha + '" />'
                + '<input type="hidden" name="login[login]" value="' + login + '" />'
                + '<input type="hidden" name="login[redirect]" value="' + redirect + '" />'
                + '<input type="hidden" name="login[senha_crip]" value="1" />'
                + '</form>';
        $("body").append(form);
        $("#form-acesso-site").submit();
    },
    acessoEC: function (cliente, centroCusto, hash, pessoaId, redirect) {

        if ($("#form-acesso-ec").attr("id")) {
            $("#form-acesso-ec").remove();
        }

        link = 'http://icongresso.' + cliente + '.itarget.com.br/inscricao/auth/login/centro-custo/' + centroCusto;

        var form = '<form target="_blank" action="' + link + '"  method="post" id="form-acesso-ec">'
                + '<input type="hidden" name="acesso[hash]" value="' + hash + '" />'
                + '<input type="hidden" name="acesso[pessoaId]" value="' + pessoaId + '" />'
                + '<input type="hidden" name="acesso[redirect]" value="' + redirect + '" />'
                + '</form>';
        $("body").append(form);
        $("#form-acesso-ec").submit();
    },
    acessoECsistemas: function (cliente, centroCusto, hash, pessoaId, redirect, submodulo) {

        if ($("#form-acesso-ec").attr("id")) {
            $("#form-acesso-ec").remove();
        }

        if (!submodulo) {
            submodulo = 'evento';
        }

        var form = '<form target="_blank" action="http://icongresso.' + cliente + '.itarget.com.br/' + submodulo + '/' + centroCusto + '/auth/login" method="post" id="form-acesso-ec">'
                + '<input type="hidden" name="acesso[hash]" value="' + hash + '" />'
                + '<input type="hidden" name="acesso[pessoaId]" value="' + pessoaId + '" />'
                + '<input type="hidden" name="acesso[redirect]" value="' + redirect + '" />'
                + '<input type="hidden" name="acesso[area]" value="admin" />'
                + '</form>';
        $("body").append(form);
        $("#form-acesso-ec").submit();
    },
    openBoxIframe: function (src, divId) {
        var iframe = '<iframe src="' + src + '" id="frame_box" width="99.9%" height="99.9%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" title="Inscrever"></iframe>';
        $("#" + divId).dialog("open");
        $("#" + divId).html(iframe);
        return false;
    },
    openBox: function (content, divId) {
        $("#" + divId).dialog("open");
        $("#" + divId).html(content);
        return false;
    },
    // Função que gera o link para o download da tabela no formato para excel
    linkExcel: function (tabelaId, nomeArquivo, nomeLink) {
        document.write('<span class="imgLinkExcelAguarde"><img src="/images/default/loading.gif" border="0" alt="..." title="..." />&nbsp;</span>');
        document.write('<img class="imgLinkExcel" src="/images/icons/report-excel.png" border="0" alt="Excel" title="Excel" onclick="Sistema.tabela.paraExcel(\'' + tabelaId + '\', \'' + nomeArquivo + '\')" style="cursor: pointer; display: none" />');
        // Garantir que a tabela foi totalmente carregada
        jQuery(document).ready(function () {
            jQuery(".imgLinkExcelAguarde").hide();
            jQuery(".imgLinkExcel").show();
        });
    },
    // Função que gera o link para o download da tabela no formato para excel com template
    linkExcelComTemplate: function (tabelaId, nomeArquivo) {
        document.write('<span class="imgLinkExcelAguarde"><img src="/images/default/loading.gif" border="0" alt="..." title="..." />&nbsp;</span>');
        document.write('<img class="imgLinkExcel" src="/images/icons/report-excel.png" border="0" alt="Excel" title="Excel" onclick="Sistema.tabela.paraExcelComTemplate(\'' + tabelaId + '\', \'' + nomeArquivo + '\', \'' + titulo + '\')" style="cursor: pointer; display: none" />');
        // Garantir que a tabela foi totalmente carregada
        jQuery(document).ready(function () {
            jQuery(".imgLinkExcelAguarde").hide();
            jQuery(".imgLinkExcel").show();
        });
    },
    tabela: {
        paraExcel: function (tabelaId, nomeArquivo) {

            if (!nomeArquivo)
                nomeArquivo = 'relatorio';
            tabela = jQuery("#" + tabelaId).html();
            console.log(tabela);
            tabela = tabela.replace(/'/g, '`');
            nome = 'excel_' + Sistema.getRandomInt(1, 999);
            form = '<form name="' + nome + '" action="/default/export/excel" method="post" target="_blank">';
            form += '<input type="hidden" name="tabela" value=\'' + tabela + '\' />';
            form += '<input type="hidden" name="nomeArquivo" value="' + nomeArquivo + '" />';
            form += '</form>';
            jQuery('body').append(form);
            jQuery("form[name='" + nome + "']").submit();
            jQuery("form[name='" + nome + "']").remove();
        },
        paraExcelComTable: function (table, nomeArquivo) {

            if (!nomeArquivo)
                nomeArquivo = 'relatorio';
            tabela = table;
            console.log(tabela);
            tabela = tabela.replace(/'/g, '`');
            nome = 'excel_' + Sistema.getRandomInt(1, 999);
            form = '<form name="' + nome + '" action="/default/export/excel" method="post" target="_blank">';
            form += '<input type="hidden" name="tabela" value=\'' + tabela + '\' />';
            form += '<input type="hidden" name="nomeArquivo" value="' + nomeArquivo + '" />';
            form += '</form>';
            jQuery('body').append(form);
            jQuery("form[name='" + nome + "']").submit();
            jQuery("form[name='" + nome + "']").remove();
        },
        paraExcelComTemplate: function (tabelaId, nomeArquivo, titulo) {

            if (!nomeArquivo)
                nomeArquivo = 'relatorio';
            tabela = jQuery("#" + tabelaId).html();
            tabela = tabela.replace(/'/g, '`');
            nome = 'excel_' + Sistema.getRandomInt(1, 999);
            form = '<form name="' + nome + '" action="/default/export/excel-com-template" method="post" target="_blank">';
            form += '<input type="hidden" name="titulo" value=\'' + titulo + '\' />';
            form += '<input type="hidden" name="tabela" value=\'' + tabela + '\' />';
            form += '<input type="hidden" name="nomeArquivo" value="' + nomeArquivo + '" />';
            form += '</form>';
            jQuery('body').append(form);
            jQuery("form[name='" + nome + "']").submit();
            jQuery("form[name='" + nome + "']").remove();
        }
    },
    /**
     * Exclui uma conta de pessoa juridica
     * @param codigo Codigo do voucher
     * @param atividade ID do agendamento
     * @param centroCusto Centro de custo
     * @param destinoPessoa Quem esta utilizando o voucher
     * @param callback callback a ser chamado
     * @return void
     */
    validarVoucher: function (codigo, atividade, centroCusto, destinoPessoa, callback) {

        $.post(
                baseUrl + "/default/voucher/validar",
                {
                    codigo: codigo,
                    atividade: atividade,
                    centroCusto: centroCusto,
                    destinoPessoa: destinoPessoa
                },
        function (r) {
            if (callback) {
                callback(r);
            }
        }, "json");
    },
    data2br: function (data) {
        if (data == null) {
            return '';
        }
        return data.split("-").reverse().join("/");
    },
    gerarArquivoRemessa: function (ids, callback) {

        $.post(baseUrl + "/troca-arquivos/arquivo-remessa-gerar",
                ids
                , function (data) {
                    if (callback) {
                        callback(data);
                    }
                }, "json");
    },
    listarArquivoRemessa: function (layoutId, dataInicio, dataFim, callback) {

        $.post(baseUrl + "/troca-arquivos/arquivo-remessa-listar",
                {
                    controle_layout_id: layoutId,
                    data_inicial: dataInicio,
                    data_final: dataFim
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    downloadArquivoRemessa: function (layoutId, arquivo) {
        location.href = baseUrl + "/troca-arquivos/arquivo-remessa-download?controle_layout_id=" + layoutId + "&arquivo=" + arquivo;
    },
    enviarConviteAvaliador: function (pessoaId, centroCustoId, callback) {
        $.post(baseUrl + "/avaliadores/enviar-convite",
                {
                    pessoa_id: pessoaId,
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    desvincularTemasAvaliador: function (pessoaId, callback) {
        $.post(baseUrl + "/avaliadores/desvincular",
                {
                    pessoa_id: pessoaId,
                    c: 'true'
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    enviarConvitePalestrante: function (id, centroCustoId, callback, lingua) {
        $.post(baseUrl + "/convite-palestrantes/enviar-convite",
                {
                    lingua: lingua,
                    pessoa_id: id,
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    enviarConviteCaex: function (id, centroCustoId, callback, lingua) {
        $.post(baseUrl + "/caex/enviar-convite",
                {
                    lingua: lingua,
                    id: id,
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    enviarArquivoExpositoresCaex: function (id, centroCustoId, callback, lingua) {
        $.post(baseUrl + "/caex/enviar-arquivo-expositores",
                {
                    lingua: lingua,
                    id: id,
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    enviarConviteCaexExpositor: function (id, centroCustoId, caexCentroCustoId, callback, lingua, enviarPara, emailPara) {
        $.post(baseUrl + "/evento/" + centroCustoId + "/caex/expositor/enviar-convite",
                {
                    lingua: lingua,
                    id: id,
                    caexCentroCustoId: caexCentroCustoId,
                    enviarPara: enviarPara,
                    emailPara: emailPara
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    enviarConviteMembro: function (pessoaId, centroCustoId, callback, lingua) {
        $.post(baseUrl + "/convite-membros/enviar-convite",
                {
                    lingua: lingua,
                    pessoa_id: pessoaId,
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    enviarConviteMembro: function (pessoaId, centroCustoId, callback, lingua) {
        $.post(baseUrl + "/convite-membros/enviar-convite",
                {
                    lingua: lingua,
                    pessoa_id: pessoaId,
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
            enviarNumeracaoAvaliador: function (trabalhoId, callback) {
                $.post(baseUrl + "/trabalho/avaliadores/enviar-numeracao",
                        {
                            trabalho_id: trabalhoId
                        }
                , function (data) {
                    if (callback) {
                        callback(data);
                    }
                }, "json");
            },
    enviarDistribuicaoTrabalhosAvaliador: function (pessoaId, centroCustoId, callback) {
        $.post(baseUrl + "/trabalho/avaliadores/enviar-distribuicao-trabalho",
                {
                    pessoa_id: pessoaId,
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (callback) {
                callback(data);
            }
        }, "json");
    },
    isCNPJ: function (pCnpj)
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
    },
    setCentroCustoPadrao: function (centroCustoId)
    {
        $.post(baseUrl + '/utilidades/set-centro-custo-padrao',
                {
                    centro_custo_id: centroCustoId
                }
        , function (data) {
            if (data.status == 1) {
                Sistema.alert("Centro de custo padrão alterado com sucesso");
            } else {
                Sistema.alert("Error ao alterar. <b>" + data.msg + " </b>");
            }
        }, 'json');
    },
    /**
     * Na tela temos uma colecao de escolaridades, entao o nome dos campos devem estar no formato escolaridade[x][campo].
     * Para cada nova escolaridade inserido ou excluido, eh necessario atualizar os nomes dos campos.
     */
    atualizarNomeCamposEscolaridades: function (atualizaId) {
        // Quantidade
        var contador = 0;
        $(".escolaridade").each(function () {

            $(this).find(Sistema.inputsForm).each(function () {

                var nomeCampo = $(this).attr("name").split("[");
                if (nomeCampo[2]) {
                    nomeCampo[1] = nomeCampo[2];
                }

                if (nomeCampo[1]) {
                    nomeCampo = nomeCampo[1].replace("]", "");
                    $(this).attr("name", "escolaridade[" + contador + "][" + nomeCampo + "]");
                    if (atualizaId == true) {
                        $(this).attr("id", "escolaridade-" + nomeCampo + "_" + contador);
                    }
                    $(this).attr("contador", contador);
                }
            });
            $(this).find('.campos-empresa').addClass('campos-empresa-' + contador);
            contador++;
        });
        $(".date").datepicker("destroy");
    },
    /**
     * Adiciona escolaridade
     * @returns {Boolean}
     */
    adicionarEscolaridade: function () {

        var dados = null;
        dados = $(".escolaridade:first").clone();
        dados.find(Sistema.inputsForm)
                .not(Sistema.inputsFormBotoes)
                .val("")
                .removeAttr("checked")
                .removeClass("error");
        dados.find('.campos-empresa').css('display', 'none');
        //$(".add_escolaridade").remove();

        $(".escolaridade:first").parent().append(dados);
        Sistema.atualizarNomeCamposEscolaridades(true);
        contador = $(".escolaridade:last").find(Sistema.inputsForm).not(Sistema.inputsFormBotoes).attr('contador');
        $("#escolaridade_pessoa-curso_descricao_" + contador).attr("readonly", true);
        Sistema.cursoF2(contador, 'Cursos', $('#categoria_profissional_id'), $('#id_categoria_curso_area'));
        Sistema.removerMascaras();
        Sistema.aplicarMascaras();
        $(".date").datepicker("destroy");
        return false;
    },
    removerEscolaridade: function (botao) {
        if ($(".escolaridade").length < 2) {
            Sistema.alert("Pelo menos uma escolaridade deve ser informado.");
        } else {
            $(botao).parent().parent().remove();
        }

        contador = $(".escolaridade:last").find(Sistema.inputsForm).not(Sistema.inputsFormBotoes).attr('contador');
        Sistema.atualizarNomeCamposEscolaridades(false);
        Sistema.removerMascaras();
        Sistema.aplicarMascaras();
        $(".date").datepicker("destroy");
        return false;
    },
    closeDialog: function () {
        $('.ui-dialog').filter(function () {
            return $(this).css("display") === "block";
        }).find('.ui-dialog-content').dialog('close');
    },
    cursoF2: function (i, titulo, categoria_id, id_categoria_curso_area) {

        $("#escolaridade_pessoa-curso_descricao_" + i).attr("readonly", true);
        $("#escolaridade-curso_id_" + i).f2({
            model: "Cursos",
            url: baseUrl + "/default/f2",
            campoId: "escolaridade-curso_id_" + i,
            campoValor: "escolaridade-curso_descricao_" + i,
            titulo: titulo,
            elementos: {'categoria_id': categoria_id, 'id_categoria_curso_area': id_categoria_curso_area}
        });
//        $("#escolaridade-curso_id_" + i).change(function() {
//            var val = $(this).val();
//
//            if (val == 1) {
//                $("#curso_outro_" + i).show();
//            } else {
//                $("#curso_outro_" + i).hide();
//                $('#escolaridade-curso_' + i).val('');
//            }
//
//            return false;
//        });
    },
    popularCampos: function (table, json) {
        $.each(json, function (field, value) {
            console.log(table + field + ' | ' + value);
            $('#' + table + field).val(value);
        });
    },
    modificar: function (campo, status) {
        $(campo).attr('modificado', status);
    },
    modificado: function (campo) {
        if (campo.length > 0) {
            return false;
        }
        return true;
    },
    modificar_mensagem: function (campo) {

        if (!Sistema.modificado($(campo).filter('[modificado="true"]'), true)) {
            Sistema.confirm('Você modificou um campo, deseja sair sem salvar?', function (e) {
                if (!e) {
                    return false;
                } else {
                    location.href = $('.active').attr('href');
                }
            });
        } else {
            location.href = $('.active').attr('href');
        }
    },
    modificar_mensagem_frame: function (campo, link) {
        //'#_admin_pessoas'
        if (!Sistema.modificado($(campo).contents().find('[modificado="true"]'), true)) {
            Sistema.confirm('Você modificou um campo, deseja sair sem salvar?', function (e) {
                if (!e) {
                    return false;
                } else {
                    Sistema.removerFrameLink(link);
                }
            });
        } else {
            Sistema.removerFrameLink(link);
        }
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
    getAbcdiTiposEquipamentos: function (modalidadeId, combo, selecionado) {
        try {
            if (modalidadeId != "") {
                $.getJSON(baseUrl + "/utilidades/get-abcdi-tipos-equipamentos", {
                    modalidadeId: modalidadeId
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

                });
            } else {
                combo.empty();
                combo.append('<option value="0">' + Itarget.lang.outros.selecione + '</option>');
            }
        } catch (e) {
        }
    },
    /**
     * checkAllRegisters, marcar todas as linhas do flexgrid que estão carregadas e desmarcar caso já estejam marcadas!
     */
    checkAllRegisters: function () {

        if ($('#flexgrid-list tr[id*="row"]').length == 0) {
            return false;
        }

        if ($('#flexgrid-list tr[id*="row"]').hasClass('trSelected')) {
            $('#flexgrid-list tr[id*="row"]').removeClass('trSelected');
            $('.checkButton').html('<i class="fa fa-check-square"></i> Marcar Todos');
        } else {
            $('#flexgrid-list tr[id*="row"]').addClass('trSelected');
            $('.checkButton').html('<i class="fa fa-times"></i> Desmarcar Todos');
        }
    },
    fncGridAdd: function () {
        window.location.href = baseUrl + "/" + moduleName + "/" + controllerName + "/create";
    },
    excluirRegistrosGrid: function () {
        var linhas = $('.trSelected td:first-child div');
        var qtde = linhas.length;
        if (qtde < 1) {
            Sistema.alert("Selecione pelo menos um Registro!");
            return;
        }

        var msg = "Confirma exclusao de  "
                + qtde
                + " registro(s) ?";
        var params = {};
        params.linhas = linhas;
        params.url = baseUrl + "/" + moduleName + "/" + controllerName + "/delete";
        Sistema.deletar(msg, params);
    },
    submitList: function (idList) {
        $("#flexgrid-list")
                .flexOptions({
                    newp: 1,
                    params: $('#' + idList).serializeArray()
                }).flexReload();
        $(".flexigrid").fadeIn('fast');
        $('.box-busca-flutuante').hide(500);
        return false;
    }
};
$(document).ready(function () {

// Quando tiver busca avançada
    $(".busca-avancada").click(function () {
        $(".box-busca-avancada").toggle();
    });
    $('a').click(function () {
        if ($(this).parent().parent().hasClass('ui-tabs-nav')) {
            Sistema.atualizaTamanhoElementos();
        }
    });
    // Atualiza o tamnho do frame e barra da esquerda ao trocar de aba
    $(".ui-tabs").bind('tabsshow', function (event, ui) {
        Sistema.atualizaTamanhoElementos();
    });
    // Callback para as chamadas em AJAX, pois os elementos nao estao na pagina quando a mesma e carregada.
    $(document).click(function (e) {
        var t = $(e.target);
        // Todo link com a classe next_div_toggle, bom vc jah sabe o resto :P
        if (t.is("a") && t.hasClass("next_div_toggle")) {
            t.next("div").toggle("fast", function () {
                Sistema.atualizaTamanhoElementos();
            });
        }
    });
    $(Sistema.inputsForm).not(Sistema.inputsFormBotoes).focus(function () {
        $(this).addClass("ativo");
    }).blur(function () {
        $(this).removeClass("ativo");
    });
    Sistema.aplicarMascaras();
    $("ul.errors").each(function () {

// class error em input
        $(this).prev().addClass("error");
        //input radio
        if (undefined == $(this).prev().attr("type")) {
            $(this).parent("dd").css("border", "1px solid red");
        }

// O campo pode estar em uma aba, entao vamos realcar o titulo da aba =)
        $(".ui-tabs-panel").each(function () {
            if ($(this).find(".error").length > 0) {
                $("a[href='#" + $(this).attr("id") + "']").css("color", "red")
            }
        });
    });
    $("input[name*='email']").css("text-transform", "lowercase").blur(function () {
        $(this).val($(this).val().toLowerCase());
    });
    $("ul#nav a").click(function () {
//$(this).parent().parent().parent().parent().parent().find("ul:first").attr("style", "visibility: hidden !important");
    });
});
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