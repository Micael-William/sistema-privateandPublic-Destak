$(function () {

	// adicionando a sombra no corpo da página 
	var overlay = '<div id="overlay-lightbox"></div>';
	$(overlay).appendTo('body').hide();

	// Faz com que a sombra fique com a mesma altura que o documento, cobrindo toda a tela.
	$('#overlay-lightbox').css({ 'opacity': '0.7', 'height': $(document).height() });

	$('.link-lightbox').live({

		click: function (e) {

			// target
			var id = '#' + $(this).attr('data-rel');
			showLightbox(id);

			//$(id).css({ 'marginTop' : - $(id).height()/2 + 'px' });	
			e.preventDefault();

		} // scroll

	}); // click


	// escondendo o lightbox e a sombra ao clicar em botões específicos
	$('#overlay-lightbox, .lightbox .close').live({
		click: function () {
			hideLightbox();
		}
	});


	// esconde lightbox
	function hideLightbox() {

		$('.lightbox').animate({
			'marginTop': 0,
			top: '-50%',
			opacity: 0
		}, 150, 'linear').fadeOut(500);

		$('#overlay-lightbox').fadeOut(500);

	}


	// mostra lightbox
	function showLightbox(target) {
		$(target).fadeIn('fast');

		$('#overlay-lightbox').fadeIn(500);
		$(target).animate({
			top: '35%',
			'marginTop': - $(target).height() / 2,
			opacity: 1
		}, 150, 'linear');
	}

});
