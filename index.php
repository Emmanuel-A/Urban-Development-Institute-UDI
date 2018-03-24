<?php get_header(); ?>

<div class="container py-5">
  <div class="row">
    <div class="col-12">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <h1 class="display-3">
          <?php the_title(); ?>
        </h1>
        <div class="content">
          <?php the_content(); ?>
        </div><!-- /.content -->
      <!-- post -->
      <?php endwhile; ?>
      <!-- post navigation -->
      <?php else: ?>
      <!-- no posts found -->
      <?php endif; ?>
    </div><!-- /.col-12 -->
  </div><!-- /.row -->
</div><!-- /.container -->
<?php get_footer(); ?>
