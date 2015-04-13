var paramBox;
var paramDialog;
var paramLoad;

// Exemplo: Janelas.abrir(paramBox, paramDialog, {}, function(){});
// Parametros da box,
// paramentros do dialog,
// parametros que seram passados ao load {POST},
// funcao a ser executada apos o load

Janelas = {
    abrir: function(paramBox, paramDialog, paramLoad, fncAfterLoad)
    {
        // Seta os parametros default caso o mesmo não seja setado
        paramDialog.autoOpen = paramDialog.autoOpen ? paramDialog.autoOpen : false;
        paramDialog.resizable = paramDialog.resizable ? paramDialog.resizable : false;
        paramDialog.draggable = paramDialog.draggable ? paramDialog.draggable : true;
        paramDialog.bgiframe = paramDialog.bgiframe ? paramDialog.bgiframe : false;
        paramDialog.closeOnEscape = paramDialog.closeOnEscape ? paramDialog.closeOnEscape : false;
        paramDialog.modal = paramDialog.modal ? paramDialog.autoOpen : true;
        paramDialog.width = paramDialog.width ? paramDialog.width : 600;
        paramDialog.height = paramDialog.height ? paramDialog.height : 470;
        paramDialog.position = paramDialog.position ? paramDialog.position : 'center';
        paramDialog.title = paramDialog.title ? paramDialog.title : "Itarget";

        // Cria a div para armazenar o janela
        Utilidades.criaElemento(paramBox.boxName, "div");

        object = $("#" + paramBox.boxName);

        object.html('').load(baseUrl + paramBox.url, paramLoad, function() {

            if (fncAfterLoad)
                fncAfterLoad();
        });

        object.dialog(paramDialog).dialog('open');
    },
    conselho: function(tituloJanela) {
        paramBox = {
            boxName: 'janela-conselho-orgao-emissor',
            url: '/default/janelas/conselho/'
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 350
        };

        Janelas.abrir(paramBox, paramDialog, {}, function() {
            var uf = $('form input[name="associacao[numero_conselho]"]').val().substr(0, 2);
            var codigo = $('form input[name="associacao[numero_conselho]"]').val().substr(2);
            var orgaoEmissor = $('form input[name="associacao[conselho_orgao_emissor_id]"]').val();

            $(".dialog-form input[name='codigo']").val(codigo).focus();
            $(".dialog-form select[name='uf']").val(uf);
            $(".dialog-form select[name='orgao_emissor']").val(orgaoEmissor);
        });
    },
    orgaoEmissorRg: function(tituloJanela) {
        paramBox = {
            boxName: 'janela-orgao-emissor-rg',
            url: '/default/janelas/orgao-emissor-rg/'
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 200
        };

        Janelas.abrir(paramBox, paramDialog, {}, function() {
            $(".dialog-form select[name='orgao_emissor']").val(
                    $('form input[name="pessoa[conselho_orgao_emissor_id]"]').val()
                    );
        });
    },
    cadEscolaridade: function(tituloJanela, id) {
        paramBox = {
            boxName: 'box-cad-escolaridade-pessoa',
            url: '/escolaridades-pessoas/add/ajax/1/pss/' + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    vincularUsuariosCentroCusto: function(id, tituloJanela) {
        paramBox = {
            boxName: 'box-vincular-usuarios-centro-custo',
            url: '/default/janelas/vincular-usuarios-centro-custo/id/' + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 'auto'
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    
    redefinirSenha: function(id, tituloJanela) {
        paramBox = {
            boxName: 'box-redefinir-senha',
            url: '/default/janelas/alterar-senha-usuario/id/' + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 'auto'
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    edtEscolaridade: function(tituloJanela, id, escId) {
        paramBox = {
            boxName: 'box-cad-escolaridade-pessoa',
            url: '/escolaridades-pessoas/edit/ajax/1/pss/' + id + '/id/' + escId
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    cadEspecialidade: function(tituloJanela, id) {
        paramBox = {
            boxName: 'box-cad-especialidade-pessoa',
            url: '/especialidades-pessoas/add/ajax/1/pss/' + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    edtEspecialidade: function(tituloJanela, id, escId) {
        paramBox = {
            boxName: 'box-cad-especialidade-pessoa',
            url: '/especialidades-pessoas/edit/ajax/1/pss/' + id + '/id/' + escId
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    filtroFichaAtualizacao: function(tituloJanela, dados) {
        paramBox = {
            boxName: 'box-filtro-ficha-atualizacao',
            url: '/default/janelas/filtro-ficha-atualizao-renovacao-anuidades/'
        };

        paramDialog = {
            title: tituloJanela,
            width: 550,
            height: 300
        };

        Janelas.abrir(paramBox, paramDialog, {
            dados: dados
        });
    },
    ultimoNumeroAssociado: function(tituloJanela) {
        paramBox = {
            boxName: 'box-ultimo-numero-associado',
            url: '/default/janelas/ultimo-numero-associado/'
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 150
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    /**
     * Abre uma janela para o cadastro de um recebimento
     * @param tituloJanela Titulo da janela
     * @param id Id da conta receber
     */
    abrirTelaRecebimento: function(tituloJanela, id, width, height) {

        if (width == "") {
            width = 720;
        }

        if (height == "") {
            height = 400;
        }

        paramBox = {
            boxName: 'cadastro-recebimento',
            url: baseUrl + "/baixas-contas-receber/baixar-conta-receber/id/" + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 720,
            heigth: height
        };

        Janelas.abrir(paramBox, paramDialog, {});

    },
    abrirTelaRecebimentoAssociado: function(tituloJanela, id) {

        paramBox = {
            boxName: 'cadastro-recebimento',
            url: baseUrl + "/associado/anuidades/baixar-conta-receber/id/" + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 700
        };

        Janelas.abrir(paramBox, paramDialog, {});

    },
    /**
     * Abre uma janela para o cadastro de um pagamento em lote
     * @param tituloJanela Titulo da janela
     */
    abrirTelaPagamentoLote: function(tituloJanela) {

        paramBox = {
            boxName: 'cadastro-pagamento-lote',
            url: baseUrl + "/baixas-contas-pagar/baixar-conta-pagar-lote"
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 500
        };

        Janelas.abrir(paramBox, paramDialog, {});

    },
    /**
     * Abre uma janela para o cadastro de um recebimento em lote
     * @param tituloJanela Titulo da janela
     */
    abrirTelaRecebimentoLote: function(tituloJanela) {

        paramBox = {
            boxName: 'cadastro-recebimento-lote',
            url: baseUrl + "/baixas-contas-receber/baixar-conta-receber-lote"
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 500
        };

        Janelas.abrir(paramBox, paramDialog, {});

    },
    /**
     * Abre uma janela para o cadastro de um pagamento
     * @param tituloJanela Titulo da janela
     * @param id Id da conta receber
     */
    abrirTelaPagamento: function(tituloJanela, id) {

        paramBox = {
            boxName: 'cadastro-pagamento',
            url: baseUrl + "/baixas-contas-pagar/baixar-conta-pagar/id/" + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 750
        };

        Janelas.abrir(paramBox, paramDialog, {});

    },
    /**
     * Abre uma janela para Abertura e Fechamento do caixa
     * @param fechamentoCaixa Fechamento do Caixa
     * @param id Id do caixa
     */
    fechamentoCaixa: function(tituloJanela, id, tipo) {

        paramBox = {
            boxName: 'janela-fechamento-caixa',
            url: baseUrl + "/janelas/fechamento-caixa/id/" + id + "/tipo/" + tipo
        };

        paramDialog = {
            title: tituloJanela,
            width: 650,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    /**
     * Exibe o formulario para abertura/fechamento de um extrato bancario
     * @param tituloJanela Titulo da janela
     * @param id Id da conta bancaria
     */
    fechamentoBanco: function(tituloJanela, id) {

        paramBox = {
            boxName: 'fechamento-banco',
            url: baseUrl + "/default/janelas/fechamento-banco"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 350
        };

        Janelas.abrir(paramBox, paramDialog, {
            contaBancariaId: id
        });

    },
    /**
     * Abre uma janela para replicar uma conta a pagar
     * @param tituloJanela Titulo da janela
     */
    replicarContaPagar: function(tituloJanela) {

        paramBox = {
            boxName: 'janela-replicar-conta-pagar',
            url: baseUrl + "/default/janelas/replicar-conta-pagar"
        };

        paramDialog = {
            title: tituloJanela,
            width: 950,
            height: 500
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    /**
     * Abre uma janela para replicar uma conta a receber
     * @param tituloJanela Titulo da janela
     */
    replicarContaReceber: function(tituloJanela) {

        paramBox = {
            boxName: 'janela-replicar-conta-receber',
            url: baseUrl + "/default/janelas/replicar-conta-receber"
        };

        paramDialog = {
            title: tituloJanela,
            width: 720,
            height: 510
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    /**
     * Abre uma janela para escolher a classificacao da empreasa
     * @param tituloJanela titulo da pg
     */
    classificacaoEmpresa: function(tituloJanela, id) {

        paramBox = {
            boxName: 'janela-classificacao-empresa',
            url: baseUrl + "/default/janelas/classificacao-empresa/divid/" + id
        };

        paramDialog = {
            title: tituloJanela,
            width: 650,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {});
    },
    /**
     * Abre uma janela padrao para diminuir o numeros de funcoes da janela
     * @param tituloJanela: titulo da pg
     * @param pg: pagina da janela
     * @param div: div da janela
     * @param width: Largura da janela
     * @param height: altura da janela
     * @param dados: paramentros a ser passados
     */
    openDefault: function(tituloJanela, pg, dados, div, width, height) {

        if (width == "")
            width = 700;

        if (height == "")
            height = 400;

        if (dados == "")
            dados = {};

        paramBox = {
            boxName: div,
            url: baseUrl + "/" + pg
        };

        paramDialog = {
            title: tituloJanela,
            width: width,
            height: height
//            modal: true
        };

        Janelas.abrir(paramBox, paramDialog, dados);
    },
    /**
     * Abre uma janela para vincular caixa usuários
     * @param tituloJanela - Titulo da janela
     * @param caixaId - Id do caixa
     * @return void
     */
    vincularCaixa: function(tituloJanela, caixaId) {

        paramBox = {
            boxName: 'janela-vincular-caixa',
            url: baseUrl + "/default/janelas/vincular-caixa"
        };

        paramDialog = {
            title: tituloJanela,
            width: 420,
            height: 420
        };

        Janelas.abrir(paramBox, paramDialog, {
            'caixa_id': caixaId
        });

    },
    /**
     * Abre uma janela para selecionar as empresas
     * @return void
     */
    selecionarEmpresas: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-empresas',
            url: baseUrl + "/default/janelas/selecionar-empresas"
        };

        paramDialog = {
            title: tituloJanela,
            width: 420,
            height: 380
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selecionarCentrosCustos: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-centros-custos',
            url: baseUrl + "/default/janelas/selecionar-centros-custos"
        };

        paramDialog = {
            title: tituloJanela,
            width: 550,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selecionarRegistros: function(tituloJanela, retorno, classe, query) {

        paramBox = {
            boxName: 'janela-selecionar-registros-' + retorno,
            url: baseUrl + "/default/janelas/selecionar-registros"
        };

        paramDialog = {
            title: tituloJanela,
            width: 550,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno,
            query: query,
            classe: classe
        });

    },
    selecionarRegistrosHoteis: function(tituloJanela, retorno, classe, query) {

        paramBox = {
            boxName: 'janela-selecionar-registros-' + retorno,
            url: baseUrl + "/default/janelas/selecionar-registros-hoteis"
        };

        paramDialog = {
            title: tituloJanela,
            width: 550,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno,
            query: query,
            classe: classe
        });

    },
    selecionarRegistrosCaptacao: function(tituloJanela, retorno, classe, query, contato) {

        paramBox = {
            boxName: 'janela-selecionar-registros-captacao-' + retorno,
            url: baseUrl + "/default/janelas/selecionar-registros-captacao"
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 550
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno,
            query: query,
            contato: contato,
            classe: classe
        });

    },
    selecionarPlanosContas: function(tituloJanela, retorno, tipo, valores) {

        paramBox = {
            boxName: 'janela-selecionar-planos-contas',
            url: baseUrl + "/default/janelas/selecionar-planos-contas"
        };

        paramDialog = {
            title: tituloJanela,
            width: 550,
            height: 450
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno,
            valores: valores,
            tipo: tipo
        });

    },
    /**
     * Abre uma janela para selecionar as empresas
     * @return void
     */
    selecionarRegionais: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-regionais',
            url: baseUrl + "/default/janelas/selecionar-regionais"
        };

        paramDialog = {
            title: tituloJanela,
            width: 420,
            height: 380
        }

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    /**
     * Abre uma janela para selecionar as atividades
     * @return void
     */
    selecionarAtividades: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-atividades',
            url: baseUrl + "/default/janelas/selecionar-atividades"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 380
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selecionarPessoasTornarAvaliador: function(tituloJanela, retorno, width, height) {

        if (width == "")
            width = 400;

        if (height == "")
            height = 'auto';


        paramBox = {
            boxName: 'janela-selecionar-pessoas',
            url: baseUrl + "/default/janelas/selecionar-pessoas-tornar-avaliador"
        };

        paramDialog = {
            title: tituloJanela,
            width: width,
            height: height
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selectionarPessoaFisicaCaex: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-pessoas',
            url: baseUrl + "/default/janelas/selecionar-pessoas-fisica-caex"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selectionarPessoaJuridicaCaex: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-pessoas',
            url: baseUrl + "/default/janelas/selecionar-pessoas-juridica-caex"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selecionarContaBancaria: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-conta-bancaria',
            url: baseUrl + "/default/janelas/selecionar-conta-bancaria"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selecionarAdministradoraCartao: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-administradora-cartao',
            url: baseUrl + "/default/janelas/selecionar-administradora-cartao"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    /**
     * Abre uma janela para replicar uma conta a pagar
     * @param tituloJanela Titulo da janela
     * @param id Id da conta a pagar
     */
    dadosBoleto: function(tituloJanela, id) {

        paramBox = {
            boxName: 'janela-dados-boleto',
            url: baseUrl + "/default/janelas/dados-boleto"
        };

        paramDialog = {
            title: tituloJanela,
            width: 420,
            height: 380
        }

        Janelas.abrir(paramBox, paramDialog, {
            'dados_boleto[id]': id
        });
    },
    /**
     * Abre uma janela para adicionar retencoes de uma conta a pagar
     * @param tituloJanela Titulo da janela
     * @param id Id da conta a pagar
     * @param tipoFilha Tipo: Imposto(1) ou Rateio(2)
     */
    retencoesContaPagar: function(tituloJanela, id, tipoFilha) {

        paramBox = {
            boxName: 'janela-retencoes-conta-pagar',
            url: baseUrl + "/default/janelas/retencoes-conta-pagar"
        };

        paramDialog = {
            title: tituloJanela,
            width: 960,
            height: 380
        }

        Janelas.abrir(paramBox, paramDialog, {
            'retencao[conta_pagar_id]': id,
            'retencao[tipo_filha]': tipoFilha
        });
    },
    /**
     * Abre uma janela para realizar uma inscricao em anuidade/revista
     * @param tituloJanela Titulo da janela
     * @param pessoaId Id da pessoa
     */
    inscricaoAnuidadeRevista: function(tituloJanela, pessoaId) {

        paramBox = {
            boxName: 'janela-inscricao-anuidade-revista',
            url: baseUrl + "/default/janelas/inscricao-anuidade-revista"
        };

        paramDialog = {
            title: tituloJanela,
            width: 600,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            'pessoa_id': pessoaId
        });
    },
    /**
     * Abre uma janela para realizar uma compensacao de cheque
     * @param tituloJanela Titulo da janela
     */
    listarChequesACompensar: function(tituloJanela) {

        paramBox = {
            boxName: 'janela-listar-cheques-a-compensar',
            url: baseUrl + "/default/janelas/listar-cheques-a-compensar"
        };

        paramDialog = {
            title: tituloJanela,
            width: 600,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {});
    },
    /**
     * Abre uma janela para realizar uma compensacao de cheque
     * @param contaReceberId Titulo da janela
     * @param urlBoleto Titulo da janela
     * @param entidadeId Id da entidade
     * @param tituloJanela Titulo da janela
     */
    updateControleLayout: function(contaReceberId, urlBoleto, entidadeId, tituloJanela) {

        paramBox = {
            boxName: 'janela-update-controle-layout',
            url: baseUrl + "/default/janelas/update-controle-layout"
        };

        paramDialog = {
            title: tituloJanela,
            width: 300,
            height: 210
        };

        Janelas.abrir(paramBox, paramDialog, {
            'contaReceberId': contaReceberId,
            'entidadeId': entidadeId,
            'urlBoleto': urlBoleto
        });
    },
    /**
     * Abri a janela para ver qual o codigo do recido a ser mostrado
     * @param contaReceberId
     * @retrun void
     */
    emitirReciboReceber: function(contaReceberId, tituloJanela) {

        paramBox = {
            boxName: 'janela-emitirReciboReceber',
            url: baseUrl + "/default/janelas/emitir-recibo-receber"
        };

        paramDialog = {
            title: tituloJanela,
            width: 300,
            height: 250
        };

        Janelas.abrir(paramBox, paramDialog, {
            'contaReceberId': contaReceberId
        });
    },
    /**
     * Abre a janela com o formulario para o cadastro de conta pra pessoa juridica
     * @param pessoaId
     * @param tituloJanela
     * @retrun void
     */
    cadastrarContaPj: function(pessoaId, tituloJanela) {

        paramBox = {
            boxName: 'janela-cadastrar-conta-pj',
            url: baseUrl + "/default/janelas/cadastrar-conta-pj"
        };

        paramDialog = {
            title: tituloJanela,
            width: 730,
            height: 380
        };

        Janelas.abrir(paramBox, paramDialog, {
            'pessoaId': pessoaId
        });
    },
    /**
     * Abre a janela com o formulario para a edicao de conta para pessoa juridica
     * @param pessoaId
     * @param contaBancariaId
     * @param tituloJanela
     * @retrun void
     */
    editarContaPj: function(pessoaId, contaBancariaId, tituloJanela) {

        paramBox = {
            boxName: 'janela-editar-conta-pj',
            url: baseUrl + "/default/janelas/editar-conta-pj"
        };

        paramDialog = {
            title: tituloJanela,
            width: 730,
            height: 380
        }

        Janelas.abrir(paramBox, paramDialog, {
            'pessoaId': pessoaId,
            'contaBancariaId': contaBancariaId
        });
    },
    loginEsqueciSenha: function(login, centroCusto, tituloJanela, urlLogin) {
        paramBox = {
            boxName: 'janela-login-esqueci-senha',
            url: baseUrl + "/default/janelas/login-esqueci-senha"
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 380
        };

        if (!urlLogin) {
            urlLogin = '';
        }

        Janelas.abrir(paramBox, paramDialog, {
            'login': login,
            'centroCusto': centroCusto,
            'urlLogin': urlLogin
        });
    },
    eventoParcelasInscricao: function(nossoNumero, layout, tituloJanela) {
        paramBox = {
            boxName: 'janela-evento-parcelas-inscricao',
            url: baseUrl + "/evento/inscricoes/parcelas"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 300
        }

        Janelas.abrir(paramBox, paramDialog, {
            'nosso_numero': nossoNumero,
            'layout': layout
        });
    },
    adicionarSubCategoriaTrabalho: function(tituloJanela, categoriaId, subCategoriaId) {
        paramBox = {
            boxName: 'janela-adicionar-sub-categoria-trabalho',
            url: baseUrl + "/default/categorias-trabalhos/adicionar-sub-categoria"
        };

        paramDialog = {
            title: tituloJanela,
            width: 400,
            height: 250
        }

        Janelas.abrir(paramBox, paramDialog, {
            'categoriaId': categoriaId,
            'subCategoriaId': subCategoriaId // No caso de edicao
        });
    },
    categoriaTrabalhoCentroCusto: function(tituloJanela, categoriaId) {
        paramBox = {
            boxName: 'janela-categoria-trabalho-centro-custo',
            url: baseUrl + "/default/categorias-trabalhos/centro-custo"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 250
        }

        Janelas.abrir(paramBox, paramDialog, {
            'categoriaId': categoriaId
        });
    },
    formaTrabalhoCentroCusto: function(tituloJanela, formaId) {
        paramBox = {
            boxName: 'janela-forma-trabalho-centro-custo',
            url: baseUrl + "/default/formas-trabalhos/centro-custo"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 250
        }

        Janelas.abrir(paramBox, paramDialog, {
            'formaId': formaId
        });
    },
    grupoTrabalhoCentroCusto: function(tituloJanela, grupoId) {
        paramBox = {
            boxName: 'janela-grupo-trabalho-centro-custo',
            url: baseUrl + "/grupos-trabalhos/centro-custo"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 250
        }

        Janelas.abrir(paramBox, paramDialog, {
            'grupoId': grupoId
        });
    },
    editarTrabalhosRecebidos: function(tituloJanela, trabalhos) {
        paramBox = {
            boxName: 'janela-editar-trabalhos-recebidos',
            url: baseUrl + "/trabalho/recebimentos/editar"
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 500
        };

        Janelas.abrir(paramBox, paramDialog, {
            'trabalhos': trabalhos
        });
    },
    inscreverPessoa: function(pessoaId, centroCustoId) {
        var iframe = '<iframe src="/default/janelas/inscrever-pessoa?pessoa_id=' + pessoaId + '&centro_custo_id=' + centroCustoId + '" id="frame_inscrever" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" title="Inscrever"></iframe>';

        $("#dialog_inscrever").dialog("open");
        $("#dialog_inscrever").html(iframe);

        return false;
    },
    pagarNovamente: function(numero, layout, cbc, tituloJanela) {
        paramBox = {
            boxName: 'janela-pagar-novamente',
            url: baseUrl + "/inscricao/inscricoes/pagar-novamente"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 310
        };

        Janelas.abrir(paramBox, paramDialog, {
            'numero': numero,
            'layout': layout,
            'cbc': cbc
        });
    },
    pagarNovamentePadrao: function(numero, layout, cbc, tituloJanela, modulo) {
        paramBox = {
            boxName: 'janela-pagar-novamente',
            url: baseUrl + "/" + modulo + "/inscricoes/pagar-novamente"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 310
        }

        Janelas.abrir(paramBox, paramDialog, {
            'numero': numero,
            'layout': layout,
            'cbc': cbc
        });
    },
    pagarNovamenteAssociado: function(numero, layout, cbc, tituloJanela) {
        paramBox = {
            boxName: 'janela-pagar-novamente',
            url: baseUrl + "/associado/anuidades/pagar-novamente"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 310
        }

        Janelas.abrir(paramBox, paramDialog, {
            'numero': numero,
            'layout': layout,
            'cbc': cbc
        });
    },
    atualizarEmailPessoa: function(pessoaId, tituloJanela) {
        paramBox = {
            boxName: 'janela-atualizar-email-pessoa',
            url: baseUrl + "/default/janelas/atualizar-email-pessoa"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 150
        };

        Janelas.abrir(paramBox, paramDialog, {
            'pessoa_id': pessoaId
        });
    },
    atualizarEmailAvaliador: function(pessoaId, tituloJanela) {
        paramBox = {
            boxName: 'janela-atualizar-email-avaliador',
            url: baseUrl + "/avaliadores/atualizar-email-avaliador"
        };

        paramDialog = {
            title: tituloJanela,
            width: 500,
            height: 150
        };

        Janelas.abrir(paramBox, paramDialog, {
            'pessoa_id': pessoaId
        });
    },
    listagemTrabalhosAvaliador: function(avaliadorCentroCustoId, tituloJanela) {
        paramBox = {
            boxName: 'janela-listagem-trabalhos-avaliador',
            url: baseUrl + "/trabalhos/listagem-trabalhos-avaliador"
        };

        paramDialog = {
            title: tituloJanela,
            width: 1000,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {
            'avaliador_centro_custo_id': avaliadorCentroCustoId
        });
    },
    listagemTrabalhoDetalhes: function(trabalhoId, tituloJanela) {

        paramBox = {
            boxName: 'janela-listagem-trabalho-detalhe',
            url: baseUrl + "/utilidades/listagem-trabalho-detalhes"
        };

        paramDialog = {
            title: tituloJanela,
            width: 600,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            'trabalho_id': trabalhoId
        });
    },
    selectionarFrequenciaPainel: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-painel',
            url: baseUrl + "/default/janelas/selecionar-frequencia-painel"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selectionarFrequenciaGrade: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-grade',
            url: baseUrl + "/default/janelas/selecionar-frequencia-grade"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selectionarFrequenciaAtividade: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-grade',
            url: baseUrl + "/default/janelas/selecionar-frequencia-atividade"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    selecionarCaex: function(tituloJanela, retorno) {

        paramBox = {
            boxName: 'janela-selecionar-caex',
            url: baseUrl + "/default/janelas/selecionar-caex"
        };

        paramDialog = {
            title: tituloJanela,
            width: 620,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            retorno: retorno
        });

    },
    dadosDuplicadoPessoa: function(pessoaIds, cpf, email, acao, tituloJanela) {
        paramBox = {
            boxName: 'dados-duplicado-pessoa',
            url: baseUrl + "/default/janelas/dados-duplicado-pessoa"
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 380
        }

        Janelas.abrir(paramBox, paramDialog, {
            'ids': pessoaIds,
            'cpf': cpf,
            'email': email,
            'acao': acao
        });
    },
    datalhesVoucher: function(id, tituloJanela) {
        paramBox = {
            boxName: 'detalhes-voucher',
            url: baseUrl + "/default/janelas/detalhes-voucher"
        };

        paramDialog = {
            title: tituloJanela,
            width: 400,
            height: 200
        }

        Janelas.abrir(paramBox, paramDialog, {
            id: id
        });
    },
    aplicarVoucher: function(inscricao_id, tituloJanela) {
        paramBox = {
            boxName: 'aplicar-voucher',
            url: baseUrl + "/default/voucher/aplicar"
        };

        paramDialog = {
            title: tituloJanela,
            width: 400,
            height: 115
        };

        Janelas.abrir(paramBox, paramDialog, {
            inscricao_id: inscricao_id
        });
    },
    verProcessoDetalhado: function(processo_id, tipo_usuario, tituloJanela) {

        paramBox = {
            boxName: 'utilidades-ver-processo-detalhado',
            url: baseUrl + "/icase/utilidades/ver-processo-detalhado"
        };

        paramDialog = {
            title: tituloJanela,
            width: 800,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            'processo_id': processo_id,
            'tipo_usuario': tipo_usuario //usuario 0 é usuario empresa - nao exibe o botao tramitar;  1 é usuario iCase - exibe o botao tramitar
        });
    },
    tramitarProcesso: function(processo_id, processo_equipamento_id, fase_ts_id, tituloJanela) {

        paramBox = {
            boxName: 'utilidades-tramitar-processo',
            url: baseUrl + "/icase/utilidades/tramitar-processo"
        };

        paramDialog = {
            title: tituloJanela,
            width: 700,
            height: 400,
        }

        Janelas.abrir(paramBox, paramDialog, {
            'processo_id': processo_id,
            'fase_ts_id': fase_ts_id,
            'processo_equipamento_id': processo_equipamento_id
        });
    },
    solicitarSelos: function(processo_id, tituloJanela) {

        paramBox = {
            boxName: 'utilidades-solicitar-selos',
            url: baseUrl + "/icase/utilidades/solicitar-selos"
        };

        paramDialog = {
            title: tituloJanela,
            width: 800,
            height: 400
        }

        Janelas.abrir(paramBox, paramDialog, {
            'processo_id': processo_id
        });
    },
    acompanharSolicitacaoSelos: function(solicitacao_selo_id, tituloJanela) {

        paramBox = {
            boxName: 'utilidades-acompanhar-solicitacao-selos',
            url: baseUrl + "/icase/utilidades/acompanhar-solicitacao-selos"
        };

        paramDialog = {
            title: tituloJanela,
            width: 800,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {
            'solicitacao_selo_id': solicitacao_selo_id
        });
    },
    setInstrucaoBoleto: function(tituloJanela, conta_receber_id, link) {
        paramBox = {
            boxName: 'utilidades-set-inscrucao-boleto',
            url: baseUrl + "/utilidades/set-instrucao-boleto"
        };

        paramDialog = {
            title: tituloJanela,
            width: 800,
            height: 250
        };

        Janelas.abrir(paramBox, paramDialog, {
            'conta_receber_id': conta_receber_id,
            'url': link
        });
    },
    gerarCapaOrContraCapa: function(trabalho_id, trabalho_fase, acao, tituloJanela) {
        paramBox = {
            boxName: 'trabalhos-gerar-capa-or-contra-capa',
            url: baseUrl + "/trabalhos/gerar-capa-or-contra-capa"
        };

        paramDialog = {
            title: tituloJanela,
            width: 800,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, {
            'trabalho_id': trabalho_id,
            'trabalho_fase': trabalho_fase,
            'acao': acao
        });
    },
    gerarCertificados: function(url, boxName, params, tituloJanela) {
        paramBox = {
            boxName: boxName,
            url: baseUrl + url
        };

        paramDialog = {
            title: tituloJanela,
            width: 800,
            height: 400
        };

        Janelas.abrir(paramBox, paramDialog, params);
    }
}