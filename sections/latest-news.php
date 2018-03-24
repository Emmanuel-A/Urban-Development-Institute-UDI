
<?php if (have_posts()): ?>

	<div class="row section-latest-news">
		<?php
			$loop = get_query_loop(array(
				'post_type'   => 'news',
				'post_status' => 'publish',
				'nopaging'    => true,
			));
		?>
		<?php while ($loop->have_posts()) : $loop->the_post() ?>

			<article>
				<div class="section-photo" style="<?php the_featured_image('full',true) ?>"></div>
				<div class="section-content">
					<h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
					<div class="meta">
						<time class="updated" datetime="<?php the_time('c') ?>" >
							<?php the_time('M j, Y') ?>
						</time>
					</div>
				</div>
			</article>

		<?php endwhile ?>
	</div>

<?php endif ?>
