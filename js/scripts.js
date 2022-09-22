var App = function () {

	// ---------------------
	// VALIDA CPF
	// ---------------------
	var validaCPF = function (cpf) {

		cpf = cpf.replace(".", "");
		cpf = cpf.replace(".", "");
		cpf = cpf.replace(".", "");
		cpf = cpf.replace("-", "");

		if (cpf.length != 11 ||
			cpf == "00000000000" ||
			cpf == "11111111111" ||
			cpf == "22222222222" ||
			cpf == "33333333333" ||
			cpf == "44444444444" ||
			cpf == "55555555555" ||
			cpf == "66666666666" ||
			cpf == "77777777777" ||
			cpf == "88888888888" ||
			cpf == "99999999999")
			return false;

		add = 0;
		for (i = 0; i < 9; i++)
			add += parseInt(cpf.charAt(i)) * (10 - i);
		rev = 11 - (add % 11);
		if (rev == 10 || rev == 11)
			rev = 0;
		if (rev != parseInt(cpf.charAt(9)))
			return false;
		add = 0;
		for (i = 0; i < 10; i++)
			add += parseInt(cpf.charAt(i)) * (11 - i);
		rev = 11 - (add % 11);
		if (rev == 10 || rev == 11)
			rev = 0;
		if (rev != parseInt(cpf.charAt(10)))
			return false;
		return true;
	}

	/* ====================
	GERENCIA PAGINAÇÃO
	====================== */
	var handlePaginacao = function () {

		$('.page-link').bind('click', function (e) {

			e.preventDefault();

			var page = $(this).attr('data-page');
			var form = $('.form-busca-lista');
			var acaoField = form.find('.acao');
			var pageField = form.find('#numero-pagina');

			if (acaoField) {
				acaoField.val('busca');
			}

			pageField.val(page);
			form.submit();

		});

	}


	// ---------------------
	// ESCONDE LIGHTBOX
	// ---------------------
	var hideLightbox = function () {

		$('.lightbox').animate({
			marginTop: 0,
			top: '-50%',
			opacity: 0
		}, 100, 'linear').fadeOut(250);

		$('#overlay-lightbox').fadeOut(250);

	}

	// --------------------
	// MOSTRA LIGHTBOX
	// --------------------
	var showLightbox = function (target) {

		$(target).fadeIn(250);
		$('#overlay-lightbox').fadeIn(250);

		$(target).animate({
			'top': '35%',
			'marginTop': -($(target).height() / 2.5) + 'px',
			opacity: 1
		}, 100, 'linear');

	}

	// -----------------------
	// CONTROLA #LIGHTBOX
	// -----------------------
	var handleLightbox = function () {

		// adicionando a sombra no corpo da página 
		var overlay = '<div id="overlay-lightbox"></div>';
		$(overlay).appendTo('body').hide();
		$('#overlay-lightbox').css({
			'opacity': 0.7
		});

		$('.link-lightbox').on('click', function (e) {

			e.preventDefault();

			var lightboxID = '#' + $(this).attr('data-rel');

			// target
			showLightbox(lightboxID);

		}); // click

		// escondendo o lightbox e a sombra ao clicar em botões específicos
		$(document).on('click', '#overlay-lightbox, .close-lightbox', function (e) {

			e.preventDefault();

			if ($('.lightbox').is(':visible') && !$('.lightbox').hasClass('lightbox-impressao')) {
				hideLightbox();
			}

		});

	}

	/*var delWithLightbox = function(elemID, msgText, btnID, titleText, parentElem) {

	}
*/

	/* ------------------
	// #ATUALIZA SELECT
	--------------------- */
	var atualizaSelect = function (action, target) {

		/*$.post( '', {acao: action},  function( response ) { // busca dados no BD

		// se houver resposta popula o select com os dados encontrados
		if(response)
		target.html('<option value="0">Selecione</option>' + response);

		});	*/

	}


	// ---------------------------
	// RENOMEIA CAMPOS DE EMAIL
	// ---------------------------
	var renomeiaCampos = function (box, text) {

		box.each(function (i, v) {
			i = i + 1;
			$(this).find('label').text(text + i);
		});

	}


	// ------------------------------------------------------
	// APLICA ZEBRA NAS CAIXAS DE OBSERVAÇÕES (#ADVOGADOS)
	// ------------------------------------------------------
	var zebraObs = function () {
		$('.box-obs:odd, .box-andamento:odd').css('backgroundColor', '#fff');
		$('.box-obs:even, .box-andamento:even').css('backgroundColor', '#f7f7f7');
	}


	// -----------------
	// FUNÇÕES GERAIS
	// -----------------
	var handleGeneral = function () {

		// RELATÓRIOS
		$('.sel-relatorio').on('change', function () {
			var opcao = $(this).find('option:selected');

			if (!opcao.hasClass('opt-cores')) {
				$('.hidden-field').stop(true, true).slideDown(150);
			} else {
				$('.hidden-field').stop(true, true).slideDown(150);
				$('.hidden-periodo').stop(true, true).slideUp(150);
			}

		});


		// FORM CADASTRO PROCESSO	
		$('.form-cadastro-processo').on('submit', function () {
			$('.loading-mask').fadeIn(200);
		});


		// ---------------------------------
		// REPOPULA SELECT DE SECRETARIA
		// ---------------------------------
		$('.sel-estado').on('change', function () {

			var estado = $(this).val();
			var selLightbox = $('#form-secretaria-lightbox').find('select[name=estado_secretaria]');
			var btnAddSecretaria = $('.add-secretaria');
			var hiddenEstadoLightbox = $('input[name=estado_lightbox]');
			var selJornal = false;

			if ($(this).hasClass('sel-estado-jornal')) {
				selJornal = true;
			} else {
				selJornal = false;
			}

			if ($(this).val() == '0') {
				btnAddSecretaria.hide();
			} else {
				btnAddSecretaria.show();
			}
			selLightbox.val(estado).change();
			hiddenEstadoLightbox.val(estado);

			$.post('ajax-popula-secretaria.php', {
				item_id: estado
			}, function (response) {

				if (response && selJornal) {
					$('#sel-secretaria').html('<option value="0">Selecione</option>' + response);
				} else if (response && !selJornal) {
					$('#sel-secretaria').html(response);
				} else {
					$('#sel-secretaria').html('<option value="0">Selecione</option>');
				}

			});

		});


		// --------------------------------------------------
		// MOSTRA SUBMENU AO TOPO (OPÇÃO DE SAIR DO SISTEMA)
		// --------------------------------------------------
		$('.faixa-admin .box-admin').on('click', function () {

			var lista = $(this).find('ol');
			var seta = $(this).find('.seta');

			//lista.stop(true, true).slideToggle('fast');

			if (lista.is(':visible')) {
				seta.removeClass('seta-baixo');
				seta.addClass('seta-frente');
				lista.stop(true, true).slideUp('fast');
			} else {
				seta.removeClass('seta-frente');
				seta.addClass('seta-baixo');
				lista.stop(true, true).slideDown('fast');
			}

		});


		// --------------------------------------------------------------------
		// VALIDA CPF / CNPJ AO SUBMETER FORMS QUE CONTÉM ESSES CAMPOS
		// --------------------------------------------------------------------
		$('form').on('submit', function () {

			var campoCPF = $('.cpf-input');
			var campoCNPJ = $('.cnpj-input');

			if (campoCPF.is(':visible') && !validaCPF(campoCPF.val())) {
				alert('Digite um número de CPF válido.');
				campoCPF.focus();
				return false;
			}
			return true;

		});



		// -------------------------------
		// MARCA / DESMARCA NÍVEL
		// -------------------------------
		$('.nivel-check').on('click', function () {

			var parentBox = $(this).closest('.nivel-check-box');
			parentBox.find('.nivel-check').not($(this)).attr('checked', false);

		});


		// -----------------
		// GERA EXCEL
		// -----------------
		$('.excel-item').on('click', function (e) {

			e.preventDefault();
			$('#form-excel').submit();

		});


		// -------------------------------------
		// MUDA #SINALIZAÇÃO DO PROCESSO
		// -------------------------------------
		$('.muda-sinalizacao-btn').on('click', function (e) {

			var form = $('#form-altera-status');
			var acao = $(this).attr('data-acao');
			var confirmacao = confirm($(this).attr('data-msg'));
			e.preventDefault();

			if (confirmacao) {
				form.find('input[name=acao]').val(acao);
				form.submit();
			}

		});


		// -------------------------------
		// FIXED SIDEBAR
		// -------------------------------

		var $sidebar = $("#sidebar"),
			$window = $(window),
			offset = $sidebar.offset(),
			topPadding = 15;

		$window.scroll(function () {

			if ($window.scrollTop() > offset.top) {
				$sidebar.stop().animate({
					marginTop: $window.scrollTop() - offset.top
				});

			} else {
				$sidebar.stop().animate({
					marginTop: 0
				});
			}

		});



		// --------------------------------------
		// CADASTRA #ADVOGADO (LIGHTBOX)
		// --------------------------------------

		var existeAdv = false;

		// QUANDO CLICAR NO CHECKBOX DE AVOGADO
		$('.check-advogado').on('click', function () {

			var campoAdv = $('input[name=nome_advogado]');

			// VERIFICA SE NOME JÁ EXISTE	NO BANCO DE DADOS
			$.post('ajax-cadastro-advogado.php', {
				nome_advogado: campoAdv.val()
			}, function (response) {

				// SE EXISTIR 
				if (response == 'ja existe') {
					existeAdv = true;
					$('.warning-box').show('fast').find('.erro').text('OAB já existe no sistema.');
				}

				// SE NÃO EXISTIR
				else {
					existeAdv = false;
					$('.warning-box').hide('fast').find('.erro').text('');
				}

			});

		});

		// AO CLICAR PARA CADASTRAR
		$('#btn-cadastra-advogado').on('click', function (e) {

			e.preventDefault();

			$('.warning-box').hide('fast').find('.erro').text('');
			var campos = $(this).closest('form').serialize();
			var lightbox = $(this).closest('.lightbox');
			var erro = false;

			// VALIDA OS CAMPOS	
			var form = $('#form-advogado-lightbox'),
				inputAdvId = $('#advogado-id'),
				inputAdvNome = $('.advogado-field'),

				nomeAdv = $('input[name=nome_advogado]'),
				emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
				oabAdv = $('input[name=oab_advogado]'),

				// inputText = form.find('input[type=text]'),	
				warningBox = form.find('.warning-box'),
				erro;

			// VALIDA CAMPO ADVOGADO
			if (!erro && nomeAdv.val() == '') {
				warningBox.show('fast').find('.erro').text('Preencha o nome do Advogado.');
				erro = true;
			}

			// VALIDA OAB ADVOGADO
			if (!erro && oabAdv.val() == '') {
				warningBox.show('fast').find('.erro').text('Preencha o OAB do Advogado.');
				erro = true;
			}

			// VALIDA 'existeadv'
			if (!erro && existeAdv) {
				warningBox.show('fast').find('.erro').text('OAB já existe no sistema.');
				erro = true;
			}

			// VALIDA E-MAILs
			if (!erro) {
				$("#form-advogado-lightbox .email-advogado").each(function () {

					var email_val = $(this).val();

					if (email_val == "") {
						warningBox.show('fast').find('.erro').text('Preencha o e-mail do Advogado.');
						erro = true;
					}
					if (!emailReg.test(email_val)) {
						warningBox.show('fast').find('.erro').text('Preencha um e-mail válido.');
						erro = true;
					}

				});

			}

			// VALIDA TELEFONES
			if (!erro) {
				$("#form-advogado-lightbox .telefone-advogado").each(function () {

					var telefone_val = $(this).val();

					if (telefone_val == "") {
						warningBox.show('fast').find('.erro').text('Preencha o telefone do Advogado.');
						erro = true;
					}

				});

			}

			// SE NÃO HOUVER NENHUM ERRO EXECUTA O POST
			if (!erro) {

				$.post('ajax-cadastro-advogado.php', campos, function (response) {

					if (response.msg == 'sucesso') {
						hideLightbox(lightbox);
						form.find('input[type=text]').val('');

						$('.dado-advogado').addClass('dado-arquivo--sucesso').text('Advogado cadastrado com sucesso.');
						inputAdvNome.val(response.adv_nome); // preenche o campo com o nome do avogado cadastrado
						inputAdvId.val(response.adv_id); // preenche o hidden com  o ID do avogado cadastrado
					} else if (response.msg == 'ja existe') {
						warningBox.show('fast').find('.erro').text('OAB já existe no sistema.');
					}

				}, 'json');

			}

		});



		// --------------------------------------------
		// CADASTRA #SECRETARIA/FÓRUM (LIGHTBOX)
		// --------------------------------------------

		var existeSec = false;

		$('.check-secretaria').on('click', function () {

			var campoSec = $('input[name=nome_secretaria]');

			// verifica se nome já existe	
			$.post('ajax-cadastro-secretaria.php', {
				nome_secretaria: campoSec.val()
			}, function (response) {

				if (response == 'ja existe') {
					existeSec = true;
					$('.warning-box').show('fast').find('.erro').text('Secretaria/Fórum já existe no sistema.');
				} else {
					existeSec = false;
					$('.warning-box').hide('fast').find('.erro').text('');
				}

			});

		});


		// CLICAR NO BOTÃO DE CADASTRO	
		$('#btn-cadastra-secretaria').on('click', function () {

			// VALIDAÇÃO DE CAMPOS	
			var form = $('#form-secretaria-lightbox'),
				inputText = form.find('input[type=text]'),
				nomeSecretaria = form.find('input[name=nome_secretaria]'),
				sel = form.find('select'),
				warningBox = form.find('.warning-box'),
				erro, erro2;

			inputText.each(function () {
				if ($(this).val() == '') {
					erro = true;
				} else {
					erro = false;
				}
			});

			sel.each(function () {
				if ($(this).val() == '' || $(this).val() == '0') {
					erro2 = true;
				} else {
					erro2 = false;
				}
			});

			// se houver erro
			if (erro || erro2) {
				warningBox.show('fast').find('.erro').text('Preencha todos os campos.');
			}

			// se houver erro
			else if (existeSec) {
				warningBox.show('fast').find('.erro').text('Secretaria/Fórum já existe no sistema.');
			}

			// se não...
			else {
				warningBox.hide('fast');
				var sel = $('#sel-secretaria');
				ajaxCadastraPopula($(this), sel, 'ajax-cadastro-secretaria.php', 'ajax-popula-secretaria.php', $('.sel-estado').val());

			}

		});


		// ------------------------------
		// CADASTRA #JORNAL (LIGHTBOX)
		// ------------------------------

		var existeJor = false;

		$('.check-jornal').on('click', function () {

			var campoJor = $('input[name=nome_jornal]');

			// verifica se nome já existe	
			$.post('ajax-cadastro-jornal.php', {
				nome_jornal: campoJor.val()
			}, function (response) {

				if (response == 'ja existe') {
					existeJor = true;
					$('.warning-box').show('fast').find('.erro').text('Jornal já existe no sistema.');
				} else {
					existeJor = false;
					$('.warning-box').hide('fast').find('.erro').text('');
				}

			});

		});

		$('#btn-cadastra-jornal').on('click', function () {

			// VALIDAÇÃO DE CAMPOS	
			var form = $('#form-jornal-lightbox'),
				inputText = form.find('input[type=text]'),
				sel = form.find('#sel-status-jornal'),
				//selSec = form.find('#sel-secretaria-jornal option:selected'),	
				warningBox = form.find('.warning-box'),
				erro, erro2;

			inputText.each(function () {
				if ($(this).val() == '') {
					erro = true;
				} else {
					erro = false;
				}
			});

			sel.each(function () {
				if ($(this).val() == '' || $(this).val() == '0') {
					erro2 = true;
				} else {
					erro2 = false;
				}
			});

			// se houver erro
			if (erro || erro2) {
				warningBox.show('fast').find('.erro').text('Preencha todos os campos.');
			}

			// se existir jornal, mostra msg
			else if (existeJor) {
				warningBox.show('fast').find('.erro').text('Jornal já existe no sistema.');
			}

			// se não...
			else {
				warningBox.hide('fast');
				var sel = $('#sel-jornal'); // target
				ajaxCadastraPopula($(this), sel, 'ajax-cadastro-jornal.php', 'ajax-popula-jornal.php', $('#sel-secretaria').val());
			}

		});



		// ----------------------------------------------------------------------------
		// BOTÃO LIGHTBOX PARA CADASTRO DE JORNAL (TELA DE #PROCESSOS / #PROPOSTAS)
		// ----------------------------------------------------------------------------
		$(document).on('click', '.btn-lightbox-jornal', function () {

			var secretariaAlvo = $('#sel-secretaria').val(); // select da tela
			var secretariaAlvoHTML = $('#sel-secretaria').html();

			$('#sel-secretaria-jornal').html(secretariaAlvoHTML).val(secretariaAlvo).change();
			//$('#sel-secretaria-jornal') // select do lightbox
			$('#hidden-secretaria-jornal').val(secretariaAlvo); // campo hidden do lightbox

		});


		// ----------------------------------------------------------------------------
		// BOTÃO LIGHTBOX PARA ADIÇÃO DE JORNAL POR CIDADE (TELA DE #PROCESSOS )
		// ----------------------------------------------------------------------------
		$(document).on('click', '#btn-adiciona-jornal', function () {

			var selJornal = $('#sel-jornal').val(); // select da tela
			lightbox = $(this).closest('.lightbox'),
				jornalID = lightbox.find('.jornal-id'),
				jornalNome = lightbox.find('.jornal-nome'),
				opcao = $('#sel-jornal').find('option'),
				count = 0;

			// OPCOES NO SELECT
			// SE JÁ HOUVER A OPÇÃO SELECIONADA NO SELECT DE JORNAIS, SOMA AO CONTADOR.
			opcao.each(function () {
				if ($(this).val() == jornalID.val()) {
					count++;
				} else {
					count = 0;
				}
			});


			// SE O CONTADOR FOR ZERO, OU SEJA, SE NENHUMA OPÇÃO NO SELECT FOR IGUAL
			// AO QUE O USUÁRIO ESCOLHEU, ADICIONA A OPÇÃO ESCOLHIDA E ESCONDE O LIGHTBOX
			if (count == 0) {
				hideLightbox(lightbox);
				$('#sel-jornal')
					.append('<option value="' + jornalID.val() + '">' + jornalNome.val() + '</option>');
				$('#sel-jornal').find('option:last').attr('selected', 'selected');
				$('#sel-jornal').change(); // change
			}

			// SE JÁ HOUVER A OPÇÃO NO SELECT, EXIBIR AVISO.
			else {
				alert('Jornal já incluso na lista.');
			}

		});


		// -------------------------------
		// FUNÇÃO PARA POPULA SELECTS
		// -------------------------------
		function ajaxCadastraPopula(elem, target, url1, url2, param2) {

			var form = elem.closest('form'),
				campos = form.serialize(),
				lightbox = elem.closest('.lightbox');

			$.post(url1, campos, function (response) { // POST PARA CADASTRO

				var secId = response.sec_id;
				var jorId = response.jornal_id;

				// ERRO CADASTRO SECRETARIA	
				if (elem.attr('id') == 'btn-cadastra-secretaria' && response.msg == 'ja existe') {
					//form.find('.warnng-box').show('fast').find('.erro').text('Secretaria/Fórum já existe no sistema.');	
					hideLightbox(lightbox); // ESCONDE LIGHTBOX
					$('.dado-secretaria')
						.text('Secretaria/Fórum existente no sistema, salve o processo para corrigi-lo.');

					// REPOPULA SELECT APÓS MEIO SEGUNDO
					setTimeout(function () {

						$.post(url2, {
							item_id: param2
						}, function (responseSel) { // POST PARA POPULAR SELECT-ALVO

							if (elem.attr('id') == 'btn-cadastra-secretaria' && responseSel) {
								form.find('input[type=text]').val('');
								form.find('select').val('0').change();

								//	
								target.html(responseSel);
								$('#sel-secretaria').val(secId).change();

							}

						});

					}, 200);
				}

				// ERRO CADASTRO JORNAL	
				else if (elem.attr('id') == 'btn-cadastra-jornal' && response.msg == 'ja existe') {
					form.find('.warning-box').show('fast').find('.erro').text('Jornal já existe no sistema.');
				}

				// SUCESSO SECRETARIA OU JORNAL				
				else if (response.msg == 'sucesso') {

					hideLightbox(lightbox); // ESCONDE LIGHTBOX

					// SUCESSO CADASTRO SECRETARIA
					if (elem.attr('id') == 'btn-cadastra-secretaria') {
						$('.dado-secretaria')
							.addClass('dado-arquivo--sucesso')
							.text('Secretaria/Fórum cadastrado com sucesso.');
					}

					// SUCESSO CADASTRO JORNAL
					else if (elem.attr('id') == 'btn-cadastra-jornal') {
						$('.dado-jornal')
							.addClass('dado-arquivo--sucesso');
						$('.dado-jornal a:last').after('<span>&nbsp; &nbsp; Jornal cadastrado com sucesso.</span>');
					}

					// REPOPULA SELECT APÓS MEIO SEGUNDO
					setTimeout(function () {

						$.post(url2, {
							item_id: param2
						}, function (responseSel) { // POST PARA POPULAR SELECT-ALVO

							if (elem.attr('id') == 'btn-cadastra-secretaria' && responseSel) {
								form.find('input[type=text]').val('');
								form.find('select').val('0').change();

								//	
								target.html('<option value="0">Selecione</option>' + responseSel);
								$('#sel-secretaria').val(secId).change();

							} else if (elem.attr('id') == 'btn-cadastra-jornal' && responseSel) {
								form.find('input[type=text]').val('');
								form.find('select').val('0').change();

								//
								target.html('<option value="0">Selecione</option>' + responseSel);
								$('#sel-jornal').val(jorId).change();

							} else {
								form.find('input[type=text]').val('');
								form.find('select').val('0').change();
							}

						});

					}, 200);

				}

			}, 'json');

		}


		// ----------------------------------------------------------------
		// REPOPULA SELECT DE #JORNAIS AO TROCAR SELECT #SECRETARIA
		// ----------------------------------------------------------------	
		$('#sel-secretaria').on('change', function () {

			$.post('ajax-popula-jornal.php', {
				item_id: $(this).val()
			}, function (response) {

				var retorno = response.trim();

				if (retorno) {
					$('#sel-jornal').html('<option value="0">Selecione</option>' + retorno).change();
					$('#sel-jornal-padrao').html('<option value="0">Selecione</option>' + retorno).change();
					$('.wrapper-campo-jornal, .panel-jornal').fadeIn('fast');
					//$('.btn-lightbox-jornal').hide();
				} else if (retorno == '') {
					$('#sel-jornal').html('<option value="0">Selecione</option>' + retorno).change();
					$('#sel-jornal-padrao').html('<option value="0">Selecione</option>' + retorno).change();
					$('.wrapper-campo-jornal, .panel-jornal').fadeIn('fast');
					$('.btn-lightbox-jornal').show();
				}

			});

		});


		// ------------------------------------------------------------------------------------------
		// CAPTURA DADOS DO JORNAL AO TROCAR SELECT (PÁGINA DE GERENCIAMENTO DE PROPOSTA)
		// -------------------------------------------------------------------------------------------	
		$(document).on('change', '#sel-jornal', function () {

			var jornalID = $(this).val();
			var jornalAux = $('input[name=jornal-id-aux]');

			// BOXES DOS JORNAIS
			var boxJornal = $('.box-jornal');
			var boxDJE = $('.box-dje');

			// QUANTIDADES DOS JORNAIS
			var qtdField = boxJornal.find('.qtd-jornal');
			var qtdFieldDJE = boxDJE.find('.qtd-dje');

			// VALORES DOS JORNAIS
			var valField = boxJornal.find('.valor-jornal');
			var valFieldDJE = boxDJE.find('.valor-dje-padrao');
			var valDJE = boxDJE.find('.valor-dje');

			// VALORES FINAIS
			var valFinalField = boxJornal.find('.valor-final-jornal');
			var valFinalFieldDJE = boxDJE.find('.valor-dje-final');

			$.post('ajax-valor-jornal.php', {
				jornal_id: jornalID
			}, function (response) { // pega valores do jornal

				//if( response ) {

				$('.box-jornal').fadeIn('fast'); // mostra box jornal 

				if (jornalID == '0') {
					qtdField.val('2'); // coloca a quantidade
					valField.val('0'); // coloca o valor padrão
					valFinalField.val('0'); // coloca o valor final
					jornalAux.val('0');
				} else {

					// altera valores do jornal
					qtdField.val('2'); // coloca a quantidade
					valField.val(response.valor_padrao); // coloca o valor padrão
					valFinalField.val(response.valor_padrao); // coloca o valor final
				}

				// se houver box DJE, também altera os campos do DJE
				if (boxDJE.is(':visible') && jornalID != '0') {

					qtdFieldDJE.val('1'); // coloca a quantidade no DJE
					valFieldDJE.val(response.valor_padrao); // coloca o valor padrão no DJE
					valDJE.val(response.valor_dje);

					var result = parseFloat(response.valor_padrao.replace('.', '').replace(',', '')) + parseFloat(response.valor_dje.replace('.', '').replace(',', ''));
					valFinalFieldDJE.val(result); // coloca o valor final no DJE

				} else {
					qtdFieldDJE.val('1'); // coloca a quantidade no DJE
					valFieldDJE.val('0'); // coloca o valor padrão no DJE
					valDJE.val('0');
					valFinalFieldDJE.val('0');
				}

				handleMasks();

				//}

			}, 'json');

		});

		// CARREGA DADOS DE SUBSTATUS CONFORME SELECAO DE STATUS NO CADASTRO DE ACOMPANHAMENTO
		$('#acomp-status').on('change', function () {

			var statusID = $(this).val();

			$.post('ajax-popula-substatus.php', {
				status_codigo: statusID
			}, function (response) {

				if (response) {
					$('#acomp-substatus').html(response);
				}

			});

		});

		// ------------
		// BUSCA #CEP
		// ------------

		$(document).on('click', '.busca-cep-btn', function (e) {

			e.preventDefault();
			var cep = $(this).prev('.cep-input').val().replace(/\D/g, '');
			var loadingIco = $('.loading-ico');
			var logradouro = '',
				numero = '',
				bairro = '',
				cidade = '',
				estado = '';

			// mostra loading ao clicar no botão de CEP
			loadingIco.show();

			//Verifica se campo cep possui valor informado.
			if (cep != "") {

				//Expressão regular para validar o CEP.
				var validacep = /^[0-9]{8}$/;

				//Valida o formato do CEP.
				if (validacep.test(cep)) {

					//Consulta o webservice viacep.com.br/
					$.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

						loadingIco.hide();

						if (!("erro" in dados)) {
							//Atualiza os campos com os valores da consulta.
							$('#logradouro').val(dados.logradouro);
							$('#numero').val('');
							$('#bairro').val(dados.bairro);
							$('#cidade').val(dados.localidade);
							$('#estado').val(dados.uf).change();
						} //end if.
						else {
							//CEP pesquisado não foi encontrado.
							$('#logradouro').val('');
							$('#numero').val('');
							$('#bairro').val('');
							$('#cidade').val('');
							$('#estado').val('').change();
						}
					});
				} //end if.
				else {
					loadingIco.hide();
					//cep é inválido.
					logradouro = '';
					numero = '';
					bairro = '';
					cidade = '';
					estado = '';
					alert("Formato de CEP inválido.");
				}
			} //end if.
			else {
				loadingIco.hide();
				//cep sem valor, limpa formulário.
				logradouro = '';
				numero = '';
				bairro = '';
				cidade = '';
				estado = '';
			}
			$('#logradouro').val('');
			$('#numero').val('');
			$('#bairro').val('');
			$('#cidade').val('');
			$('#estado').val('').change();
		});

		// -----------------------------------
		// QUADRO DE #ALERTAS COM ACCORDION
		// -----------------------------------
		$('.panel-accordion > .panel-title').bind('click', function (e) {

			var panelContent = $(this).next('.panel-content');
			var seta = $(this).find('.seta');

			if (e.target === this) {

				panelContent.stop(true, true).slideToggle('fast');

				if (seta.hasClass('seta-baixo')) {
					seta.removeClass('seta-baixo').addClass('seta-frente');
				} else {
					seta.removeClass('seta-frente').addClass('seta-baixo');
				}

			}

		});


		// ----------------------------------------
		// MOSTRA/ESCONDE BOTÃO VOLTAR AO TOPO 
		// ---------------------------------------- */

		$(document).ready(function () {

			$('.back-top').hide();

			$(window).bind('scroll', function () { // QUANDO ROLAR A PÁGINA
				var topo = $(this).scrollTop();
				if (topo >= 180)
					$('.back-top').show();
				else
					$('.back-top').hide();
			});

			$('.back-top').bind('click', function () { // QUANDO CLICAR NO BOTÃO PARA VOLTAR AO TOPO
				$('body, html').stop(true, true).animate({
					scrollTop: 0
				}, 150, 'easeOutQuad');
			});

		});




		// -----------------------
		// ADICIONA #OBSERVAÇÕES 
		// -----------------------

		zebraObs(); // zebra observações

		// MOSTRA BOTÃO PARA ADICIONAR OBSERVAÇÕES AO CLICAR NO TÍTULO
		$('.obs-title').bind('click', function () {
			$('.add-obs').fadeIn('fast');
		});

		// ADICIONA OBSERVAÇÃO
		$(document).on('click', '.add-obs', function (e) {

			e.preventDefault();

			var title = $(this).closest('.obs-title');
			var obsBox = title.next('.panel-obs');
			var html = '<div class="campo-box box-obs clearfix clear">' +
				'<input type="hidden" name="obs_id[]" value="0">' +
				'<textarea name="observacao[]" rows="2" class="std-input full-width-input"></textarea>' +
				'<a href="#" title="Excluir" class="std-btn sm-btn gray-btn del-campo fr">Excluir</a>' +
				'</div><!-- campo -->';

			obsBox
				.show()
				.prepend(html)
				.find('.box-obs:first');

			$('.box-obs:last')
				.css('marginBottom', '15px')
				.siblings('.box-obs').css('marginBottom', '8px');

			zebraObs(); // refaz zebra das caixas

		});


		// --------------------------------------------------------------------------
		// ADICIONA #ANDAMENTOS ( OBSERVAÇÕES DA TELA ACOMPANHAMENTO PROCESSUAL ) 
		// --------------------------------------------------------------------------

		zebraObs(); // zebra

		// MOSTRA BOTÃO DE ADICIONAR ANDAMENTO AO CLICAR NO TÍTULO
		$('.andamento-title').bind('click', function () {
			$('.add-andamento').fadeIn('fast');
		});


		// ADICIONA ANDAMENTO
		$(document).on('click', '.add-andamento', function (e) {

			e.preventDefault();

			var title = $(this).closest('.andamento-title');
			var obsBox = title.next('.panel-andamento');
			var html = '<div class="campo-box box-andamento clearfix clear">' +
				'<input type="hidden" name="obs_acomp_id[]" value="0">' +
				'<textarea name="observacao_acompanhamento[]" rows="2" class="std-input full-width-input"></textarea>' +
				'<a href="#" title="Excluir" class="std-btn sm-btn gray-btn del-campo fr">Excluir</a>' +
				'</div><!-- campo -->';

			//if( obsBox.is(':visible') )	{		

			obsBox
				.show()
				.prepend(html)
				.find('.box-andamento:first');

			$('.box-andamento:last')
				.css('marginBottom', '15px')
				.siblings('.box-andamento').css('marginBottom', '8px');

			//}	

			zebraObs(); // refaz zebra

		});

		// --------------------------------------------------------------------------
		// ADICIONA #FINANCEIRO OBSERVAÇÕES ( TELA ACOMPANHAMENTO PROCESSUAL ) 
		// --------------------------------------------------------------------------

		zebraObs(); // zebra

		// ADICIONA ANDAMENTO
		$(document).on('click', '.add-finan-obs', function (e) {

			e.preventDefault();

			var title = $(this).closest('.obs-finan-title');
			var obsBox = title.next('.panel-finan');
			var html = '<div class="campo-box box-finan clearfix clear">' +
				'<input type="hidden" name="obs_finan_id[]" value="0">' +
				'<textarea name="observacao_financeiro[]" rows="2" class="std-input full-width-input"></textarea>' +
				'<a href="#" title="Excluir" class="std-btn sm-btn gray-btn del-campo fr">Excluir</a>' +
				'</div><!-- campo -->';

			//if( obsBox.is(':visible') )	{		

			obsBox
				.show()
				.prepend(html)
				.find('.box-finan:first');

			$('.box-finan:last')
				.css('marginBottom', '15px')
				.siblings('.box-finan').css('marginBottom', '8px');

			//}	

			zebraObs(); // refaz zebra

		});


		// -------------------------
		// ADICIONA #TELEFONES
		// ------------------------- 

		$(document).on('click', '.add-tel', function (e) {

			e.preventDefault();

			var multipleBox = $(this).closest('.multiple-box');

			var html = '<div class="campo-box tel-box">' +
				'<label>DDD/Telefone </label>' +
				'<input type="hidden" name="tel_id[]" value="0">' +
				'<input type="text" name="ddd[]" value="" class="std-input ddd-input">&nbsp;' +
				'<input type="text" name="numero_telefone[]" value="" class="std-input tel-input">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

			//if( $('.tel-box').length < 3 ) {

			multipleBox.append(html);

			/*setTimeout(function() {

				var telBox = multipleBox.find('.tel-box');		
				renomeiaCampos( telBox, 'DDD/Telefone ' );

			}, 100);*/

			// } 

		});

		$(document).on('click', '.add-tel-lightbox', function (e) {

			e.preventDefault();

			var multipleBox = $(this).closest('.stack-box');

			var html = '<div class="campo-box tel-box">' +
				'<label>DDD/Telefone </label>' +
				'<input type="hidden" name="tel_id[]" value="0">' +
				'<input type="text" name="ddd_tel_advogado[]" value="" class="std-input ddd-input" style="width:30px;">&nbsp;' +
				'<input type="text" name="telefone_advogado[]" value="" class="std-input tel-input telefone-advogado" style="width:200px;">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

			multipleBox.append(html);

		});


		// -------------------------
		// ADICIONA #E-MAILS
		// ------------------------- 


		// EMAIL JORNAL
		$(document).on('click', '.add-email', function (e) {

			e.preventDefault();

			var multipleBox = $(this).closest('.multiple-box');
			var html = '<div class="campo-box email-box"><div class="clear"></div><label>E-mail</label>' +
				'<input type="hidden" name="email_id[]" class="id-email" value="0">' +
				'<input type="text" name="email[]" class="campo-email std-input md-input">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

			// adiciona campos de email					 
			multipleBox.append(html);

		});

		// EMAIL ADVOGADO
		$(document).on('click', '.add-email-adv', function (e) {

			e.preventDefault();

			var multipleBox = $(this).closest('.multiple-box');
			var html = '<div class="campo-box email-box"><label>E-mail</label>' +
				'<input type="hidden" name="email_id" class="id-email" value="0">' +
				'<input type="text" name="email" class="campo-email std-input md-input">' +
				'&nbsp;<input type="checkbox" name="enviar_email" class="check-email" value="">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

			// adiciona campos de email					 
			multipleBox.append(html);

			var qtdEmails = $('.id-email').length;

			setTimeout(function () {

				$('.qtd-email').val(qtdEmails);

				$('.check-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'enviar_email_' + idx);
				});

				$('.campo-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'email_' + idx);
				});

				$('.id-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'email_id_' + idx);
				});

			}, 100);

		});

		$(document).on('click', '.add-email-lightbox', function (e) {

			e.preventDefault();

			var multipleBox = $(this).closest('.stack-box');
			var html = '<div class="campo-box email-box"><label>E-mail</label>' +
				'<input type="text" name="email_advogado[]" class="campo-email std-input md-input email-advogado" style="width:247px;">' +
				'&nbsp;<input type="checkbox" name="tick_advogado[]" class="check-email" value="">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

			// adiciona campos de email					 
			multipleBox.append(html);

		});

		// -------------------------
		// ADICIONA #CIDADES
		// ------------------------- 


		// EMAIL JORNAL
		$(document).on('click', '.add-city', function (e) {

			e.preventDefault();

			var multipleBox = $(this).closest('.multiple-box');
			var html = '<div class="campo-box cidade-box"><div class="clear"></div><label>Cidade</label>' +
				'<input type="hidden" name="cidade_id[]" class="id-cidade" value="0">' +
				'<input type="text" name="cidade_circulacao[]" class="campo-cidade std-input md-input">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="" data-acao="jornal-cidade">Excluir</a></div>';

			// adiciona campos de email					 
			multipleBox.append(html);

		});


		// -----------------------------------------
		// ATUALIZA SELECT DE #ADVOGADOS VIA AJAX
		// -----------------------------------------
		$('#atualiza-advogado').on('click', function () {

			// atualizaSelect('advogado', $('#sel-advogados'));		

		});

		// ----------------------------------------------
		// ATUALIZA SELECT DE #SECRETARIA/FÓRUM VIA AJAX
		// ----------------------------------------------
		$('#atualiza-secretaria').on('click', function () {

			// atualizaSelect('secretaria', $('#sel-secretaria'));		

		});

	}


	// -----------
	// #SIDEBAR
	// -----------
	var handleSidebar = function () {

		var menu = $('#menu'),
			subLink = $('.sub');

		// MOSTRA SUBMENU
		subLink.bind('click', function () {

			var submenu = $(this).find('ol');

			if (submenu.is(':hidden')) {
				submenu.slideDown('fast');
				$(this, '> a')
					.removeClass('arrow-right')
					.addClass('arrow-down');
			} else if (submenu.is(':visible')) {
				submenu.slideUp('fast');
				$(this, '> a')
					.removeClass('arrow-down')
					.addClass('arrow-right');
			}

		});

		subLink.find('a:first').bind('click', function (e) {
			e.preventDefault();
		});

	}


	// -----------------
	// #EXCLUSÕES
	// -----------------
	var handleExclusoes = function () {

		// DEL ITEM PADRÃO
		$('.del-item').on('click', function (e) {

			e.preventDefault();

			var confirmacao = confirm($(this).attr('data-del-message'));

			if (confirmacao) {
				$('#form-exclusao').submit();
			}

		});


		// --------------------------------
		// EXCLUI SECRETARIA / FÓRUM
		// --------------------------------	
		$(document).on('click', '#exclui-secretaria', function (e) {

			e.preventDefault();

			var boxMsg = '#box-msg';

			showLightbox(boxMsg);

			// DEFINE O TÍTULO DO LIGHTBOX
			$(boxMsg).find('.header').text('Atenção');

			// DEFINE O CONTEÚDO DO LIGHTBOX
			$(boxMsg).find('.content').html('Existem processos vinculados a essa Secretaria/Fórum.');

		});

		// ----------------------------------
		// EXCLUI CAMPOS / FUNÇÃO GERAL
		// ----------------------------------	
		$(document).on('click', '.del-campo', function (e) {

			e.preventDefault();

			var campoID = $(this).attr('data-id');
			var acao = $(this).attr('data-acao');
			var _this = $(this);
			var jornalID = $('input[name=jornal_id]');
			var confirmacao = confirm('Tem certeza que deseja remover este campo?');

			if (acao == 'jornal-secretaria') {
				console.log('é jornal-secretaria');
				var params = {
					item_id: campoID,
					acao: acao,
					jornal_id: jornalID.val()
				};
			} else {
				var params = {
					item_id: campoID,
					acao: acao
				};
			}


			if (confirmacao) {

				$.post('ajax-exclui-item.php', params, function (response) {

					if (response == 'sucesso') {
						_this.closest('div').fadeOut('fast', function () {
							$(this).remove();
						});

					} // sucesso

				}); // post de exclusão

			}

		});


		// ---------------------------
		// APAGA OBSERVAÇÃO VAZIA
		// ---------------------------				
		$(document).on('click', '.del-empty-obs', function (e) {

			e.preventDefault();

			var parentBox = $(this).closest('div');

			parentBox.fadeOut('fast', function () {
				$(this).remove();
				zebraObs();
			});

		});



		// ----------------------------------------
		// EXCLUI OBS	(CAMPO GRAVADO NO BANCO)
		// ----------------------------------------
		$(document).on('click', '.del-obs', function (e) {

			e.preventDefault();

			var boxID = $(this).closest('.box').attr('id');
			var boxMsg = '#box-msg';
			var msgHTML = 'Tem certeza que deseja apagar esta Observação?' +
				'<br><br>' +
				'<a href="#" title="Cancelar" class="std-btn gray-btn close-lightbox">Cancelar</a>' +
				'&nbsp;' +
				'<a href="#" title="Confirmar Exclusão" class="std-btn" data-target="" id="exclui-obs">Confirmar</a>';

			showLightbox(boxMsg); // CHAMA LIGHTBOX
			$(boxMsg).find('.header').text('Exclusão de Observação'); // DEFINE O TÍTULO DO LIGHTBOX
			$(boxMsg).find('.content').html(msgHTML); // DEFINE O CONTEÚDO DO LIGHTBOX
			$(boxMsg).find('#exclui-obs').attr('data-target', boxID); // ADICIONA 'ALVO' NO BOTÃO DE EXCLUSÃO	

		});


		// ----------------------------
		// CONFIRMA EXCLUSÃO DE OBS
		// ----------------------------
		$(document).on('click', '#exclui-obs', function (e) {

			var target = '#' + $(this).attr('data-target');

			e.preventDefault();

			hideLightbox($(this).closest('.lightbox')); // ESCONDE O LIGHTBOX


			// remove caixa
			$(target).closest('.box').fadeOut('fast', function () {
				$(this).remove();
				zebraObs(); // zebra
			});

		});

	}


	// -----------
	// #FORMS
	// -----------
	var handleForms = function () {

		// TITULO BUSCA
		$('.titulo-filtro').bind('click', function () {

			var camposBusca = $(this).next('.campos');
			var seta = $(this).find('.seta');

			camposBusca.slideToggle('fast');
			seta.toggleClass('seta-baixo');

		});


		// FORM BUSCA > LIMPAR
		$('.clean-btn').on('click', function (e) {

			e.preventDefault();

			var form = $(this).closest('form');
			var commomSelect = form.find('select').not('.sel-sinalizador');

			form.find('input[type=text]').val('');

			commomSelect.val('0').change(); // select normal
			form.find('.sel-sinalizador').val('A').change(); // select sinalizador
			form.submit();

		});

		// FORM BUSCA RELATÓRIO > LIMPAR 
		$('.clean-btn-rel').on('click', function () {
			// location.reload(true); 
		});


		// AO SUBMETER O FORMULÁRIO DE JORNAL
		$(document).on('submit', '.form-has-tel', function () {

			var telBox = $('.tel-box');
			var dddInput = $('.ddd-input');
			var telInput = $('.tel-input');


			// VALIDA DDD
			dddInput.each(function (idx, elem) {
				if ($(this).val() == '') {
					$(this).addClass('empty');
				} else {
					$(this).removeClass('empty');
				}
			});


			// VALIDA TEL
			telInput.each(function (idx, elem) {
				if ($(this).val() == '') {
					$(this).addClass('empty');
				} else {
					$(this).removeClass('empty');
				}
			});


			// IMPEDE A SUBMISSÃO DO FORMULÁRIO
			if ($('.empty').length > 0) {

				alert('Preencha todos os telefones adicionados.');
				$('html, body').animate({
					scrollTop: telBox.offset().top - 200
				}, 150, 'linear');
				return false;

			}
			// SUBMETE FORMULÁRIO
			else {
				return true;
			}

		});


		// AO SUBMETER O FORMULÁRIO DE ADVOGADO...
		$(document).on('submit', '.form-advogado', function () {

			var nome = $('#nome');
			var oab = $('#oab');

			if (nome.val() == '') {
				alert('Preencha o campo Nome');
				nome.focus();
				return false;
			} else if (oab.val() == '') {
				alert('Preencha o campo OAB');
				oab.focus();
				return false;
			} else
				return true;

		});

	}


	// -----------------
	// #MÁSCARAS
	// -----------------
	var handleMasks = function () {

		// data	
		$('.date-input').mask('00/00/0000');

		// cep	
		$('.cep-input').mask('00000-000');

		// cpf	
		$('.cpf-input').mask('000.000.000-00');

		// cnpj	
		$('.cnpj-input').mask('00.000.000/0000-00');

		// money
		$('.money-input').mask('000.000.000.000.000,00', {
			reverse: true
		});

		// just numbers
		$('.number-input').mask('0#', {
			maxlength: false
		});

	}


	// ----------------------------------------
	// TELA DE GERENCIAMENTO DE #ADVOGADOS
	// ----------------------------------------
	var handleAdvogados = function () {

	}


	// ----------------------------------------
	// TELA DE GERENCIAMENTO DE #BOLETOS
	// ----------------------------------------
	var handleBoletos = function () {
		$('.impressao-boleto').on('click', function (e) {
			e.preventDefault();
			$('#form-download').submit();
		});

		// ADICIONA OBSERVACAO BOLETO
		$(document).on('click', '.add-boleto-obs', function (e) {

			e.preventDefault();

			var title = $(this).closest('.obs-boleto-title');
			var obsBox = title.next('.panel-boleto');
			var html = '<div class="campo-box box-boleto clearfix clear">' +
				'<input type="hidden" name="obs_boleto_id[]" value="0">' +
				'<textarea name="observacao[]" rows="2" class="std-input full-width-input"></textarea>' +
				'<a href="#" title="Excluir" class="std-btn sm-btn gray-btn del-campo fr">Excluir</a>' +
				'</div><!-- campo -->';

			//if( obsBox.is(':visible') )	{		

			obsBox
				.show()
				.prepend(html)
				.find('.box-boleto:first');

			$('.box-boleto:last')
				.css('marginBottom', '15px')
				.siblings('.box-boleto').css('marginBottom', '8px');

			//}	

			zebraObs(); // refaz zebra

		});
	}


	// ----------------------------------------
	// TELA DE GERENCIAMENTO DE #SACADOS
	// ----------------------------------------
	var handleSacados = function () {

		$(".btn-documento").on('click', function () {
			$('input[name="cpf_cnpj"]').focus();
			$('input[name="cpf_cnpj"]').removeClass('cpf-input');
			$('input[name="cpf_cnpj"]').removeClass('cnpj-input');

			if ($(this).val() == 'pj') {
				$('input[name="cpf_cnpj"]').addClass('cnpj-input');
			} else {
				$('input[name="cpf_cnpj"]').addClass('cpf-input');
			}

			setTimeout(function () {
				handleMasks();
			}, 250);
		});

		$('.add-email-sac').on('click', function (e) {
			e.preventDefault();

			var multipleBox = $(this).closest('.multiple-box');
			var html = '<div class="campo-box email-box"><label>E-mail</label>' +
				'<input type="hidden" name="email_id" class="id-email" value="0">' +
				'<input type="text" name="email" class="campo-email std-input md-input">' +
				'&nbsp;<input type="checkbox" name="enviar_email" class="check-email" value="">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

			// adiciona campos de email					 
			multipleBox.append(html);

			var qtdEmails = $('.id-email').length;

			setTimeout(function () {

				$('.qtd-email').val(qtdEmails);

				$('.check-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'enviar_email_' + idx);
				});

				$('.campo-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'email_' + idx);
				});

				$('.id-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'email_id_' + idx);
				});

			}, 100);

		});

		$('.excel-item').on('click', function (e) {

			e.preventDefault();
			$('#form-excel').submit();

		});
	}

	// -------------------------------------
	// TELA DE GERENCIAMENTO DE #PROCESSOS
	// -------------------------------------
	var handleProcessos = function () {

		//--------------------------------------------------------------------------
		// ZERA O CAMPO 'CÓDIGO ADVOGADO' SE O CAMPO 'NOME ADVOGADO' ESTIVER VAZIO
		// -------------------------------------------------------------------------
		$('#form-processo').on('submit', function () {
			if ($('.advogado-field').val() == '')
				$('#advogado-id').val(0);
		});


		// ----------------------------
		// CADASTRO DE PROCESSOS
		// ----------------------------
		$('.campo-arquivo-processo').hide();

		$('#sel-estado-processo').on('change', function () {

			var valor = $(this).val();

			if (valor == "DF" || valor == "MS" || valor == "MT" || valor == "RJ" || valor == "SE" || valor == "SP")
				$('.campo-arquivo-processo').show('fast');
			else
				$('.campo-arquivo-processo').hide('fast');

		});

		$('.estado_arquivo_importacao').hide();

		$(document).ready(function () {
			$('input:radio[name=opcao_estado]').change(function () {
				if ($('input[name=opcao_estado]:checked').val() == 2)
					$('.estado_arquivo_importacao').show('fast');
				else {
					$('.estado_arquivo_importacao').hide('fast');
					setTimeout(function () {
						$('#sel-estado-importacao').val(0);
					}, 250);
				}
			});
		});



	}

	// --------------------------------------
	// TELA DE GERENCIAMENTO DE #PROPOSTAS
	// --------------------------------------
	var handlePropostas = function () {

		$('.form-proposta').on('submit', function () {
			// var status = $('.sel-status');			
		});

		// BTN ENVIA PROPOSTA
		$('.btn-proposta').on('click', function () {
			controlaProposta($(this));
		});


		// CONTROLA PROPOSTA
		function controlaProposta(elem) {

			var form = elem.closest('form');
			var acao = form.find('input[name=acao]');
			var param = elem.attr('data-acao');
			var confirmacao = confirm(elem.attr('data-msg'));

			if (confirmacao) { // se confirmar submete formulário
				acao.val(param);
				form.submit();
			}

		}
		// -------------

		var statusAuxInicial = $('.status-aux').val();

		$('.jornal-aceito, .jornal-nao-aceito').on('click', function (e) {
			e.preventDefault();
		});

		// DÁ ACEITE NO JORNAL
		$(document).on('click', '.aceite-jornal', function (e) {

			e.preventDefault();

			var _thisAceite = $(this).prev('.hidden-aceite');
			var _this = $(this);
			var valorJornal = $(this).closest('.box-wrapper').find('.val-padrao');
			var makeGreen = function () {

				_this.addClass('jornal-aceito green-btn').removeClass('black-btn').text('Aceito');
				_thisAceite.val('A');
				$('.status-aux').val('A');

				$('.aceite-jornal').not(_this)
					.removeClass('green-btn jornal-aceito')
					.addClass('black-btn')
					.text('Aceite');

				$('.hidden-aceite').not(_thisAceite).val('');
			}

			//////

			if (!$(this).hasClass('jornal-aceito')) {
				var confirmacaoAceite = confirm('Confirma o aceite desta opção de Jornal?');
			}

			// se já estiver como aceito...
			if ($(this).hasClass('jornal-aceito')) {

				// tira classe de botão verde e muda texto
				$(this).removeClass('jornal-aceito green-btn').addClass('black-btn').text('Aceite');
				_thisAceite.val(''); // limpa hidden	

				if (statusAuxInicial == 'A') {
					$('.status-aux').val('E');
				} else {
					$('.status-aux').val(statusAuxInicial);
				}

			} else if (!$(this).hasClass('jornal-aceito') && confirmacaoAceite && valorJornal.val() == '') {
				alert('Jornal sem valor padrão.');
			} else if (!$(this).hasClass('jornal-aceito') && confirmacaoAceite && valorJornal.val() == '0,00') {
				alert('Jornal sem valor padrão.');
			} else if (!$(this).hasClass('jornal-aceito') && confirmacaoAceite && valorJornal.val() == '0') {
				alert('Jornal sem valor padrão.');
			} else if (!$(this).hasClass('jornal-aceito') && confirmacaoAceite) {
				makeGreen();
			}

		});


		// ADICIONA DJE
		$('.jornal-title').bind('click', function () {

			var boxDJE = $('.box-dje').is(':visible');

			// SOMENTE EXIBE O BOTÃO PARA ADICIONAR BOX DJE SE O BOX DJE NÃO ESTIVER NA TELA	
			if (!boxDJE) {
				$('.add-dje').fadeIn('fast');
			}

		});

		// AO CLICAR NO BOTÃO
		$(document).on('click', '.add-dje', function (e) {

			e.preventDefault();

			var jornalID = $(this).closest('.panel-jornal').find('#sel-jornal').val();
			var _this = $(this);

			$.post('ajax-valor-jornal.php', {
				acao: 'dje',
				jornal_id: jornalID
			}, function (response) {

				var valorFinal = 1 + parseFloat(response.valor_padrao.replace(',', '.')) + parseFloat(response.valor_dje.replace(',', '.'));
				var valorPadrao = parseFloat(response.valor_padrao);
				var valorDJE = parseFloat(response.valor_dje);

				valorPadrao = valorPadrao.formatMoney(2, '.', ',');
				valorDJE = valorDJE.formatMoney(2, '.', ',');
				valorFinal = valorFinal.formatMoney(2, '.', ',');

				// HTML DO BOX DE DJE
				var html = '<div class="box-adicional box-wrapper box-dje fr">' +
					'<div class="form-title"> <span class="fl">DJE</span> <a href="#" class="std-btn gray-btn del-dje fr">Excluir</a> </div>' +
					'<div class="campo-box">' +
					'<label class="label-block">Quantidade</label>' +
					'<input type="text" name="quantidade[]" class="std-input sm-input number-input" value="1">' +
					'</div>' +
					'<!-- campo -->' +
					'<div class="campo-box">' +
					'<label class="label-block">Valor Padrão (R$) </label>' +
					'<input type="text" name="valor_padrao[]" class="std-input sm-input valor-dje-padrao money-input val-padrao" value="' + valorPadrao + '">' +
					'</div>' +
					'<!-- campo -->' +
					'<div class="campo-box">' +
					'<label class="label-block">Valor DJE (R$) </label>' +
					'<input type="text" name="valor_dje[]" class="std-input sm-input money-input" value="' + valorDJE + '">' +
					'</div>' +
					'<!-- campo -->' +
					'<div class="campo-box">' +
					'<label class="label-block">Valor Final (R$) </label>' +
					'<input type="text" name="valor_final[]" class="std-input sm-input money-input" value="' + valorFinal + '">' +
					'</div>' +
					'<!-- campo -->' +
					'<a href="#" class="std-btn sm-btn black-btn full-width-btn aceite-jornal">Aceite</a>' +
					'</div><!-- box adicional -->';

				if ($('.box-jornal').is(':visible')) {

					// MOSTRA BOX DJE
					$('.box-jornal').next('.box-dje').hide().fadeIn('fast', function () {

						$('.qtd-dje').val('1');
						$('input[name=adiciona_dje]').val('sim'); // preenche hidden dje para validação do PHP
						$('.valor-dje-padrao').val(valorPadrao);
						$('.valor-dje').val(valorDJE);
						$('.valor-dje-final').val(valorFinal);

						somaDJE();

						setTimeout(function () {
							// handleMasks();
						}, 250);

						_this.fadeOut('fast');

					});

				}

			}, 'json');

		});


		// TROCA JORNAL PADRÃO
		$('.valor-jornal').on('keyup', function () {
			somaDJE();
		});

		// SOMA DJE
		$('.valor-dje').on('keyup', function () {
			somaDJE();
		});

		/*function soma() {

			var qtdJornal = $('.qtd-jornal'),
			valJornal = $('.valor-jornal'),
			valorFinal = $('.valor-final-jornal'),
			resultado;

			resultado = parseFloat( qtdJornal.val() ) * parseFloat( valJornal.val().replace('.', '').replace(',', '') );
			valorFinal.val( resultado );
			handleMasks();
		}*/

		function somaDJE() {

			var boxDJE = $('.box-dje');
			var valJornal = $('.valor-jornal');
			var valorDJE = $('.valor-dje');
			var valorFinal = $('.valor-dje-final');
			var valorFinalJornal = $('.valor-final-jornal');
			var resultado;

			resultado = parseFloat(valJornal.val().replace('.', '').replace(',', ''));
			resultado = resultado + parseFloat(valorDJE.val().replace('.', '').replace(',', ''));

			valorFinalJornal.val(valJornal.val())

			// SE A CAIXA DO DJE ESTIVER VISÍVEL PREENCHE O CAMPO DE VALOR FINAL 
			if (boxDJE.is(':visible')) {
				valorFinal.val(resultado);
			}

			handleMasks();

		}

		// CARREGA DADOS DO JORNAL SELECIONADO AO TROCAR O SELECT DE JORNAL
		$('#sel-jornal-padrao').on('change', function () {

			var jornalID = $(this).val();

			//$.post( 'arquivo.php', { jornal_id: jornalID }, function( response ) {

			//if( response ) {

			$('.qtd-jornal').val('777');
			$('.valor-jornal').val('123,00');
			$('.valor-final-jornal').val('545465465,00');

			//}

			//});

		});

		// --------------------
		// EXCLUSÃO #DJE
		// --------------------		

		// EXCLUI DJE (INTENÇÃO DE EXCLUSÃO). 
		// MOSTRA BOX DE CONFIRMAÇÃO
		$(document).on('click', '.del-dje', function (e) {

			e.preventDefault();

			var djeID = $(this).attr('data-id');
			var boxMsg = '#box-msg';
			var msgHTML = 'Tem certeza que deseja apagar DJE?' +
				'<br><br>' +
				'<a href="#" title="Cancelar" class="std-btn gray-btn close-lightbox">Cancelar</a>' +
				'&nbsp;' +
				'<a href="#" title="Confirmar Exclusao" class="std-btn" id="exclui-dje" data-id="' + djeID + '">Confirmar</a>';

			showLightbox(boxMsg); // CHAMA LIGHTBOX
			$(boxMsg).find('.header').text('Atenção'); // DEFINE O TÍTULO DO LIGHTBOX
			$(boxMsg).find('.content').html(msgHTML); // DEFINE O CONTEÚDO DO LIGHTBOX

		});


		// -----------------------------
		// CONFIRMA EXCLUSÃO DJE
		// -----------------------------
		$(document).on('click', '#exclui-dje', function (e) {

			e.preventDefault();

			var djeID = $(this).attr('data-id');

			$.post('ajax-exclui-item.php', {
				acao: 'valor-dje',
				item_id: djeID
			}, function (response) {

				if (response == 'sucesso') {

					hideLightbox($(this).closest('.lightbox')); // ESCONDE O LIGHTBOX

					$('.box-dje').fadeOut('fast', function () {
						$('.box-dje').find('.hidden-aceite').val('');
						$('.box-dje').find('.id-dje, .valor-dje-final').val('0');
						// $(this).remove();
						$('.add-dje').show();

						$('input[name=adiciona_dje]').val(''); // limpa hidden dje para validação do PHP

					});

				}

			});

		});
		// ------------------------------------

	}


	// -------------------------------------------
	// TELA DE #ACOMPANHAMENTO PROCESSUAL
	// ------------------------------------------- 
	var handleAcompanhamento = function () {

		// IMPRIME PROPOSTA
		$('.btn-impressao-proposta').on('click', function (e) {
			e.preventDefault();

			if ($(this).hasClass("salvar-financeiro")) {
				$('.form-sacado').submit();
				$('.form-impressao').submit();

				return false;
			}

			$('input[name="sacado_cpf_cnpj"]').removeClass('cpf-input');
			$('.form-impressao').submit();
			$('#form-detalhe').submit();

		});

		$('input[name="sacado_cpf_cnpj"]').on('blur', function () {
			var documento = $(this).val();
			if ($('#sacado-id').val() == "") {
				$.getJSON('ajax-busca-sacado.php', {
						type: 2,
						term: documento
					})
					.done(function (result) {
						if (result.existe == "S") {
							alert("Existe um Sacado cadastrado com o CPF/CNPJ informado.");
						}
					});
			}
		});

		$('.btn-boleto-detalhe').on('click', function (e) {
			e.preventDefault();
			$('#form-boleto-detalhe').submit();
		});

		$('.btn-boleto-pdf').on('click', function (e) {
			e.preventDefault();
			$('#form-boleto-pdf').submit();
		});

		$('.close-lightbox').on('click', function (e) {
			e.preventDefault();
			hideLightbox();
		});

		$('.btn-endereco-escritorio').on('click', function (e) {
			e.preventDefault();

			var advogado_id = $("#advogado-id").val();

			if (!advogado_id) {
				alert('Opção não disponivel. Advogado não informado!');
				return;
			}

			$.getJSON('ajax-busca-advogado.php', {
					type: 1,
					term: advogado_id
				})
				.done(function (result) {
					if (result.cep == "") {
						alert("O escritório não possui endereço cadastrado");
					}
					$('input[name="sacado_cep"]').val(result.cep);
					$('input[name="sacado_logradouro"]').val(result.logradouro);
					$('input[name="sacado_numero"]').val(result.numero);
					$('input[name="sacado_complemento"]').val(result.complemento);
					$('input[name="sacado_bairro"]').val(result.bairro);
					$('input[name="sacado_cidade"]').val(result.cidade);
					$('select[name="sacado_estado"]').val(result.estado);
				});

		});

		$(".btn-documento").on('click', function () {
			$('input[name="sacado_cpf_cnpj"]').focus();
			$('input[name="sacado_cpf_cnpj"]').removeClass('cpf-input');
			$('input[name="sacado_cpf_cnpj"]').removeClass('cnpj-input');

			if ($(this).val() == 'pj') {
				$('input[name="sacado_cpf_cnpj"]').addClass('cnpj-input');
			} else {
				$('input[name="sacado_cpf_cnpj"]').addClass('cpf-input');
			}

			setTimeout(function () {
				handleMasks();
			}, 250);
		});


		$('.add-email-sac').on('click', function (e) {
			e.preventDefault();

			var multipleBox = $('.email-sacado');
			var html = '<div class="campo-box email-box"><label>E-mail</label>' +
				'<input type="hidden" name="email_id" class="id-email" value="0">' +
				'<input type="text" name="email" class="campo-email std-input md-input">' +
				'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

			// adiciona campos de email					 
			multipleBox.append(html);

			var qtdEmails = $('.id-email').length;

			setTimeout(function () {

				$('.qtd-email').val(qtdEmails);

				$('.check-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'enviar_email_' + idx);
				});

				$('.campo-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'email_' + idx);
				});

				$('.id-email').each(function (i, v) {
					var idx = i + 1;
					$(this).attr('name', 'email_id_' + idx);
				});

			}, 100);

		});


		//hideLightbox();

		$('.snd_andamento').on('click', function (e) {
			e.preventDefault();
			var idxID = $(this).attr('data-id');
			var andamento = $('textarea#tex' + idxID).val();
			var andamento_email = $('#andamento_email').val();
			var obs_andamento = $('#and' + idxID).val();
			$('#andamento_lightbox').val(andamento);
			$('#andamento_emails_lightbox').val(andamento_email);
			$('.obs-id').val(obs_andamento);
		});


		/* -------------------------------------------
		ENVIO DE ANDAMENTO PROCESSUAL
		---------------------------------------------- */
		$('#btn-envia-andamento').on('click', function (e) {
			e.preventDefault();
			var acao = 'enviarObservacao';
			var form = $('.form-acompanhamento');
			var emails = $('#andamento_emails_lightbox').val();
			var confirmacao = confirm('Confirma o envio do Andamento Processual?');
			if (confirmacao) {
				form.find('input[name=acao]').val(acao);
				form.find('input[name=andamento_email_destino]').val(emails);
				form.submit();
			}
		});

		/* -------------------------------------------
		ENVIO DE ANDAMENTO DE PROCESSO
		---------------------------------------------- */
		$('.andamento-btn').on('click', function (e) {
			e.preventDefault();
			var acao = 'enviarObservacao';
			var form = $(this).closest('form');
			var boxID = $(this).closest('.box-andamento').attr('data-id');
			var confirmacao = confirm('Confirma o envio do Andamento Processual?');

			if (confirmacao) {

				form.find('input[name=acao]').val(acao);
				form.find('.obs-id').val(boxID);
				form.submit();

			}

		});



		// ------------------
		// CONFIRMA ENVIO
		// ------------------

		$('#envia-agendamento').on('click', function (e) {

			var target = '#' + $(this).attr('data-target');
			var box = $(target).closest('.box');
			var html = '<label>' +
				'<span class="fl">' +
				'Data: <strong>18/08/2014</strong>' +
				'<br>' +
				'Usuário: <strong>Katelyn Kusac</strong>' +
				'</span>';

			e.preventDefault();

			hideLightbox($(this).closest('.lightbox')); // ESCONDE O LIGHTBOX

			// remove caixa
			box.addClass('andamento-enviado');
			box.find('.std-btn').fadeOut('fast', function () {
				$(this).remove();
			});
			box.find('label').append(html);
			box.find('textarea').attr('disabled', 'disabled');

		});


		// ---------------------
		// EMITE #PETIÇÕES
		// ---------------------
		$('.btn-peticao').on('click', function (e) {

			e.preventDefault();

			var acao = $(this).attr('data-acao');
			var tipo = $(this).attr('data-tipo');
			var form = $(this).closest('form');
			var hiddenAcao = form.find('input[name=acao]');
			var hiddenTipo = form.find('input[name=tipo_peticao]');
			var confirmacao = confirm($(this).attr('data-mensagem'));

			// AO CONFIRMAR PREENCHE HIDDENS E SUBMETE O FORMULÁRIO
			if (confirmacao) {
				hiddenAcao.val(acao);
				hiddenTipo.val(tipo);
				form.submit();
			}

		});


		// ------------------------------------------------------------------------------
		// AO CLICAR EM SALVAR, MUDAR AÇÃO PARA SALVAR E LIMPAR CAMPO DE PETIÇÃO
		// ------------------------------------------------------------------------------
		$('.btn-salva-acompanhamento').on('click', function () {

			var form = $(this).closest('form');
			var hiddenAcao = form.find('input[name=acao]');
			var hiddenTipo = form.find('input[name=tipo_peticao]');

			hiddenAcao.val('salva');
			hiddenTipo.val('');

		});

	}


	// -------------------------------------------
	// TELA DE GERENCIAMENTO DE #JORNAIS
	// -------------------------------------------
	var handleJornais = function () {

		// var numEstados = $('.box-local-circ').length;	

		/*	$('.jornal-circ-title').bind('click', function() {
				$('.add-uf').fadeIn('fast');
			});	
	
	
			// ADICIONA ESTADO/CIDADE	
			$(document).on('click', '.add-uf', function(e) {
	
				e.preventDefault();
	
				numEstados++;
	
				var title = $(this).closest('.jornal-circ-title');
				var circBox = title.next('.panel-circ');
				var html = '<div class="campo-box box-local-circ clear" id="estado-'+numEstados+'">'								 
									 +'<div class="campo-box">'								 
									   +'<label>Estado</label>'
									   +'<select name="estado_circulacao[]" class="sel-estado-circ">'
									   +'<option value="0">Selecione</option>'
									   +'<option value="1">RS</option>'
									   +'<option value="2">SP</option>'
									   +'<option value="3">RJ</option>'
									   +'</select>'
									   +'&nbsp;<a href="#" title="" class="std-btn gray-btn del-uf">Excluir</a>'
									   +'</div>'
									   +'<!-- campo -->'
									   +'<div class="campo-box">'
									   +'<label>Cidade</label>'
									   +'<select name="cidade_circulacao[]" class="sel-cidade">'
									   +'<option value="0">Selecione</option>'
									   +'<option value="1">Caxias do Sul</option>'
									   +'<option value="2">São Paulo</option>'
									   +'<option value="3">Rocinha</option>'
									   +'</select>'
									   +'<a href="#" title="" class="std-btn add-cidade">Adicionar Cidade</a>'
									   +'</div>'
									   +'<!-- campo -->'
									   +'</div>';				 	 
			
				if( circBox.is(':visible') )	{
					$('.box-local-circ:first').before(html).hide().fadeIn();	
				}
	
			});	*/


		// ADICIONA CIDADE
		/*$(document).on('click', '.add-cidade', function(e) {

			e.preventDefault();
			var estadoID;

			var box = $(this).closest('.box-local-circ');
			var numCidades = box.find('.sel-cidade').length + 1;
			var cidade = box.find('.sel-estado-circ').val(); // estado selecionado


			var html = '<div class="campo-box">'
								 + '<label>Cidade</label>'
								 + '<select name="cidade_circulacao[]" class="sel-cidade">'
							// +'<option value="0">Selecione</option>' +
							 	 + '<option value="1">Caxias do Sul</option>'
								 + '<option value="2">São Paulo</option>'
								 + '<option value="3">Rocinha</option>'									
								 + '</select>'
								 + '&nbsp;<a href="#" title="" class="std-btn gray-btn del-cidade">Excluir</a>'
								 + '</div><!-- campo -->';

			box.append(html);

		});*/


		// EXCLUI ESTADO/CIDADE			
		/*$(document).on('click', '.del-uf', function(e) {
			
			e.preventDefault();

			var estadoID = $(this).closest('.box-local-circ').attr('id');
			var boxMsg = '#box-msg';
			var msgHTML = 'Tem certeza que deseja apagar este Estado e todas as suas cidades?' +	
										'<br><br>' +
										'<a href="#" title="Cancelar" class="std-btn gray-btn close-lightbox">Cancelar</a>' +
										'&nbsp;'+
										'<a href="#" title="Confirmar Exclusão" class="std-btn" data-target="" id="exclui-estado">Confirmar</a>';

			showLightbox(boxMsg); // CHAMA LIGHTBOX
			$(boxMsg).find('.header').text('Exclusão de Estado/Cidades'); // DEFINE O TÍTULO DO LIGHTBOX
			$(boxMsg).find('.content').html(msgHTML); // DEFINE O CONTEÚDO DO LIGHTBOX
			$(boxMsg).find('#exclui-estado').attr('data-target', estadoID);					

		});	*/


		// CONFIRMA EXCLUSÃO ESTADO/CIDADE
		/*$(document).on('click', '#exclui-estado', function(e) {

			var target = '#' + $(this).attr('data-target');

			e.preventDefault();
			
			hideLightbox( $(this).closest('.lightbox') ); // ESCONDE O LIGHTBOX
			

			// remove caixa
			$(target).closest('.box-local-circ').fadeOut('fast', function() { $(this).remove(); });

		});*/


		// EXCLUI CIDADE			
		/*$(document).on('click', '.del-cidade', function(e) {
			
			e.preventDefault();

			$(this).closest('div').fadeOut('fast', function() {
				$(this).remove();
			});				

		});	*/

	}


	/* --------------------
	// #AUTOCOMPLETAR
	----------------------- */
	var handleAutoCompletar = function () {

		// -------------------------------------------------------------
		// AUTOCOMPLETAR DE CIDADES ( LIGHTBOX - TELA PROCESSOS )
		// -------------------------------------------------------------

		$('.cidade-field').autocomplete({

			source: 'ajax-busca-jornal.php',
			// source: [ 'São Paulo', 'Rio de Janeiro', 'Curitiba', 'Florianópolis' ],
			minLength: 1,

			open: function (event, ui) { // ao abrir lista de itens
				$(".ui-autocomplete li.ui-menu-item:odd a").addClass("ui-menu-item-alternate");
				$(".ui-autocomplete .ui-menu").css('width', '');
			},

			select: function (event, ui) { // ao selecionar/clicar em algum item

				var $valor = ui.item.label,
					$selectID = $valor.split('-'),
					codJornal = $.trim($valor.substring($valor.lastIndexOf('-') + 7)),
					nomeJornal = $.trim($selectID[0] + ' - ' + $selectID[1]);

				$('.jornal-id').val(codJornal);
				$('.jornal-nome').val(nomeJornal);

			}

		});



		// ----------------------------------
		// AUTOCOMPLETAR DE ADVOGADOS
		// ----------------------------------

		$('.advogado-field').autocomplete({

			source: 'ajax-busca-advogado.php',
			//source: ['Fulano de tal', 'Ciclano de tal', 'John Doe', 'Jane Doe', 'Saul Goodman', 'Skyler', 'Jesse Pinkman', 'Walter White'],
			minLength: 1,

			open: function (event, ui) { // ao abrir lista de itens
				$(".ui-autocomplete li.ui-menu-item:odd a").addClass("ui-menu-item-alternate");
				$(".ui-autocomplete .ui-menu").css('width', '');
			},

			select: function (event, ui) { // ao selecionar/clicar em algum item

				var $valor = ui.item.label,
					$selectID = $valor.split('-');
				codAdv = $.trim($selectID[0].substring(5)); // pega o código do advogado

				$('#advogado-id').val(codAdv);

			}

		});

		// ----------------------------------
		// AUTOCOMPLETAR DE SACADOS
		// ----------------------------------

		$('.sacado-field').autocomplete({

			source: 'ajax-busca-sacado.php',
			minLength: 1,

			open: function (event, ui) { // ao abrir lista de itens
				$(".ui-autocomplete li.ui-menu-item:odd a").addClass("ui-menu-item-alternate");
				$(".ui-autocomplete .ui-menu").css('width', '');
			},

			select: function (event, ui) { // ao selecionar/clicar em algum item

				var $valor = ui.item.label,
					$selectID = $valor.split('-');
				var codSac = $.trim($selectID[0].substring(5)); // pega o código do advogado

				$('#sacado-id').val(codSac);

				$.getJSON('ajax-busca-sacado.php', {
						type: 1,
						term: codSac
					})
					.done(function (result) {
						var tipo = (result.cpf_cnpj.length > 14 ? 'pj' : 'pf');
						$('input[name="sacado_tipo"][value="' + tipo + '"]').trigger("click");
						$('input[name="sacado_nome"]').val(result.nome);
						$('input[name="sacado_cpf_cnpj"]').val(result.cpf_cnpj);
						$('input[name="sacado_cep"]').val(result.cep);
						$('input[name="sacado_logradouro"]').val(result.logradouro);
						$('input[name="sacado_numero"]').val(result.numero);
						$('input[name="sacado_complemento"]').val(result.complemento);
						$('input[name="sacado_bairro"]').val(result.bairro);
						$('input[name="sacado_cidade"]').val(result.cidade);
						$('select[name="sacado_estado"]').val(result.estado);

						if (result.emails && result.emails.length > 0) {

							var multipleBox = $('.email-sacado');
							multipleBox.find(".email-box").remove();

							for (i = 0; i < result.emails.length; i++) {
								var data = result.emails[i];

								var html = '<div class="campo-box email-box"><label>E-mail</label>' +
									'<input type="hidden" name="email_id_' + (i + 1) + '" class="id-email" value="0">' +
									'<input type="text" name="email_' + (i + 1) + '" value="' + data.email + '" class="campo-email std-input md-input">' +
									'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo">Excluir</a></div>';

								// adiciona campos de email					 
								multipleBox.append(html);
							}

							var qtdEmails = result.emails.length;

							setTimeout(function () {

								$('.qtd-email').val(qtdEmails);

								$('.check-email').each(function (i, v) {
									var idx = i + 1;
									$(this).attr('name', 'enviar_email_' + idx);
								});

								$('.campo-email').each(function (i, v) {
									var idx = i + 1;
									$(this).attr('name', 'email_' + idx);
								});

								$('.id-email').each(function (i, v) {
									var idx = i + 1;
									$(this).attr('name', 'email_id_' + idx);
								});

							}, 100);

						}
					});
			}
		});

		// ----------------------------------
		// AUTOCOMPLETAR DE SECRETARIAS
		// ----------------------------------

		$('.secretaria-field-2').autocomplete({

			//source: 'ajax-busca-secretaria-2.php',
			source: function (request, response) {
				$.ajax({
					url: "ajax-busca-secretaria-2.php?uf=" + $('.sel-estado').val() + '&term=' + $('.secretaria-field-2').val(),
					dataType: "json",
					data: {
						term: request.term
					},
					success: function (data) {
						response(data);
					}
				});
			},

			minLength: 1,

			open: function (event, ui) { // ao abrir lista de itens
				$(".ui-autocomplete li.ui-menu-item:odd a").addClass("ui-menu-item-alternate");
				$(".ui-autocomplete .ui-menu").css('width', '');
			},

			select: function (event, ui) { // ao selecionar/clicar em algum item
				var $valor = ui.item.label,
					$selectID = $valor.split('-');
				codSec = $.trim($selectID[0].substring(5)); // pega o código da secretaria
				$('.sel-estado').val($.trim($selectID[1])).change();
				setTimeout(function () {
					$('#sel-secretaria').val($.trim($selectID[0].substring(5))).change();
					$('.secretaria-field-2').val('');
				}, 1000);
			}

		});


		// ---------------------------------------
		// AUTOCOMPLETAR DE SECRETARIA/FÓRUM
		// ---------------------------------------

		$('.secretaria-field').autocomplete({

			source: 'ajax-busca-secretaria.php',
			// source: ['Secretaria 1', 'Secretaria 2', 'Secretaria 3', 'Secretaria 4', 'Secretaria 5'],
			minLength: 1,

			open: function (event, ui) { // ao abrir lista de itens

				$(".ui-autocomplete li.ui-menu-item:odd a").addClass("ui-menu-item-alternate");
				$(".ui-autocomplete .ui-menu").css('width', '');

			},

			select: function (event, ui) { // ao selecionar/clicar em algum item

				var $valor = ui.item.label,
					$selectID = $valor.split('-');
				codSec = $.trim($selectID[0].substring(5)),
					nomeSec = $.trim($selectID[1]),
					boxSecretaria = $('.panel-secretaria .panel-content');

				var html = '<div class="campo-box">' +
					'<label>Secretaria/Fórum</label>' +
					'<input type="hidden" name="secretaria[]" class="secretaria-id" value="0">' +
					'<input type="text" name="" class="std-input secretaria-nome" readonly>' +
					'&nbsp; &nbsp;<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="' + codSec + '" data-acao="jornal-secretaria">Excluir</a>' +
					'</div>';

				boxSecretaria.append(html); // coloca HTML

				$('.secretaria-id:last').val(codSec); // preenche hidden com o ID
				$('.secretaria-nome:last').val(nomeSec); // preenche hidden com o nome

				$(this).val('');
				return false; // limpa campo

			}

		});



		// -------------------------------
		// AUTOCOMPLETAR DE JORNAIS
		// -------------------------------

		$('.jornal-field').autocomplete({

			//source: 'busca-relacionados.php',
			source: ['Lorem adfg dsgg f', 'Ipsum sfgsfgrrgr', 'Dolor efgh hg g898f', 'Sit', 'Amet'],
			minLength: 1,

			open: function (event, ui) { // ao abrir lista de itens
				$(".ui-autocomplete li.ui-menu-item:odd a").addClass("ui-menu-item-alternate");
				$(".ui-autocomplete .ui-menu").css('width', '');
			},

			select: function (event, ui) { // ao selecionar/clicar em algum item

				var $valor = ui.item.label,
					$selectID = $valor.split('-');
				prodId = $selectID[0];

				$('.campo-jornal-target').val($valor);
				hideLightbox($(ui).closest('.lightbox'));

			}

		});

	}


	// -----------------
	// #TOOLTIPS
	// -----------------
	var handleTooltip = function () {

		$('.tooltip-link').on('mouseover', function (e) { // mouseover

			var tooltipEl = '<span class="tooltip"></span>';
			tooltipContent = $(this).attr('data-title'),
				tWidth = $(this).attr('data-width'),
				direction = $(this).attr('data-direction'),
				y = parseInt($(this).attr('data-y')),
				x = parseInt($(this).attr('data-x'));

			$('body').append(tooltipEl);

			// MOSTRA TOOLTIP
			showTip(e, y, x, direction, tWidth);

		}).on('mousemove', function (e) { // mousemove

			// MOSTRA TOOLTIP	
			showTip(e, y, x, direction, tWidth);

		}).on('mouseleave', function () { // mouseleave

			$('.tooltip').fadeOut('fast', function () {
				$(this).remove();
			});

		});

		// FUNÇÃO PARA MOSTRAR TOOLTIP
		function showTip(evento, y, x, direction, tWidth) {

			if (direction == 'left' || direction == null) {

				$('.tooltip').css({
					'width': tWidth + 'px',
					'top': (evento.pageY - y) + 'px',
					'left': (evento.pageX + x) + 'px'
				}).html(tooltipContent).fadeIn('fast');

			} else if (direction == 'right' && direction != null) {

				$('.tooltip').css({
					'width': tWidth + 'px',
					'top': (evento.pageY - y) + 'px',
					'left': (evento.pageX - x) + 'px'
				}).html(tooltipContent).fadeIn('fast');

			}

		}
		//

	}

	return {

		init: function () { // retorno das funções criadas acima

			// FUNÇÃO PARA FORMATAÇÃO DE NÚMERO EM VALORES MONETÁRIOS 
			// Créditos: Patrick Desjardins - http://www.patrickdesjardins.com/
			Number.prototype.formatMoney = function (decPlaces, thouSeparator, decSeparator) {
				var n = this,
					decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
					decSeparator = decSeparator == undefined ? "." : decSeparator,
					thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
					sign = n < 0 ? "-" : "",
					i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
					j = (j = i.length) > 3 ? j % 3 : 0;
				return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
			};

			handleSidebar();
			handleTooltip();
			handleMasks();
			handleGeneral();
			handleForms();
			handleLightbox();
			handleExclusoes();
			handleAutoCompletar();
			handlePaginacao();

		},

		processos: function () {
			handleProcessos();
		},

		propostas: function () {
			handlePropostas();
		},

		acompanhamento: function () {
			handleAcompanhamento();
		},

		advogados: function () {
			handleAdvogados();
		},

		boletos: function () {
			handleBoletos();
		},

		sacados: function () {
			handleSacados();
		},

		jornais: function () {
			handleJornais();
		}

	}

}();

/*============
CHAMADA
==============*/
App.init();