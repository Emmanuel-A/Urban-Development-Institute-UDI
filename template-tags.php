<?php

function get_query_loop($args = array()){
	return Enlighten_Loop::create($args);
}

function get_theme_url($path){
	$url = get_template_directory_uri() . '/' . ltrim($path, '/');
	return $url;
}

function the_theme_url($path){
	print get_theme_url($path);
}

function get_cached_busted_theme_url($path){
	$timestamp = @filemtime(get_template_directory() . '/' . ltrim($path, '/'));
	return get_theme_url("{$path}?t=".$timestamp);
}

function the_cached_busted_theme_url($path){
	print get_cached_busted_theme_url($path);
}

function get_template_with($template, array $vars = array()){
	extract($vars, EXTR_SKIP);
	require locate_template("{$template}.php");
}

function the_language_switcher($format = '<a class="languageswitcher" href="{url}" hreflang="{code}" title="{name}"><span>{name}</span></a>') {
	global $q_config;
	if (function_exists('qtranxf_getSortedLanguages')) {
		foreach(qtranxf_getSortedLanguages() as $language) {
			if ($language !== $q_config['language']) {
				return strtr($format, array(
					'{url}'  => qtranxf_convertURL('', $language, false, true),
					'{code}' => $language,
					'{name}' => $q_config['language_name'][$language],
				));
				break;
			}
		}
	}
}

function trans($text = '', $alt = ''){
	$locale = function_exists('qtranxf_getLanguage') ? qtranxf_getLanguage() : 'en';
	if (empty($text)) {
		return $locale;
	}
	if (empty($alt) === false) {
		$text = [$text, $alt];
	}
	if (is_array($text)) {
		$text = '[:en]' . $text[0] . '[:fr]' . $text[1];
	}
	return strtr(__($text), array('{locale}' => $locale));
}

function get_attachment_image($postID, $size = 'full', $background_image = false){
	$image = wp_get_attachment_image_src($postID, $size);
	if ($image) {
		if ($background_image) {
			return 'background-image: url(' . $image[0] .');';
		}
		return $image[0];
	}
}

function get_featured_image_caption(){
	$attachment = get_post(get_post_thumbnail_id(get_the_ID()));
	return trim($attachment->post_excerpt);
}

function get_featured_image($size = 'full', $background_image = false){
	$attachmentID = get_post_thumbnail_id(get_the_ID());
	return get_attachment_image($attachmentID, $size, $background_image);
}

function the_featured_image($size = 'full', $background_image = false){
	print get_featured_image($size, $background_image);
}

function get_image_field($key, $size = 'full', $background_image = false){
	return get_attachment_image(get_field($key), $size, $background_image);
}

function the_image_field($key, $size = 'full', $background_image = false){
	print get_image_field($key, $size, $background_image);
}

function get_sub_image_field($key, $size = 'full', $background_image = false){
	return get_attachment_image(get_sub_field($key), $size, $background_image);
}

function the_sub_image_field($key, $size = 'full', $background_image = false){
	print get_sub_image_field($key, $size, $background_image);
}
