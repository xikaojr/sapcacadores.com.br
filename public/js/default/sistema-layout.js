SistemaLayout = {
    intervalBarra: null,
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

            tamanhoBodyFrame = parseInt(tamanhoBodyFrame) + valorComplemento;

            if (valorAltura != undefined && valorAltura > 300)
                tamanhoBodyFrame = parseInt(valorAltura);

            window.parent.$('#esquerda').css('height', tamanhoBodyFrame + 'px');
            window.parent.$("#conteudo_pagina iframe").filter(':visible').css('height', tamanhoBodyFrame + 'px');

        }
    },
    barraEsquerda: {
        cookie: function () {
            if ($.cookie("barra-esquerda") == "hide") {
                SistemaLayout.barraEsquerda.ocultar();
            } else {
                SistemaLayout.barraEsquerda.exibir();
            }
        },
        acaoBotao: function () {
            if ($('.conteudo_esquerda').css('display') == 'none') {
                SistemaLayout.barraEsquerda.exibir();
            } else {
                SistemaLayout.barraEsquerda.ocultar();
            }
        },
        exibir: function () {
            $('#esquerda').css('width', '140px');
            $('#direita').css("margin-left", "153px");
            $('.conteudo_esquerda').show();

            SistemaLayout.atualizaTamanhoElementos();

            $.cookie("barra-esquerda", "show");
        },
        ocultar: function () {
            SistemaLayout.atualizaTamanhoElementos();
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
    resizeIframe: function (obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    },
    /**
     * Cria um frame, onde a pagina sera alocada, para que seja possivel a
     * navegacao.
     */
    criarFrameLink: function (link, titulo, descricao) {

        var id = SistemaLayout.gerarIdLink(link);
        var d = window.parent.document || window.document;

        $(".frame_conteudo", d).hide();
        $(".seleciona_recentes", d).removeClass("aba_ativa").addClass("aba_inativa");

        if (!$("#" + id, d).attr('id')) {
            iframe = "<iframe scrolling='auto' width='100%' onload='javascript:SistemaLayout.resizeIframe(this);' name='" + id + "' frameborder='0' ";
            iframe += "vspace='0' hspace='0' marginwidth='0' marginheight='0' ";
            iframe += "scrolling='no' noresize class='frame_conteudo' ";
            iframe += "id='" + id + "' src='" + link + "'></iframe>";

            $("#conteudo_pagina", d).append(iframe);
            SistemaLayout.criarAbaLink(link, titulo, descricao);
        } else {
            abaId = SistemaLayout.gerarIdAba(SistemaLayout.gerarIdLink(link));
            $("#" + abaId, d).removeClass("aba_inativa").addClass("aba_ativa");
        }

        try {
            $("#" + id, d).show();
            $("#" + id, d).iframeAutoHeight();

            SistemaLayout.atualizaTamanhoElementos();
        } catch (e) {
            SistemaLayout.atualizaTamanhoElementos();
        }
    },
    /**
     * Cria uma aba na lateral esquerda, para o link clicado
     */

    criarAbaLink: function (link, titulo, descricao) {
        var d = window.parent.document || window.document;
        var abaId = Sistema.gerarIdAba(Sistema.gerarIdLink(link));

        var aba = '<div class="seleciona_recentes" id="' + abaId + '">';
        aba += '<a style="" title="' + Itarget.lang.botoes.fechar + '" href="javascript:;" onclick="Sistema.modificar_mensagem_frame(\'#' + Sistema.gerarIdLink(link) + '\',\'' + link + '\');return false;">';
        aba += '<i class="fa fa-times" style="color:#333;float:right; margin-top:-5px;"></i>';
        aba += '</a>';
        aba += '<p onclick="Sistema.criarFrameLink(\'' + link + '\');" style="cursor: pointer"><strong>' + titulo + '</strong><br />' + descricao + '</p>';
        aba += '</div>';

        $("#abas_recentes", d).append(aba);
    },
    /**
     * Remove o frame que foi criado para o link
     */
    removerFrameLink: function (link) {
        var d = window.parent.document || window.document;
        ;
        id = SistemaLayout.gerarIdLink(link);
        $("#" + id, d).remove();
        SistemaLayout.removerAbaLink(link);

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
        abaId = SistemaLayout.gerarIdAba(SistemaLayout.gerarIdLink(link));
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
        SistemaLayout.carregandoLinkRapido();

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
            SistemaLayout.montarLinkRapido(links);
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
            abas += '<p onclick="Sistema.criarFrameLink(\'' + link + '\', \'' + titulo + '\', \'' + descricao + '\');" style="cursor: pointer"><strong>' + titulo + '</strong><br />' + descricao + '</p>';
            abas += '</div>';

        }

        // A chamada vem de um frame
        $("#abas_links_rapidos", window.parent.document).html(abas);

    },
    excluirLinkRapido: function (id) {
        SistemaLayout.carregandoLinkRapido();
        $.post(
                baseUrl + "/usuarios-preferencias/excluir-link-rapido",
                {
                    id: id
                },
        function (links) {
            SistemaLayout.montarLinkRapido(links);
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
    getRandomInt: function (min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
};

$(document).ready(function () {

    // Quando tiver busca avançada
    $(".busca-avancada").click(function () {
        $(".box-busca-avancada").toggle();
    });

    $('a').click(function () {
        if ($(this).parent().parent().hasClass('ui-tabs-nav')) {
            SistemaLayout.atualizaTamanhoElementos();
        }
    });

    // Atualiza o tamnho do frame e barra da esquerda ao trocar de aba
    $(".ui-tabs").bind('tabsshow', function (event, ui) {
        SistemaLayout.atualizaTamanhoElementos();
    });

    // Callback para as chamadas em AJAX, pois os elementos nao estao na pagina quando a mesma e carregada.
    $(document).click(function (e) {
        var t = $(e.target);
        // Todo link com a classe next_div_toggle, bom vc jah sabe o resto :P
        if (t.is("a") && t.hasClass("next_div_toggle")) {
            t.next("div").toggle("fast", function () {
                SistemaLayout.atualizaTamanhoElementos();
            });
        }
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