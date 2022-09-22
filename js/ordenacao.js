$(function () {

	// area		
	var area = $('.form-ordenacao select option:selected'),
		origem = $('#sortable').attr('data-area', area.val());

	// sortable
	$("#sortable").tableDnD({

		onDrop: function (table, row) {

			// refazendo zebra após sort
			//refazZebraLista();

			var linhas = $('#sortable tr:not(:eq(0))'),
				rows = table.rows,
				idCategoria = [],
				linePosition = [],
				indice;

			linhas.each(function () { // looping em linha por linha

				idCategoria.push($(this).attr('id')); // adicionando os IDs em um array					 
				linePosition.push($(this).index()); // adicionando as posições em um array			
				indice = $(this).index();

				// alterando o texto que indica a posição, de acordo com a posição da linha na tabela
				$(this).find('.posicao-prod').text(indice);

			}); // fim loop				 

			// postando o array de IDs, o array de posições e a área em que eles estão sendo alterados	
			$.post('adm-ordenacao-categoria.php', {
				categoria_id: idCategoria, posicao: linePosition
			}, function (response) {
				console.log(response);
			});

		},

		onDragClass: "drag-item"

	});

});