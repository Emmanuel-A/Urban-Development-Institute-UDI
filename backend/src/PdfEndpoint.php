<?php

namespace IUOE;

class PdfEndpoint
{

	/**
	 * Create an instance.
	 */
	public function __construct()
	{
		add_action('init',              array($this, 'init'));
		add_action('template_redirect', array($this, 'template_redirect'));

		add_filter('body_class', array($this, 'body_class'));
	}

	/**
	 * And the PDF endpoint rewrite.
	 */
	public function init()
	{
		add_rewrite_endpoint('pdf', EP_PERMALINK);
		//flush_rewrite_rules();
	}

	/**
	 * Handle the generation and download of the PDF.
	 */
	public function template_redirect()
	{
		global $post;

		if (!defined('WKHTMLTOPDF_BIN') || get_query_var('pdf', false) === false) {
			return;
		}

		$tmpfile = tempnam(sys_get_temp_dir(),'widex-').'.pdf';

		exec(escapeshellcmd(WKHTMLTOPDF_BIN).' '.escapeshellarg(get_permalink($post->ID).'?format=pdf').' '.escapeshellarg($tmpfile));

		header('Content-Description: File Transfer');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: public, must-revalidate, max-age=0');
		header('Pragma: public');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream', false);
		header('Content-Type: application/download', false);
		header('Content-Type: application/pdf', false);

		// don't use length if server using compression
		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) || empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			header('Content-Length: ' . filesize($tmpfile));
		}

		header('Content-disposition: attachment; filename="'.$post->post_name.'.pdf"');
		readfile($tmpfile);

		unlink($tmpfile);
		exit;
	}

	/**
	 * Add the format to the body class.
	 */
	public function body_class($classes)
	{
		if (!empty($_GET['format']) && $_GET['format'] === 'pdf') {
			$classes[] = 'format-pdf';
		}

		return $classes;
	}

}
