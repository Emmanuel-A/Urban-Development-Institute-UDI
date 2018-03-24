
<div id="say-it-louder" align=""class="row section-text-with-photo content-position-<?php the_sub_field('photo_mask_position') ?> background-mask-<?php the_sub_field('photo_mask_type') ?>" style="<?php the_sub_image_field('photo','full',true) ?>">
	<div class="col-sm-5">
		<div class="section-content">
			<?php the_sub_field('content') ?>
			<?php
					$permalink = home_url();
					$description = get_field('social_description','option');
				?>
			<p class="sharesocial">
                <a class="fbshare" data-platform="Facebook" data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($permalink) ?>"><img src="<?php the_theme_url('assets/img/facebook.png') ?>" height="54" alt="Facebook"><br /><?php if (trans() === 'fr'): ?>
							Partager sur Facebook
						<?php else: ?>
							Share on Facebook
						<?php endif ?></a>
				<a class="twshare" data-platform="Twitter"  data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://twitter.com/intent/tweet?url=<?= urlencode($permalink) ?>&amp;text=<?= urlencode($description) ?>"><img src="<?php the_theme_url('assets/img/twitter.png') ?>" height="54" alt="Twitter"><br /><?php if (trans() === 'fr'): ?>
							Partager sur Twitter
						<?php else: ?>
							Tweet your Support
						<?php endif ?></a>
					
					
					
				</p>
		</div>
	</div>
</div>
