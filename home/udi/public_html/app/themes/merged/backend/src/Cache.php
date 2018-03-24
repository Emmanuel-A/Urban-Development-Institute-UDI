<?php

namespace IUOE;

use Doctrine\Common\Cache\FilesystemCache;

class Cache extends FilesystemCache {

	/**
	 * Create an instance.
	 */
	public function __construct()
	{
		$upload_dir = wp_upload_dir();
		parent::__construct(trailingslashit($upload_dir['basedir']) . 'iuoe-cache');
	}

}

