
<div id="spread-the-word" class="row section-know-the-facts" style="<?php the_sub_image_field('photo','full',true) ?>">
	<div class="section-content">
		<?php the_sub_field('content') ?>
	</div>
	<div class="pins">
		<?php while (have_rows('facts')) : the_row() ?>

		<div class="pin-content" data-pin="<?php the_row_index() ?>" <?php if (get_row_index() > 1) print 'style="display:none"' ?>>
			<div class="inner">
				<?php the_sub_field('content') ?>
				<?php if (get_sub_field('infographic')): ?>
					<div class="infographic">
						<img src="<?php the_sub_image_field('infographic') ?>" alt="">
					</div>
				<?php endif ?>
				<div class="social">
					<?php
						$permalink = home_url('/share/'.get_sub_field('share_identifier').'/');
						$description = get_sub_field('share_content');
					?>
					<div class="share">
						<a class="fb" data-platform="Facebook" data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($permalink) ?>"><span>Facebook</span></a>
						<a class="tw" data-platform="Twitter"  data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://twitter.com/intent/tweet?url=<?= urlencode($permalink) ?>&amp;text=<?= urlencode($description) ?>"><span>Twitter</span></a>
						<a class="ml" data-platform="Email"    data-permalink="<?= esc_attr($permalink) ?>" href="mailto:?subject=&body=<?= rawurlencode($description."\n\n".$permalink) ?>"><span>Email</span></a>
					</div>
					<!--
					<div class="fb-like" data-href="<?= esc_attr($permalink) ?>" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="false"></div>
					-->
				</div>
			</div>
		</div>

		<?php endwhile ?>
	</div>
	<div class="section-pins" style="display:none">
		<?php while (have_rows('facts')) : the_row() ?>

		<div class="pin <?php if (get_row_index() === 1) print 'active' ?> pin-<?php the_row_index() ?>">
			<button data-pin="<?php the_row_index() ?>" data-share="<?php the_sub_field('share_identifier') ?>">
				<span class="sr-only">Pin</span>
			</button>
		</div>

		<?php endwhile ?>
	</div>
</div>
