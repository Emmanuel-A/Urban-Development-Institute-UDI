
	</div><!-- /#document -->

	<footer class="footer" id="contentinfo" role="contentinfo">
	<div class="container">
		<div class="row">
        <div class="col-sm-12 col-md-3">
          <a href="<?php echo home_url(); ?>"> <img src="<?php site_url(); ?>/app/themes/merged/assets/img/cco-logo.jpg" class="bottomlogo" /> </a>
      	</div>
        <div class="col-sm-12 col-md-3">
		<p>155 Lynden Rd, Unit 2<br />
Brantford, ON. N3R 8A7<br />
Tel: (519) 752-2124 Ext.100<br />
            Fax: (519) 752-3649</p>
	</div>
	<div class="col-sm-12 col-md-3  footerlinks">

		<a href="http://www.careercollegesontario.ca/contact" target="_blank" class="contactfooter"><?= trans('Contact', 'Contacte') ?></a>
        <a href="<?php echo home_url(); ?>/privacy-policy" target="_blank"><?= trans('Privacy Policy', 'Politique de protection de la vie privée') ?></a>
	</div>
	<div class="col-sm-6">
	</div>
	</div></div>

		<div class="col-sm-10"></div>
			<div class="col-sm-2 float-right"><a href="#hero"><img src="<?php site_url(); ?>/app/themes/merged/assets/img/jump-to-top.jpg" class="jumptotop" /></a></div>

	</footer>

</div><!-- /#wrap -->


<div id="siteshare" style="display:none">
	<?php
		$permalink = home_url();
		$description = get_field('social_description','option');
	?>
	<div class="share">
		<a class="fb" data-platform="Facebook" data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($permalink) ?>"><span>Facebook</span></a>
		<a class="tw" data-platform="Twitter"  data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://twitter.com/intent/tweet?url=<?= urlencode($permalink) ?>&amp;text=<?= urlencode($description) ?>"><span>Twitter</span></a>
		<a class="ml" data-platform="Email"    data-permalink="<?= esc_attr($permalink) ?>" href="mailto:?subject=<?= trans('Make your voice heard!', 'Faites entendre votre voix!') ?>&body=<?= rawurlencode($description."\n\n".$permalink) ?>"><span>Email</span></a>
	</div>
</div>


<?php wp_footer() ?>

</body>
</html>
