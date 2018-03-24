<?php

class Enlighten_Loop {

	public function __construct($posts, $query = null)
	{
		$this->posts = $posts;
		$this->post_count = count($posts);
		$this->current_post = -1;
		$this->original_post = @$GLOBALS['post'];
		$this->query = $query ?: (object)array('max_num_pages' => 1);
	}

	public function have_posts()
	{
		if ($this->current_post + 1 < $this->post_count) {
			return true;
		}
		$this->reset();
		return false;
	}

	public function the_post()
	{
		$this->current_post += 1;
		setup_postdata($GLOBALS['post'] = $this->posts[$this->current_post]);
	}

	public function reset()
	{
		$this->current_post = -1;
		if ($this->original_post) {
			setup_postdata($GLOBALS['post'] = $this->original_post);
		}
	}

	public function the_pagination(array $args = array())
	{
		if (function_exists('wp_bootstrap_pagination')) {
			wp_bootstrap_pagination(array('custom_query' => $this->query));
		}
	}

	public static function create($args = array())
	{
		// use get_post()
		if (is_numeric($args)) {
			$args = get_post($args);
			return new self(array($args));
		}
		// use existing an WP_Query
		if (is_object($args) and $args instanceof WP_Query) {
			return new self($args->posts, $args);
		}
		// check for an existing array of posts
		if (is_array($args) and isset($args[0]->ID)) {
			return new self($args);
		}
		// create a new WP_Query using get_post defaults
		// as the defaults for the new query
		if (is_array($args)) {
			$wpq = new WP_Query(wp_parse_args($args, array(
				'numberposts'      => 5,
				'offset'           => 0,
				'category'         => 0,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'include'          => array(),
				'exclude'          => array(),
				'meta_key'         => '',
				'meta_value'       => '',
				'post_type'        => 'post',
				'suppress_filters' => true
			)));
			return new self($wpq->posts, $wpq);
		}
		// use the wp_query
		if (is_object($GLOBALS['wp_query']) and $GLOBALS['wp_query'] instanceof WP_Query) {
			return new self($GLOBALS['wp_query']->posts, $GLOBALS['wp_query']);
		}
		// use the post
		return new self(array($GLOBALS['post']));
	}

}
