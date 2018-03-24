
<div id="thank-mp" class="row section-thank-mp">
	<div class="col-sm-6">
		<div class="section-content">
			<?php the_sub_field('content') ?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-wrapper">
			<form name="thankMPStep1Form" role="form">
				
				<div class="alert alert-danger" style="display:none"></div>
				<div class="form-group">
					<label class="sr-only" for="thankMPStep1FormName"><?= trans('Enter Your Name', 'Nom' ) ?></label>
					<input type="text" class="form-control" id="thankMPStep1FormName" name="name" placeholder="<?= trans('Enter Your Name', 'Nom' ) ?>" required>
				</div>
				<div class="form-group">
					<label class="sr-only" for="thankMPStep1FormEmail"><?= trans('Enter Your Email' , 'Courriel') ?></label>
					<input type="email" class="form-control" id="thankMPStep1FormEmail" name="email" placeholder="<?= trans('Enter Your Email' , 'Courriel') ?>" required>
				</div>
				<div class="form-group">
					<label class="sr-only" for="thankMPStep1FormStreetAddress"><?= trans('Enter Your Street Address', 'Adresse') ?></label>
					<input type="text" class="form-control" id="thankMPStep1FormStreetAddress" name="street_address" placeholder="<?= trans('Enter Your Street Address', 'Adresse') ?>" required>
				</div>
				<div class="row">
					<div class="col-sm-6 form-group">
						<label class="sr-only" for="thankMPStep1FormCity"><?= trans('Your City', 'Ville') ?></label>
						<input type="text" class="form-control" id="thankMPStep1FormCity" name="city" placeholder="<?= trans('Your City', 'Ville') ?>" required>
					</div>
					<div class="col-sm-6 form-group">
						<label class="sr-only" for="thankMPStep1FormPostalCode"><?= trans('Your Postal Code', 'Code postal') ?></label>
						<input type="text" class="form-control" id="thankMPStep1FormPostalCode" name="postal_code" placeholder="<?= trans('Your Postal Code', 'Code postal') ?>" required pattern="[A-Za-z][0-9][A-Za-z] ?[0-9][A-Za-z][0-9]">
					</div>
				</div>
				<div class="form-group clearfix">
					<div class="radio radio-1">
						<input type="radio" id="thankMPStep1FormEngineerTrue" name="engineer" value="true" required>
						<label for="thankMPStep1FormEngineerTrue">
							<?= trans("I'm an Operating Engineer", "Je suis un opérateur-ingénieur") ?>
						</label>
					</div>
					<div class="radio radio-2">
						<input type="radio" id="thankMPStep1FormEngineerFalse" name="engineer" value="false" required>
						<label for="thankMPStep1FormEngineerFalse">
							<?= trans("I'm a Supporter", "Je suis un sympathisant") ?>
						</label>
					</div>
				</div>
				<div class="form-group checkbox">
					<label>
						<input type="checkbox" name="opt_in">
						<span>
						<?php if (trans() === 'fr'): ?>
							En cliquant sur « envoyer », vous acceptez de recevoir à l’occasion des communications
							faisant le point sur cette campagne et ses enjeux. Pour prendre connaissance de notre
							politique de protection de la vie privée
							<a href="<?= get_permalink(145) ?>" target="_blank">cliquez ici</a>
						<?php else: ?>
							By clicking submit, you agree to be contacted periodically with important
							updates on the campaign and related matters.
							<a href="<?= get_permalink(145) ?>" target="_blank">You can read our privacy statement by clicking here.</a>
						<?php endif ?>
						</span>
					</label>
				</div>
				<div class="form-group">
					<button type="submit" class="btn">
						<i class="fa fa-spinner fa-spin" style="display:none"></i>
						<?= trans('SAY THANK YOU', 'DITES MERCI') ?>
					</button>
				</div>
			</form>
			<form name="thankMPStep2Form" role="form" style="display:none">
				<p>
					<?= trans('Tell Your MPP To') ?>
					<span id="form-header">
						<?= trans('#HelpOntarioHear') ?>
					</span>
				</p>
				<div class="alert alert-danger" style="display:none"></div>
				<input type="hidden" name="voice">
				<div class="form-group checkbox">
					<label id="representativeConfirmation">
						<input type="checkbox" name="confirmation" required> <?= trans('I confirm that', 'Je confirme que') ?> <span></span> <?= trans('is my MP.', 'est mon député.') ?>
					</label>
				</div>
				<div class="preview"></div>
				<div class="form-group">
					<button type="submit" class="btn">
						<i class="fa fa-spinner fa-spin" style="display:none"></i>
						<?= trans('SEND TO MP', 'ENVOYER AU DÉPUTÉ') ?>
					</button>
				</div>
			</form>
			<div class="thankyou" style="display:none">
				<?php the_sub_field('thank_you_message') ?>
				<?php
					$permalink = home_url();
					$description = get_field('social_description','option');
				?>
				<div class="share">
					<a class="fb" data-platform="Facebook" data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($permalink) ?>"><span>Facebook</span></a>
					<a class="tw" data-platform="Twitter"  data-permalink="<?= esc_attr($permalink) ?>" target="_blank" href="https://twitter.com/intent/tweet?url=<?= urlencode($permalink) ?>&amp;text=<?= urlencode($description) ?>"><span>Twitter</span></a>
					<a class="ml" data-platform="Email"    data-permalink="<?= esc_attr($permalink) ?>" href="mailto:?subject=&body=<?= rawurlencode($description."\n\n".$permalink) ?>"><span>Email</span></a>
				</div>
			</div>
		</div>
	</div>
</div>



<?php while (have_rows('sections', get_option('page_on_front'))) : the_row() ?>

	<?php if (get_row_layout() === 'know_the_facts'): ?>
		<?php get_template_part('sections/know-the-facts') ?>
	<?php endif ?>

<?php endwhile ?>
