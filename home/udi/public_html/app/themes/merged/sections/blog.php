
<?php if (have_posts()): ?>

	<div class="row section-blog">
		<?php
			$loop = get_query_loop(array(
				'post_type'   => 'post',
				'post_status' => 'publish',
				'nopaging'    => true,
			));
		?>
		<?php while ($loop->have_posts()) : $loop->the_post() ?>

			<article>
				<div class="section-photo" style="<?php the_featured_image('full',true) ?>"></div>
				<div class="section-content">
					<div class="meta">
						<span class="author">
							<?php the_author() ?>
						</span>
						<time class="updated" datetime="<?php the_time('c') ?>" >
							<?php the_time('F j, Y') ?>
						</time>
					</div>
					<h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
					<div class="excerpt">
						<?= get_the_excerpt() ?>
						<a href="<?php the_permalink() ?>"><?= trans('Read More', 'En savoir plus') ?></a>
					</div>
				</div>
			</article>

		<?php endwhile ?>
	</div>

<?php endif ?>
