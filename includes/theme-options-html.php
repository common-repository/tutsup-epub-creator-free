<?php if ( ! defined('ABSPATH')) exit; ?>

<div class="wrap">

<h2> <a href="http://www.tutsup.com/">Tutsup Epub Creator</a> <span style="font-size: 12px;">ver <?php echo $this->ver; ?></span></h2>

<?php if( isset($_GET['settings-updated']) ) { ?>
	<div id="message" class="updated">
		<p><strong><?php _e('Settings saved.', 'tutsup') ?></strong></p>
	</div>
<?php } ?>

<!-- <form method="post" action="options.php"> -->

    <input type="hidden" name="post-ids" class="tutsup-post-ids">

	<div class="tutsup-content clearfix">
    
        <div class="tutsup-data cleafix">
            <h3 class="tutsup-heading"><?php _e('Book data', 'tutsup');?></h3>
            
            <p>
                <?php _e('Please, refer to', 'tutsup'); ?>
                <a target="_blank" href="http://www.idpf.org/epub/20/spec/OPF_2.0.1_draft.htm#TOC2.2">link</a>
                <?php _e('for more details.', 'tutsup'); ?>
            </p>
            
            <label class="tutsup-labels" for="tutsup-input-book-title"><?php _e('Title:', 'tutsup');?></label>
            <input type="text" class="tutsup-input-book-title regular-text" id="tutsup-input-book-title">
            
            <label class="tutsup-labels" for="tutsup-input-book-creator"><?php _e('Creator:', 'tutsup');?></label>
            <input type="text" class="tutsup-input-book-creator regular-text" id="tutsup-input-book-creator">
            
            <label class="tutsup-labels" for="tutsup-input-book-language"><?php _e('Language:', 'tutsup');?></label>
            <input type="text" class="tutsup-input-book-language regular-text" id="tutsup-input-book-language">
            
            <label class="tutsup-labels" for="tutsup-input-book-rights"><?php _e('Rights:', 'tutsup');?></label>
            <input type="text" class="tutsup-input-book-rights regular-text" id="tutsup-input-book-rights">
            
            <label class="tutsup-labels" for="tutsup-input-book-publisher"><?php _e('Publisher:', 'tutsup');?></label>
            <input type="text" class="tutsup-input-book-publisher regular-text" id="tutsup-input-book-publisher">
            
            <label class="tutsup-labels" for="tutsup-input-book-cover"><?php _e('Book cover:', 'tutsup');?></label>
            <input type="text" class="tutsup-input-book-cover regular-text" id="tutsup-input-book-cover">
        </div>
        
        <div class="tutsup-data clearfix">
        
            <h3 class="clearfix tutsup-heading"><?php _e('Organize pages', 'tutsup');?></h3> 
            
            <p class="clearfix"><?php _e('Here you can search your posts and then drag and drop them to your book pages.', 'tutsup');?></p>

            <div class="tutsup-left-col tutsup-cols">
            
                <h3><?php _e('Your posts', 'tutsup');?></h3>
                <label class="tutsup-labels" for="tutsup-input-noticia"><?php _e('Search your posts:', 'tutsup');?></label><br>
                <input type="search" class="tutsup-input-noticia" id="tutsup-input-noticia"><br>
                
                <div class="searching"><?php _e('Searching...', 'tutsup');?></div>
                                
                <br><div class="tutsup-noticias-encontradas"></div>
            </div>
            

            <div class="tutsup-right-col tutsup-cols">
                <div class="tutsup-right-col-inner">
                    <h3><?php _e('Book pages', 'tutsup');?></h3>
                    <p class="tutsup-apaga-noticia">
                        <span class="tutsup-move-noticia ui-icon ui-icon-trash"></span>
                        <?php _e('Drag pages here to delete', 'tutsup');?>
                    </p>
                  
                    <div class="tutsup-template">
                        <p class="tutsup-drag-here"><?php _e('Drag posts bellow:', 'tutsup');?></p>
                        <div class="tutsup-noticia tutsup-noticia-full"></div>
                    </div>
                </div>
            </div>
            
        </div>

    </div> <!-- .content -->

    <div class="book-data"></div>  

    <br>
    <input type="submit" class="button button-primary create-epub" value="<?php _e('Create book', 'tutsup');?>">

<!-- </form> form -->
	
</div> <!-- .wrap -->