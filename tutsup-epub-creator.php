<?php
/* 
Plugin Name: Tutsup Epub Creator Free
Plugin URI: http://www.tutsup.com/
Description: Tutsup Epub Creator Free allows you to create EPUB e-books using your WordPress posts. You can search, add and organize posts into your e-book. You can add a cover image and set the book details. It makes it easier for you to create e-books for your readers, so they can read your content using e-book readers like Google Play Books or Calibre.
Version: 0.0.3
Author: Luiz Otávio Miranda
Author URI: http://www.tutsup.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: tutsup
Domain Path: /languages
*/

/**
 * Class Tutsup Epub Creator
 *
 */
if ( ! class_exists('TutsupEpubCreator') ) {

	class TutsupEpubCreator
	{
		// Options
		protected $opts;
		
		// Theme version
		protected $ver;

		/**
		 * __construct
		 *
		 * Loads all methods.
		 */
		public function __construct () {
			$this->opts = array();
			$this->ver = '0.0.3';
            
            load_theme_textdomain('tutsup', plugin_dir_path( __FILE__ ) . '/languages' );
				
			/* Admin only */
			if ( is_admin() ) {
				
				// Loads scripts
				add_action( 
					'admin_enqueue_scripts', 
					array( $this, 'carrega_scripts' ) 
				);

				// Add menu options
				add_action('admin_menu', array( $this, 'adiciona_menu' ) );
				
				// Register options
				add_action( 'admin_init', array( $this, 'registra_opcoes' ) );
				
				// Register a busca ajax
				add_action( 'wp_ajax_tutsup_search_noticia', array( $this, 'search_news' ) );
				
				// Register a busca ajax
				add_action( 'wp_ajax_create_epub', array( $this, 'create_epub' ) );
                
				$plugin = plugin_basename( __FILE__ );
				add_filter( "plugin_action_links_$plugin", array( $this, 'plugin_add_settings_link' ) );
                
			} // is_admin
			
		} // __construct
        
		public function plugin_add_settings_link( $links ) {
			$settings_link = '<a href="edit.php?page=tutsup-epub-creator">Epub Creator</a>';
			array_push( $links, $settings_link );
			return $links;
		}
		
		/**
		 * Adds menu
		 */
		public function adiciona_menu() {
		
			// Creates a page for editing the theme options
			add_posts_page(
				'Epub Creator',            // Título da página
				'Epub Creator',            // Título do menu
				'publish_posts',               // Permissões
				'tutsup-epub-creator',       //	Slug do menu
				array( $this, 'admin_html' ) // Função de callback
			);
			
		} // adiciona_menu
		
		/**
		 * Register options
		 */
		public function registra_opcoes() {
		
			register_setting( 
				'tutsup-epub-creator', 
				'tutsup-epub-creator', 
				array( $this, 'valida_campos' ) // Função de callback
			);
			
		} // registra_opcoes
		
		// Callback to validate data
		public function valida_campos( $input ) {
			
			// Vamos validar apenas o fundo, só para você entender
			if( isset( $input['fundo'] ) ) {
				$input['fundo'] = sanitize_text_field( $input['fundo'] );
			}
			
			return $input;
			
		} // valida_campos

		/**
		 * Loads the HTML
		 */
		public function admin_html() {		
			// If you want to edit, go to the views folder
			require_once plugin_dir_path( __FILE__ ) . '/includes/theme-options-html.php';
		} // admin_html
		
		/**
		 * Loads styles and scripts
		 */
		public function carrega_scripts() {
		
			wp_enqueue_script('media-upload');
			
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			
			wp_enqueue_style( 'wp-color-picker' );
			
			// Our style
			wp_enqueue_style( 
				'tutsup-frontpage-style', 
				plugins_url( '', __FILE__ ) . '/css/admin-style.css',
				array(), 
				$this->ver,
				'all' 
			);
            
            wp_enqueue_style(
                'epub-creator-admin-ui-css',
                plugins_url( '', __FILE__ ) . '/css/jquery-ui.min.css',
                false,
                $this->ver,
                false
            );
                
			// Our script
			wp_enqueue_script(
				'tutsup-frontpage-script', 
				plugins_url( '', __FILE__ ) . '/js/admin-settings.js', 
				array(
                    'wp-color-picker',
                    'jquery-ui-draggable',
                    'jquery-ui-droppable',
                    'jquery-ui-sortable',
                ), 
				$this->ver,
				true
			);
            
            wp_localize_script( 
                'tutsup-frontpage-script', 
                'objectL10n', 
                array(
                    'wait' => __( 'Please, wait...', 'tutsup' ),
                )
            );
			
		} // carrega_scripts
        
        /* Procura notícias */
        public function search_news () {
        
            $segunda_consulta = new WP_Query(
                array(
                's'              => $_POST['s'],
                'posts_per_page' => 10,    // Apenas 10 posts
                'post_type' => 'post'
                )
            );
            ?>

            <?php if ( $segunda_consulta->have_posts() ): ?>
                <?php while ( $segunda_consulta->have_posts() ): ?>

                    <?php $segunda_consulta->the_post(); ?>

                    <div class="tutsup-noticia-ajax clearfix">
                        
                        <div class="noticia-id" data-id="<?php echo $segunda_consulta->post->ID?>"></div>
                        <span class="ui-icon ui-icon-arrow-4 tutsup-move-noticia"></span>
                        <?php the_title();?>
                    </div>

                <?php endwhile; ?>
            <?php endif; ?>

            <?php wp_reset_postdata(); 

            die(); // this is required to terminate immediately and return a proper response
            
        }
	
        /* Procura notícias */
        public function create_epub () {
        
            // This is only to make sure the charset is UTF-8
            // You may remove this line.
            header('Content-Type: text/html; charset=utf-8');
            
            if ( 
                ! isset( $_POST['creator'] ) || empty( $_POST['creator'] )
                || ! isset( $_POST['language'] ) || empty( $_POST['language'] )
                || ! isset( $_POST['rights'] ) || empty( $_POST['rights'] )
                || ! isset( $_POST['title'] ) || empty( $_POST['title'] )
                || ! isset( $_POST['publisher'] ) || empty( $_POST['publisher'] )
            ) {
                echo '<p class="tutsup-error">';
                _e('You must fill all fields to create a book.', 'tutsup');
                echo '</p>';
                exit;
            }
            
            if ( ! isset( $_POST['post-ids'] ) || empty( $_POST['post-ids'] ) ) {
                echo '<p class="tutsup-error">';
                _e('Please, add pages to your book.', 'tutsup');
                echo '</p>';
                exit;
            }
            
            // The class is in the folder classes
            require plugin_dir_path( __FILE__ ) . '/php-epub-creator/classes/TPEpubCreator.php';
            
            // Here we go
            $epub = new TPEpubCreator();

            // E-book configs
            $epub->title = stripslashes(esc_attr( $_POST['title'] ));
            $epub->creator = stripslashes(esc_attr( $_POST['creator'] ));
            $epub->language = stripslashes(esc_attr( $_POST['language'] ));
            $epub->rights = stripslashes(esc_attr( $_POST['rights'] ));
            $epub->publisher = stripslashes(esc_attr( $_POST['publisher'] ));
            
            $book_file_name = sanitize_title( $epub->title );
            
            $upload_dir = wp_upload_dir();
            $epub_creator_dir = $upload_dir['basedir'] . '/tutsup-epub-creator/';
            
            if ( ! file_exists( $epub_creator_dir ) || ! is_dir( $epub_creator_dir ) ) {
                mkdir( $epub_creator_dir, 0777 );
                mkdir( $epub_creator_dir . '/temp_folder/', 0777 );
                mkdir( $epub_creator_dir . '/epubs/', 0777 );
            }
             
            // Temp folder and epub file name (path)
            $epub->temp_folder = $epub_creator_dir . '/temp_folder/';
            $epub->epub_file = $epub_creator_dir . '/epubs/' . $book_file_name . '.epub';

            // You can specity your own CSS
            $epub->css = file_get_contents( plugin_dir_path( __FILE__ ) . '/php-epub-creator/base.css' );
            
            if ( isset( $_POST['book-cover'] ) && ! empty( $_POST['book-cover'] ) ) {
            
                if( ! filter_var( $_POST['book-cover'], FILTER_VALIDATE_URL ) ) {
                    echo '<p class="tutsup-error">';
                    _e('Book cover is not and URL:' . $_POST['book-cover'], 'tutsup');
                    echo '</p>';
                    exit;
                }
            
                $epub->AddImage( $_POST['book-cover'], false, true );
            }
            
            $ids = explode( ',', $_POST['post-ids'] );
            
            $segunda_consulta = new WP_Query(
                array(
                'post__in'              => $ids,
                'orderby' => 'post__in',
                'posts_per_page' => -1,
                'post_type' => 'post'
                )
            );
            ?>

            <?php if ( $segunda_consulta->have_posts() ): ?>
                <?php while ( $segunda_consulta->have_posts() ): ?>

                    <?php $segunda_consulta->the_post(); ?>

                    <?php 
                    $title = get_the_title();
                    $content = get_the_content();
                    $content = apply_filters('the_content', $content);
                    $page_content = '<h1>' . $title . '</h1>' . $content;
                    ?>
                    
                    <?php $epub->AddPage( $page_content, false, $title, true );?>

                <?php endwhile; ?>
            <?php endif; ?>

            <?php wp_reset_postdata(); 
            
            // Create the EPUB
            // If there is some error, the epub file will not be created
            if ( ! $epub->error ) {

                // Since this can generate new errors when creating a folder
                // We'll check again
                $epub->CreateEPUB();
                
                // If there's no error here, you're e-book is successfully created
                if ( ! $epub->error ) {
                    echo '<p class="tutsup-success">';
                    _e('Success: Download your book', 'tutsup');
                    echo ' <a href="' . $upload_dir['baseurl'] . '/tutsup-epub-creator/epubs/' . $book_file_name . '.epub">';
                    _e('here', 'tutsup');
                    echo '</a>.';
                    echo '</p>';
                }
                
            } else {
                // If for some reason your e-book hasn't been created, you can see whats
                // going on
                echo $epub->error;
            }            

            die(); // this is required to terminate immediately and return a proper response
            
        }
	
	} // Class TutsupOpcoesTema

	// Loads
	$tutsup_opcoes_tema = new TutsupEpubCreator();
}