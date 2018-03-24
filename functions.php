<?php

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/error_log');

require_once locate_template('backend/init.php');

require_once locate_template('vendor/enlighten-loop.php');
require_once locate_template('vendor/widget-template-widget.php');
require_once locate_template('template-tags.php');

add_action('init', function(){
	if (class_exists('wp_basic_auth')) {
		$whitelistedIpAddresses = [
			'::1',
			'127.0.0.1',
			'198.84.241.50',
			'23.21.180.178',    // H+K Apps Staging Server (allow PDF generation)
			'216.13.199.146',   // H+K Calgary
			'184.71.14.213',    // H+K Edmonton
			'74.114.100.2',     // H+K Montreal
			'216.174.138.174',  // H+K Regina
			'38.99.156.5',      // H+K Toronto
			'216.191.227.98',   // H+K Ottawa
			'208.71.9.210',     // H+K Quebec
			'38.88.64.3',       // H+K Vancouver
			'184.69.26.154',    // H+K Victoria
		];
		if (in_array($_SERVER['REMOTE_ADDR'], $whitelistedIpAddresses)) {
			remove_action('template_redirect', [wp_basic_auth::$instance, 'basic_auth'], 1);
		}
	}
});

add_action('admin_head', function(){
	printf('<link rel="stylesheet" href="%s/assets/css/admin.css">', get_template_directory_uri());
});

add_action('wp_enqueue_scripts', function(){
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', '', '', '', false);
	}
},100);

add_action('after_setup_theme', function(){
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	register_nav_menus(array(
		'header_navigation_1' => 'Header Navigation 1',
		'header_navigation_2' => 'Header Navigation 2',
	));
	ob_start();
});

add_action('widgets_init', function(){
	$sidebars = array(
		'footer_1' => 'Footer 1',
		'footer_2' => 'Footer 2',
		'footer_3' => 'Footer 3',
	);
	foreach ($sidebars as $id => $label) {
		register_sidebar(
			array(
				'id'            => $id,
				'name'          => $label,
				'description'   => $label,
				'before_widget' => '<article id="%1$s" class="widget %2$s">',
				'after_widget'  => '</article>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>'
			));
	}
});

add_action('init', function(){
	register_post_type('news', array(
		'label'               => 'news',
		'description'         => '',
		'labels'              => array(
			'name'                => 'News',
			'singular_name'       => 'News',
			'menu_name'           => 'News',
			'name_admin_bar'      => 'News',
			'parent_item_colon'   => 'Parent News:',
			'all_items'           => 'All News',
			'add_new_item'        => 'Add New News',
			'add_new'             => 'Add New',
			'new_item'            => 'New News',
			'edit_item'           => 'Edit News',
			'update_item'         => 'Update News',
			'view_item'           => 'View News',
			'search_items'        => 'Search News',
			'not_found'           => 'Not found',
			'not_found_in_trash'  => 'Not found in Trash',
		),
		'supports'            => array('title', 'editor', 'thumbnail', 'revisions', 'excerpt'),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-aside',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'rewrite'             => array(
			'with_front' => false,
			'slug'       => 'news',
		)
	));
});

add_filter('wp_setup_nav_menu_item', function($menu_item){
	if (!is_admin()) {
		if (stripos($menu_item->url, '|') !== false) {
			list($en, $fr) = explode('|', $menu_item->url, 2);
			$menu_item->url = __("[:en]{$en}[:fr]{$fr}");
		}
	}
	return $menu_item;
},0);

add_filter('widget_posts_args', function($args){
	$args['post_type'] = array('post','news');
	return $args;
});

add_filter('excerpt_more', function($more){
	return '&hellip;';
});

if (function_exists('acf_add_options_page')) {
	acf_add_options_page(array(
		'page_title' => 'Theme Options',
		'menu_slug'  => 'acf-iuoe',
		'position'   => 100,
		'icon_url'   => 'dashicons-admin-settings',
	));
	acf_add_options_page(array('page_title' => 'Analytics',   'parent_slug' => 'acf-iuoe'));
	acf_add_options_page(array('page_title' => 'Contact',     'parent_slug' => 'acf-iuoe'));
	acf_add_options_page(array('page_title' => 'MP Letters',  'parent_slug' => 'acf-iuoe'));
	acf_add_options_page(array('page_title' => 'Social',      'parent_slug' => 'acf-iuoe'));
}

add_action('init', function(){
	add_rewrite_endpoint('share', EP_ROOT);
	//flush_rewrite_rules();
});


add_filter('wp_nav_menu_objects', function($items){
	foreach ($items as $item) {
		if ($item->ID === 58 && is_page([296,301,316,320,323,329,332])) {
			$item->url = '#contact-your-mp';
		}
	}
	return $items;
});


function get_open_graph_data() {
	global $post;
	$share_identifier = get_query_var('share', false);
	if ($share_identifier) {
		$sections = get_field('sections', get_option('page_on_front'));
		if ($sections) {
			$data = null;
			foreach ($sections as $section) {
				if ($section['acf_fc_layout'] === 'know_the_facts') {
					foreach ($section['facts'] as $fact) {
						if ($fact['share_identifier'] === $share_identifier) {
							return (object)array(
								'url'         => home_url("/share/$share_identifier/"),
								'title'       => get_field('social_title','option'),
								'description' => $fact['share_content'],
								'image'       => $fact['share_photo'],
							);
						}
					}
					break;
				}
			}
		}
		// if the we can't match the share identifier
		// the throw a 404 error
		$GLOBALS['wp_query']->set_404();
		header('HTTP/1.0 404 Not Found');
		require TEMPLATEPATH.'/404.php';
		exit;
	}
	// use the post information
	if (is_single()) {
		return (object)array(
			'url'         => get_permalink(),
			'title'       => get_the_title(),
			'description' => wp_trim_words($post->post_content, 140),
			'image'       => get_featured_image(),
		);
	}
	// fallback to the global site information
	return (object)array(
		'url'         => home_url(),
		'title'       => get_field('social_title','option'),
		'description' => get_field('social_description','option'),
		'image'       => get_field('social_photo','option'),
	);
}



/* add_action('admin_bar_menu', function($admin_bar){
	$admin_bar->add_menu(array(
		'id'    => 'export-submissions',
		'title' => 'Export Submissions',
		'href'  => admin_url('?export_submissions=1'),
	));
},999);
*/

add_action('admin_bar_menu', function($admin_bar){
	$admin_bar->add_menu(array(
		'id'    => 'export',
		'title' => 'Export',
		'href'  => '#',
	));
	$admin_bar->add_menu(array(
		'id'     => 'export-advocacy',
		'parent' => 'export',
		'title'  => 'Advocacy Form Submissions',
		'href'   => admin_url('?export_advocacy_form_submissions=1'),
	));
	$admin_bar->add_menu(array(
		'id'     => 'export-contacts',
		'parent' => 'export',
		'title'  => 'Contact Form Submissions',
		'href'   => admin_url('?export_contact_form_submissions=1'),
	));
},999);


add_action('init', function(){
	global $wpdb;

	if (!is_user_logged_in() || empty($_GET['export_advocacy_form_submissions'])) {
		return;
	}

	// WPDB functions read the entire result set into memory instead
	// we are using the PDO connection and streaming the result set
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

	$statement = $pdo->query("
		SELECT *
		FROM {$wpdb->prefix}voices
		ORDER BY id ASC
	");


	$writer = \Box\Spout\Writer\WriterFactory::create(\Box\Spout\Common\Type::XLSX);

	$defaultStyle = (new \Box\Spout\Writer\Style\StyleBuilder)
		->setFontName('Calibri')
		->setFontSize(11)
		->setShouldWrapText(false)
		->build();

	$writer->setDefaultRowStyle($defaultStyle);
	$writer->openToBrowser('advocacy-form-'.time().'.xlsx');

	$writer->getCurrentSheet()->setName('VOICES');

	$writer->addRow([
		'Name',
		'Email',
		'Address',
		'City',
		'Postal Code',
		'Description',
		'Opt In',
		'Include Minister',
		'IP Address',
		'Email To',
		'Date',
	]);

	$stats = (object)[
		'total'  => 0,
		'emails' => [],
	];

	while ($data = $statement->fetchObject()) {
		$writer->addRow([
			$data->name,
			$data->email,
			$data->street_address,
			$data->city,
			$data->postal_code,
			utf8_encode($data->profession),
			$data->opt_in,
			$data->include_minister,
			$data->ip_address,
			$data->sent_to,
			date('m/d/Y H:i:s', strtotime($data->created_at))
		]);
		if ($data->sent_to) {
			$stats->total++;
			if (isset($stats->emails[strtolower($data->sent_to)])) {
				$stats->emails[strtolower($data->sent_to)]++;
			} else {
				$stats->emails[strtolower($data->sent_to)] = 1;
			}
		}
		/*
		if ($data->smtp_log) {
			$stats->total++;
			$addresses = [$data->email_to];
			$addresses = array_filter(array_merge($addresses, explode(',', $data->email_cc)), 'strlen');
			foreach ($addresses as $email) {
				if (isset($stats->emails[strtolower($email)])) {
					$stats->emails[strtolower($email)]++;
				} else {
					$stats->emails[strtolower($email)] = 1;
				}
			}
		}
		*/
	}

	arsort($stats->emails);

	$writer->addNewSheetAndMakeItCurrent()->setName('STATS');

	$writer->addRow([
		'Recipient',
		'District Name',
		"Sent ({$stats->total})",
	]);

	foreach ($stats->emails as $email => $sent) {
		$rep = get_representative_by_email($email);
		$writer->addRow([
			$email,
			$rep ? $rep->district_name : '',
			$sent,
		]);
	}

	$writer->close();
	exit;
});

add_action('init', function(){
	global $wpdb;

	if (!is_user_logged_in() || empty($_GET['export_contact_form_submissions'])) {
		return;
	}

	// WPDB functions read the entire result set into memory instead
	// we are using the PDO connection and streaming the result set
	$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

	$statement = $pdo->query("
		SELECT *
		FROM {$wpdb->prefix}signups
		ORDER BY id ASC
	");

	$writer = \Box\Spout\Writer\WriterFactory::create(\Box\Spout\Common\Type::XLSX);

	$defaultStyle = (new \Box\Spout\Writer\Style\StyleBuilder)
		->setFontName('Calibri')
		->setFontSize(11)
		->setShouldWrapText(false)
		->build();

	$writer->setDefaultRowStyle($defaultStyle);
	$writer->openToBrowser('contact-form-'.time().'.xlsx');

	$writer->addRow([
		'Name',
		'Email',
		'Message',
		'IP Address',
		"Sent",
	]);

	while ($data = $statement->fetchObject()) {
		$writer->addRow([
			$data->name,
			$data->email,
			$data->message,
			$data->ip_address,
			date('m/d/Y H:i:s', strtotime($data->created_at)),
		]);
	}

	$writer->close();
	exit;
});


function get_representative_by_email($email){
	global $wpdb;

	$email = strtolower($email);
	$hash = 'opennorth:email:'.sha1($email);

	$representative = get_transient($hash);
	if ($representative) {
		return $representative;
	}

	$postal_code = $wpdb->get_var($wpdb->prepare("SELECT postal_code FROM {$wpdb->prefix}voices WHERE LOWER(sent_to) = %s LIMIT 1", $email));

	if ($postal_code) {
		try {
			$representatives = (new \IUOE\Represent)->getPoliticians($postal_code, null, null);
		} catch (Exception $e) {
			return;
		}

		if ($representatives && isset($representatives->provincial_rep)) {
			set_transient($hash, $representatives->provincial_rep, strtotime('+1 week') - time());
			return $representatives->provincial_rep;
		}
	}
}


function getBootstrapBreakpoints() {
  return array(
    'xs',
    'sm',
    'md',
    'lg',
    'xl',
  );
}

function get_display_classes($showSizes, $show = 'block', $hide = 'none'){
  $classes = [];
  foreach ($showSizes as $breakpoint => $display) {
    if ($display) {
      $classes[] = "d-$breakpoint-$show";
    } else {
      if ($breakpoint === 'xs') {
        $classes[] = "d-$hide";
      } else {
        $classes[] = "d-$breakpoint-$hide";
      }
    }
  }
  return implode(' ', $classes);
}


function showSizers() {
  if (!empty($_REQUEST['showSizers'])) {
    echo '<div class="fixed-bottom show-sizers">';
    $sizes = getBootstrapBreakpoints();
    $allOff = [];
    foreach ($sizes as $size) {
      $allOff[$size] = false;
    }
    foreach ($sizes as $size) {
      $theseBreaks = $allOff;
      $theseBreaks[$size] = true;
      $classes = get_display_classes($theseBreaks, 'inline-block');
      echo "<div class=\"$classes btn btn-info btn-lg sizer\">$size</div>";
    }

    echo '</div>';
  }
}
add_action( 'wp_footer', 'showSizers' );

function add_favicons() {
  $favicon_path = untrailingslashit(get_stylesheet_directory_uri()).'/favicon';
?>
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $favicon_path; ?>/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $favicon_path; ?>/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $favicon_path; ?>/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $favicon_path; ?>/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $favicon_path; ?>/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $favicon_path; ?>/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $favicon_path; ?>/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $favicon_path; ?>/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $favicon_path; ?>/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="<?php echo $favicon_path; ?>/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $favicon_path; ?>/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $favicon_path; ?>/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $favicon_path; ?>/favicon-16x16.png">
<link rel="manifest" href="<?php echo $favicon_path; ?>/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="<?php echo $favicon_path; ?>/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<?php
}
add_action( 'wp_head', 'add_favicons' );


