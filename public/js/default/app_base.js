function App_Base() {

    var self = this;

    this.formBusca = '';
    this.grid = '';
    this.actionDelete = '';
    this.row_prefix = '#row_';
    this.actionEdit = '';
    this.formSave = '';
    this.formPrimary = '#id';
    this.containerGrid = '';
    this.containerForm = '';
    this.buttonClick = '';
    this.ClearForm = '';
    this.campoSetado = ''; //isso deve ser retirado nCB#o se usa nem deve usar isso

    this.setCamposFromBox = function setCamposFromBox(json, callback) {
        self.popularCampos(json, callback);
        $("#finder_box").dialog("close");
    },
            this.popularCampos = function popularCampos(json, container, prefix_field) {

                container = typeof container !== 'undefined' ? container + " " : '';
                prefix_field = typeof prefix_field !== 'undefined' ? prefix_field : '';

                $.each(json, function (field, value) {
                    if ($(container + prefix_field + field).attr('type') == 'checkbox')
                    {
                        if ($(container + prefix_field + field).val() == value)
                        {
                            $(container + 'input[type="checkbox"]' + prefix_field + field).attr('checked', true);
                        } else {
                            $(container + 'input[type="checkbox"]' + prefix_field + field).attr('checked', false);
                        }
                    }
                    else
                    {
                        $(container + prefix_field + field).val(value);
                    }
                });
            },
            this.loadGrid = function loadGrid(target, json) {

                $(target + ' tbody').html("");
                $(target + ' tfoot').html("");

                var count_rows = Object.keys(json.rows).length;

                if (count_rows > 0)
                {
                    $.each(json.rows, function (row_index, row) {
                        var html = '';

                        if (json.elementMapping != undefined) {
                            var strJSON = '';
                            $.each(json.elementMapping, function (elemento_index, column_map) {
                                if (row[column_map] != undefined)
                                {
                                    strJSON += ',"' + elemento_index + '":"' + row[column_map] + '"';
                                }
                            });

                            strJSON = '{' + strJSON.substring(1) + '}';
                        }

                        html += '<tr id="row_' + row[json.primary] + '">';

                        $.each(json.columns, function (column_index, column) {
                            html += '<td>';

                            if (typeof column !== 'object') {

                                if (json.editMethod != undefined) {
                                    html += '<a href="javascript:;" onclick="' + json.editMethod + '(\'' + row[json.primary] + '\');"> ';
                                    html += row[column];
                                    html += '</a>';
                                } else if (json.elementMapping != undefined && json.mappingMethod != undefined) {
                                    html += '<a href="javascript:;" onclick=\'' + json.mappingMethod + '(' + strJSON + ');';
                                    if (json.focusAfter != undefined) {
                                        html += ' $("' + json.focusAfter + '").focus();';
                                    }
                                    html += '\'>';
                                    html += row[column];
                                    html += '</a>';
                                } else if (json.clickMethod != undefined && json.clickParams != undefined) {
                                    //json.clickParams.join(',');
                                    //row[json.primary]

                                    var clickParams = '';
                                    $.each(json.clickParams, function (elemento_index, column_map) {
                                        if (row[column_map] != undefined)
                                        {
                                            clickParams += ',\'' + row[column_map] + '\'';
                                        }
                                    });

                                    html += '<a href="javascript:;" onclick="' + json.clickMethod + '(' + clickParams.substring(1) + ');"> ';
                                    html += row[column];
                                    html += '</a>';

                                } else {
                                    html += row[column];
                                }
                            } else {
                                //trantando a coluna enviada como template
                                if (column.template != undefined)
                                {
                                    var matches = column.template.match(/\{\{(.*?)\}\}/g);

                                    if (matches) {
                                        var columnTemplate = column.template;
                                        for (var i = 0, j = matches.length; i < j; i++) {
                                            //html += i +'-'+ matches[i];
                                            var columnName = matches[i].replace('{{', '').replace('}}', '');
                                            console.log(matches[i]);

                                            columnTemplate = columnTemplate.replace(matches[i], row[columnName]);

                                        }

                                        html += columnTemplate;
                                    } else {
                                        html += column.template;
                                    }
                                } else {
                                    html += column;
                                }
                            }
                            html += '</td>';
                        });

                        if (json.editMethod != undefined || json.deleteMethod) {
                            html += '<td style="text-align:left;">';

                            if (json.editMethod != undefined)
                            {
                                html += '<a href="javascript:;" onclick="' + json.editMethod + '(\'' + row[json.primary] + '\');" title="Editar"><i class="fa fa-edit"></i></a>';
                            } else if (json.elementMapping != undefined && json.mappingMethod != undefined) {
                                html += '<a href="javascript:;" onclick=\'' + json.mappingMethod + '(' + strJSON + ');\' title="Editar"><i class="fa fa-edit"></i></a>';
                            }
                            html += '&nbsp;&nbsp;';
                            if (json.deleteMethod != undefined)
                            {
                                html += '<a href="javascript:;" onclick="' + json.deleteMethod + '(\'' + row[json.primary] + '\');" title="Excluir" ><i class="fa fa-trash-o"></i> </a></a>';
                            }
                            html += '</td>';
                        }

                        html += '</tr>';

                        $(target + ' tbody').append(html);

                    });

                    //$(target).tableSelect();

                    //paginacao

                    if (json.numPages != undefined && json.numPages > 1)
                    {
                        var page = '';
                        page += '<tfoot>';
                        page += '   <tr>';
                        page += '       <td colspan="' + json.columns.length + '">';
                        page += '           <input type="hidden" name="activePage" id="activePage" value="' + json.activePage + '"/>';
                        page += '           <div class="text-center">';
                        page += '               <ul class="pagination">';

                        if (json.activePage > 1)
                        {
                            page += '                   <li><a href="javascript:;" onclick="$(\'' + self.formBusca + ' #activePage\').val(' + (eval(json.activePage) - 1) + '); ' + json.search + '();"><<</a></li>';
                        }

                        for (i = (eval(json.activePage) - 3); i <= (eval(json.activePage) + 3); i++)
                        {
                            if (json.activePage == i)
                            {
                                page += '                   <li class="active"><a href="javascript:;">' + i + '</a></li>';
                            }
                            else if (i > 0 && i <= json.numPages)
                            {
                                page += '                   <li><a href="javascript:;" onclick="$(\'' + self.formBusca + ' #activePage\').val(' + i + '); ' + json.search + '();">' + i + '</a></li>';
                            }
                        }

                        if (json.activePage < json.numPages)
                        {
                            page += '                   <li><a href="javascript:;" onclick="$(\'' + self.formBusca + ' #activePage\').val(' + (eval(json.activePage) + 1) + '); ' + json.search + '();">>></a></li>';
                        }

                        page += '               </ul>';
                        page += '           </div>';
                        page += '       </td>';
                        page += '   </tr>';
                        page += '</tfoot>';

                        $(target + ' tfoot').remove();
                        $(target).append(page);
                    }
                }
                else
                {
                    var count_columns = Object.keys(json.columns).length;
                    var html = '';

                    html += '<tr>';
                    html += '<td colspan="' + (count_columns + 1) + '">Nenhum registro encontrado</td>';
                    html += '</tr>';

                    $(target + ' tbody').append(html);
                }
            },
            this.deletar = function deletar(id, confirm_callback, cancel_callback) {
                bootbox.confirm("Confirmar Exclus&atilde;o?", function (result) {
                    if (result) {

                        $.post(self.actionDelete, {
                            id: id
                        }, function (json) {
                            showMessenger(json.msg_type, json.msg);
                            if (json.status == 1) {
                                $(self.grid + ' ' + self.row_prefix + id).remove();
                            }
                        }, "json"
                                );

                        confirm_callback ? confirm_callback() : null;
                    }
                    else
                    {
                        cancel_callback ? cancel_callback() : null;
                    }
                });
            },
            this.buscar = function buscar() {

                $.post(
                        $(self.formBusca).attr('action'),
                        $(self.formBusca).serialize(),
                        function (json) {
                            self.loadGrid(self.grid, json);
                        }, "json"
                        );

                return false;
            },
            this.editar = function editar(id, prefix_field) {
                self.showForm();

                prefix_field = typeof prefix_field !== 'undefined' ? prefix_field : '#';

                $.post(self.actionEdit,
                        {id: id},
                function (json) {
                    self.popularCampos(json.dados, self.formSave, prefix_field);
                },
                        "json"
                        );
            },
            this.clearFormValidation = function clearFormValidation() {
                $(this.formSave).children(":input").removeAttr("original-title");
                $(this.formSave).children(":input").removeAttr("title");
                $(this.formSave).children(".error").removeClass("error");
            },
            this.clearFormData = function clearFormData() {

                $(self.formSave + " :input").each(function () {
                    var field_type = (this.tagName.toLowerCase() != 'input' ? this.tagName.toLowerCase() : this.type);

                    switch (field_type) {
                        case "text":
                        case "password":
                        case "textarea":
                        case "hidden":
                            $(this).val('');
                            break;
                        case "radio":
                        case "checkbox":
                            $(this).attr('checked', false);
                            break;
                        case "select":
                        case "select-one":
                        case "select-multi":
                            $(this).val("default");
                            break;
                        default:
                            break;
                    }
                    return true;
                });
            },
            this.salvar = function salvar() {

                //$.post assincrono permite o retorno da variaB!vel fora do scopo do json
                var id;
                $.ajaxSetup({
                    async: false
                });

                $.post(
                        $(self.formSave).attr('action'),
                        $(self.formSave).serialize(),
                        function (json) {

                            if (json.status == 1) {
                                if (json.editMode == 0) {
                                    if (self.showGrid()) {
                                        self.showGrid();
                                        self.clearFormData();

                                        $(self.grid + ' tbody').html("");
                                    }
                                }

                                id = json.dados;

                                if (self.formPrimary != undefined) {
                                    $(self.formSave + ' ' + self.formPrimary).val(id);
                                }
                            }
                            if (json.msg) {
                                showMessenger(json.msg_type, json.msg);
                            }

                        }, "json"
                        );

                return id;
            },
            this.criar = function criar() {
                self.clearFormData();
                self.showForm();
            },
            this.resetValidator = function resetValidator() {
                var jqueryValidator = $(self.formSave).validate();
                jqueryValidator.resetForm();
                //$(self.formSave).removeAttr("novalidate");
                //$(self.formSave).find("div").removeClass("error");
                //$(self.formSave).find("div").attr('original-title', "");
                //$('.tipsy').remove();
                //$(self.formSave).validate();
                //$(self.formSave).find("div").removeClass("error");
                //$(self.formSave).removeClass("error");
                //$('.tipsy').remove();
            },
            this.cancelar = function cancelar() {
                self.showGrid();
                self.buscar();
                self.clearFormData();
            },
            this.showForm = function showForm() {
                $(self.containerGrid).hide();
                $(self.containerForm).show();

                $(self.containerForm + ' input[type!="hidden"]:first').focus();
            },
            this.showGrid = function showGrid() {
                self.clearFormData();
                $(self.containerForm).hide();
                $(self.containerGrid).show();

                $(self.containerGrid + ' :input:not(input[type=hidden]):first').focus();

                var input_focar = $(self.containerGrid + ' :input:not(input[type=hidden]):first');
            },
            //setar valor sera removido
            this.setarValor = function setarValor() {
                $.each(self.campoSetado, function (col, val) {
                    $(col).val(val);
                });
            },
            this.salvarValidando = function salvarValidando(alerta) {
                var id = '';

                //for selectbox
                jQuery.validator.addMethod("valueNotEquals", function (value, element, arg) {
                    return arg != value;
                }, "* N&atilde;o Selecionado");


                //for CNPJ

                /*
                 *
                 * NOVO METODO PARA O JQUERY VALIDATE
                 * VALIDA CNPJ COM 14 OU 15 DIGITOS
                 * A VALIDCAO E FEITA COM OU SEM OS CARACTERES SEPARADORES, PONTO, HIFEN, BARRA
                 *
                 * ESTE METODO FOI ADAPTADO POR:
                 * 
                 * Shiguenori Suguiura Junior <junior@dothcom.net>
                 * 
                 * http://blog.shiguenori.com
                 * http://www.dothcom.net
                 * 
                 */
                jQuery.validator.addMethod("cnpj", function (cnpj, element) {
                    cnpj = jQuery.trim(cnpj);

                    // DEIXA APENAS OS NCE!MEROS
                    cnpj = cnpj.replace('/', '');
                    cnpj = cnpj.replace('.', '');
                    cnpj = cnpj.replace('.', '');
                    cnpj = cnpj.replace('-', '');

                    var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
                    digitos_iguais = 1;

                    if (cnpj.length < 14 && cnpj.length < 15) {
                        return this.optional(element) || false;
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
                            return this.optional(element) || false;
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
                            return this.optional(element) || false;
                        }
                        return this.optional(element) || true;
                    } else {
                        return this.optional(element) || false;
                    }
                }, "* Informe um CNPJ V&aacute;lido.");

                //validar data
                jQuery.validator.addMethod("dateBR", function (value, element) {
                    //contando chars
                    if (value.length != 10)
                        return false;
                    // verificando data
                    var data = value;
                    var dia = data.substr(0, 2);
                    var barra1 = data.substr(2, 1);
                    var mes = data.substr(3, 2);
                    var barra2 = data.substr(5, 1);
                    var ano = data.substr(6, 4);

                    if (data.length != 10 || barra1 != "/" || barra2 != "/" || isNaN(dia) || isNaN(mes) || isNaN(ano) || dia > 31 || mes > 12)
                        return false;
                    if ((mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia == 31)
                        return false;
                    if (mes == 2 && (dia > 29 || (dia == 29 && ano % 4 != 0)))
                        return false;
                    if (ano < 1900)
                        return false;
                    return true;
                }, "Informe uma Data V&aacute;lida");  // Mensagem padrao 

                //validar cpf
                jQuery.validator.addMethod("cpf", function (value, element) {
                    value = jQuery.trim(value);

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

                    var retorno = true;
                    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg))
                        retorno = false;

                    return this.optional(element) || retorno;

                }, "Informe um CPF V&aacute;lido."); // Mensagem padrao 

                $(self.formSave).validate({
                    debug: false,
                    focusCleanup: true,
                    onkeyup: false,
                    onfocusout: false,
                    focusInvalid: false,
                    validClass: "success",
                    onsubmit: false,
                    highlight: function (element, errClass) {
                        $(element).attr('title', "");
                        $(element).parent().addClass("error");
                        $(element).tipsy({
                            trigger: 'manual',
                            gravity: 'sw',
                            className: self.formSave.substring(1)
                        });
                    },
                    unhighlight: function (element, errClass) {
                        $(element).parent().removeClass("error");
                        $(element).attr('original-title', "");
                        $(element).tipsy("hide");
                    },
                    errorPlacement: function (error, element) {
                        element.attr('title', error.text());
                        element.tipsy("show");
                    }
                });

                if (alerta == true) {
                    if ($(self.formSave).valid() == true) {

                        bootbox.dialog("Deseja gravar a Informa&ccedil;&otilde;es? ",
                                [{
                                        "label": "NC#o",
                                        "class": "btn",
                                        "callback": function () {
                                            return;
                                        }
                                    }, {
                                        "label": "Sim",
                                        "class": "btn btn-info",
                                        "callback": function () {
                                            id = self.salvar();
                                        }
                                    }
                                ]);
                    }
                } else {
                    if ($(self.formSave).valid() == true) {
                        id = self.salvar();
                        self.resetValidator();
                    }
                }
                return id;
            }

    this.clearGrid = function clearGrid()
    {
        $(self.grid + ' tbody').html("");
        $(self.grid + ' tfoot').html("");
    }
}