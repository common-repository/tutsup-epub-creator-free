/*----------------------------------------------------------------------------*
 * All jQuery needed (Admin only)
/*----------------------------------------------------------------------------*/
jQuery(document).ready(function($) {

	/*------------------------------------------------------------------------*
	 * Image settings
	/*------------------------------------------------------------------------*/
	function upload_image_callback( obj ){
		window.send_to_editor = function(html) {			
			// The imagem URL
			imgurl = $('img', html).attr('src');
			
			// Change value for the target field
			$(obj).val(imgurl);
			
			// Remove
			tb_remove();
		}
	}
    
	/*------------------------------------------------------------------------*
	 * Upload Image fields
	/*------------------------------------------------------------------------*/
	$('.tutsup-input-book-cover').focus(function() {
		// Remove the actual 
		$(this).val('');
		
		tb_show('Book cover', 'media-upload.php?referer=wptuts-settings&type=image&TB_iframe=true&post_id=0', false);
		
		// Callback
		upload_image_callback(this);
		
		return false;
	});

    $('.create-epub').click(function(){    
        var data = {
            'action': 'create_epub',
            'post-ids': $('.tutsup-post-ids').val(),
            'title': $('.tutsup-input-book-title').val(),
            'creator': $('.tutsup-input-book-creator').val(),
            'language': $('.tutsup-input-book-language').val(),
            'rights': $('.tutsup-input-book-rights').val(),
            'publisher': $('.tutsup-input-book-publisher').val(),
            'book-cover': $('.tutsup-input-book-cover').val()
        };
        
        var create_epub_val = $('.create-epub').val();
        $.ajax({
            beforeSend: function() { 
                $('.create-epub').val(objectL10n.wait);
            },
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function( data ) {
                $('.book-data').html( data );                
                $('.create-epub').val( create_epub_val );
            }
        });
    });

    $('.searching').hide();
	
    var tutsup_input_interval = false;
	$('.tutsup-input-noticia').keypress(function(){
        
        var tutsup_input = $(this);
        
        if( tutsup_input_interval ) {
            clearTimeout( tutsup_input_interval );
        }
        
        tutsup_input_interval = setTimeout(function(){
            var tutsup_input_val = tutsup_input.val();
            
            var data = {
                'action': 'tutsup_search_noticia',
                's': tutsup_input_val
            };
            
            $.ajax({
                beforeSend: function() {
                    $('.searching').show();
                    $('.tutsup-noticias-encontradas').html('');
                },
                url: ajaxurl,
                type: 'POST',
                data: data,
                success: function( response ){                
                    $('.searching').hide();
                    
                    if ( response.replace(/\s+/g, '') == '' ) {
                        $('.tutsup-noticias-encontradas').html('<br>Nada encontrado!');
                    } else {
                        $('.tutsup-noticias-encontradas').html(response);
                    }
                    
                    $('.tutsup-noticia-ajax').draggable({
                        connectToSortable: '.tutsup-noticia',
                        helper: 'clone'
                    }).disableSelection();
                    
                    $('.tutsup-noticia').droppable({
                        drop: function( event, ui ){}
                    }).sortable({
                        placeholder: 'tutsup-placeholder',
                        receive: function (event, ui) { // add this handler
                            ui.item.remove(); // remove original item
                        },
                        stop: function( event, ui ){
                            var noticias_id = new Array();
                            var counter = 0;
                            
                            $('.tutsup-noticia').find('.tutsup-noticia-ajax').each(function(){
                                var noticia = $(this);
                                var noticia_id = $(this).find('.noticia-id').attr('data-id');
                                
                                if ( typeof noticia_id != 'undefined' ) {
                                    counter++;
                                    noticias_id.push( noticia_id );
                                }
                            });
                            
                            $('.tutsup-post-ids').val(noticias_id);
                        }
                    }).disableSelection();
                }
            });
            
        }, 1000);
    });
     
    $('.tutsup-apaga-noticia').droppable({
        drop: function( event, ui ){
            $(ui.draggable).remove();
        }
    });   
});
