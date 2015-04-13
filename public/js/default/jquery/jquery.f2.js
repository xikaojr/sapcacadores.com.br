(function ($) {
    $.fn.f2 = function (options) {

        var rand = Sistema.getRandomInt(99, 99999);
        var dialogId = rand + '_div';

        var defaults = {
            model: undefined, // Model que realizara a busca
            titulo: '', // Titulo da janela
            frase: Itarget.lang.outros.localizar, // Frase "localizar" que aparecera no form
            campoId: undefined, // Id do campo que recebera o id do item selecionado
            campoValor: undefined, // Id do campo que recebera o valor do item selecionado
            campoValor2: undefined, // Id do campo extra que recebera um outro valor do item selecionado(por ex.: atividade_id, atividade_descricao, agendamento_sigla
            url: baseUrl + '/default/f2', // Url que retornara o resultado
            modal: true, // A tela sera do tipo modal?
            width: 520, // Largura da janela
            height: 'auto', // Altura da janela
            qtdMin: false, // Obrigar digitar a qtd minima de caracteres
            opcaoBusca: undefined, // Informacao a ser exibida, ao inves de "localizar"
            params: false, // Parametros para realizar a busca
            semBusca: false, // Retira a busca da tela e já faz o submit do form
            elementos: undefined, // Elementos que terao o valor obtido no momento em que o form for submetido
            dialogId: dialogId
        };

        var options = $.extend(defaults, options); // mesclando as opcoes
        var formId = rand + '_form';
        var htmlDialog = "";

        if (options.model == "PlanoConta") {
            options.semBusca = true;
            options.qtdMin = false;
            if (!options.params) {
                options.params = true;
            }
        }

        if (options.model == "PlanoContaNumeros") {
            options.semBusca = true;
            options.qtdMin = false;
            if (!options.params) {
                options.params = true;
            }
        }

        if (options.model == "Pessoa") {
            if (!options.qtdMin) {
                options.qtdMin = 3;
            }

            if (undefined == options.opcaoBusca) {
                options.opcaoBusca = '<div class="form-group col-md-6">';
                options.opcaoBusca += Itarget.lang.outros.localizar + ' <select name="tipo_busca" class="form-control">';
                options.opcaoBusca += '<option value="F">' + Itarget.lang.outros.pessoaFisica + '</option>';
                options.opcaoBusca += '<option value="J">' + Itarget.lang.outros.pessoaJuridica + '</option>';
                options.opcaoBusca += '<option value="cpf">CPF</option>';
                options.opcaoBusca += '<option value="cnpj">CNPJ</option>';
                options.opcaoBusca += '<option value="matricula">Matricula</option>';
                options.opcaoBusca += '<option value="email">Email</option>';
                options.opcaoBusca += '<option value="id">Id</option>';
                options.opcaoBusca += '</select>';
                options.opcaoBusca += '</div>';
            }
        }
        // Opcao de busca
        if (!options.opcaoBusca) {
            options.opcaoBusca = '<label>' + options.frase + '</label><br/>'
                    + '<input type="text" class="form-control" name="buscar" value="" /><br/>';
        } else {
            options.opcaoBusca =
                    options.opcaoBusca
                    + '<div class="form-group col-md-6 col-xs-6">'
                    + 'Valor'
                    + '<input type="text" class="form-control" name="buscar" value="" />'
                    + '</div>';
        }

        htmlDialog += '<div title="' + options.titulo + '" class="dialog_f2" id="' + dialogId + '" style="display:none">';
        htmlDialog += '<form class="form form_f2" id="' + formId + '">';
        htmlDialog += '<input type="hidden" name="model" value="' + options.model + '" />';
        htmlDialog += '<input type="hidden" name="campo_id" value="' + options.campoId + '" />';
        htmlDialog += '<input type="hidden" name="campo_valor" value="' + options.campoValor + '" />';
        htmlDialog += '<input type="hidden" name="campo_valor2" value="' + options.campoValor2 + '" />';

        if (!options.semBusca) {
            htmlDialog += '<div class="row">';
            htmlDialog += '<div class="form-group col-md-12 col-xs-12">';
            htmlDialog += options.opcaoBusca;
            htmlDialog += '</div>';
            htmlDialog += '</div>';
            htmlDialog += '<div class="form-actions">';
            htmlDialog += '<div class="row">';
            htmlDialog += '<div class="col-md-12 botoes">';
            htmlDialog += '<input type="submit" value="' + Itarget.lang.botoes.buscar + '" class="botaoAssociado btn btn-primary" />';
            htmlDialog += '</div>';
            htmlDialog += '</div>';
            htmlDialog += '</div>';
        }

        htmlDialog += '</form>';
        htmlDialog += '<div class="resultado_f2"></div>';
        htmlDialog += '</div>';

        $("body").append(htmlDialog);
        $("#" + dialogId).dialog({
            modal: options.modal,
            width: options.width,
            height: options.height,
            autoOpen: false,
            position: 'top',
            close: function (ev, ui) {
                $(this).find(".resultado_f2").html("");
            }
        });

        Sistema.atualizaTamanhoElementos();
        
        $("#" + formId).submit(function () {

            var form = $(this);
            var campoId = form.find("input[name='campo_id']").val();
            var campoValor = form.find("input[name='campo_valor']").val();
            var campoValor2 = form.find("input[name='campo_valor2']").val();

            if (options.qtdMin) {
                if ($.trim(form.find("input[name='buscar']").val()).length < 3) {
                    showMessenger(null, Itarget.lang.outros.qtdMinimaCaracteres);
                    return false;
                }
            }

            options.params = options.params || '';

            if (options.elementos != undefined && options.elementos) {
                for (var chave in options.elementos) {
                    if (options.elementos[chave] && options.elementos[chave].val() != undefined) {
                        if (options.params.indexOf(chave) != -1) {
                            options.params = options.params.replace(chave + "=" + options.elementos[chave].val() + "&", '');
                        }
                        options.params += "&" + chave + "=" + options.elementos[chave].val() + "&";
                    }
                }
            }

            if (options.callbackParams) {
                options.callbackParams(options);
            }

            options.params += "&dialogId=" + dialogId;

            form.next(".resultado_f2").html(Itarget.lang.outros.aguarde);

            $.post(
                    options.url,
                    form.serialize() + options.params,
                    function (data) {

                        var htmlTabela = "";

                        htmlTabela += '<div class="table-responsive">';
                        htmlTabela += '<table class="table table-striped">';
                        htmlTabela += '<thead>';
                        htmlTabela += '<tr>';

                        if (data && data.cabecalho) {
                            for (var k in data.cabecalho) {
                                htmlTabela += "<th>" + data.cabecalho[k] + "</th>";
                            }
                        }

                        htmlTabela += "</tr>";
                        htmlTabela += "</thead>";
                        htmlTabela += "<tbody>";

                        if (data && data.item) {

                            for (var i in data.item) {

                                htmlTabela += '<tr style="cursor:pointer" class="registroBusca"';

                                htmlTabela += 'onclick=" try { $(\'#' + campoId + '\').val(' + data.item[i][0] + '); ';
                                htmlTabela += '$(\'#' + campoValor + '\').val(\'' + data.item[i][1] + '\'); ';
                                htmlTabela += '$(\'#' + dialogId + '\').dialog(\'close\'); ';

                                if (data.item[i][2] && 'undefined' != campoValor2) {
                                    htmlTabela += '$(\'#' + campoValor2 + '\').val(\'' + data.item[i][2] + '\'); ';
                                }

                                htmlTabela += '$(\'#' + campoId + '\').change(); ';
                                htmlTabela += '} catch(e) {}; ';

                                htmlTabela += ' ">';

                                for (var j in data.item[i]) {
                                    htmlTabela += "<td>" + data.item[i][j] + "</td>";
                                }
                                htmlTabela += "</tr>";
                            }
                        }

                        htmlTabela += "</tbody>";
                        //paginacao
                        
//                        if (data.pagination.numPages !== undefined && data.pagination.numPages > 1)
//                        {
//                            var page = '';
//                            page += '<tfoot>';
//                            page += '   <tr>';
//                            page += '       <td colspan="' + data.cabecalho.length + '">';
//                            page += '           <input type="hidden" name="activePage" id="activePage" value="' + data.pagination.activePage + '"/>';
//                            page += '           <div class="text-center">';
//                            page += '               <ul class="pagination">';
//
//                            if (data.pagination.activePage > 1)
//                            {
//                                page += '<li><a href="javascript:;" onclick="$(\'' + '#'+formId + ' #activePage\').val(' + (eval(data.pagination.activePage) - 1) + '); ' + $('#'+formId+ '.botaoAssociado').click + '();"><<</a></li>';
//                            }
//
//                            for (i = (eval(data.pagination.activePage) - 3); i <= (eval(data.pagination.activePage) + 3); i++)
//                            {
//                                if (data.pagination.activePage == i)
//                                {
//                                    page += '                   <li class="active"><a href="javascript:;">' + i + '</a></li>';
//                                }
//                                else if (i > 0 && i <= data.pagination.numPages)
//                                {
//                                    page += '                   <li><a href="javascript:;" onclick="$(\'' + '#'+formId + ' #activePage\').val(' + i + '); ' + $('#'+formId+ ' .botaoAssociado').click + '();">' + i + '</a></li>';
//                                }
//                            }
//
//                            if (data.pagination.activePage < data.pagination.numPages)
//                            {
//                                page += '                   <li><a href="javascript:;" onclick="$(\'' + self.formBusca + ' #activePage\').val(' + (eval(data.pagination.activePage) + 1) + '); ' + $('#'+formId+ ' .botaoAssociado').click + '();">>></a></li>';
//                            }
//
//                            page += '               </ul>';
//                            page += '           </div>';
//                            page += '       </td>';
//                            page += '   </tr>';
//                            page += '</tfoot>';
//
//                            $('.table tfoot').remove();
//                            htmlTabela += page;
//                        }

                        htmlTabela += "</table></div>";

                        if (undefined != data.html) {
                            form.next(".resultado_f2").html(data.html);
                        } else {
                            form.next(".resultado_f2").html(htmlTabela);
                        }

                    },
                    "json");

            return false;
        });

        // Observando o input, para exibir a dialog
        $(this).bind("keyup", function (event) {

            if (event.keyCode == 113) { // f2 = 113

                if (options.callbackDialog) {
                    options.callbackDialog(options);
                }

                $("#" + dialogId).dialog("open");
                $(this).blur();

                // Evitar a acao do botao por parte do navegador
                event.stopPropagation();
                event.preventDefault();

                if (options.semBusca && options.params != false) {
                    $("#" + formId).submit();
                }
            }
        });

    };
})(jQuery);