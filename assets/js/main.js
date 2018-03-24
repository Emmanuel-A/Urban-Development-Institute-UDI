

// analytics event tracking function
function trackEvent(eventCategory, eventAction, eventLabel, eventValue){
	console.log('GOOGLE_ANALYTICS', 'event', eventCategory, eventAction, eventLabel, eventValue);
	if (typeof ga === 'function'){
		ga('send', 'event', eventCategory, eventAction, eventLabel, eventValue);
	}
}

// analytics social tracking function
function trackSocial(socialNetwork, socialAction, socialTarget){
	console.log('GOOGLE_ANALYTICS', 'social', socialNetwork, socialAction, socialTarget);
	if (typeof ga === 'function'){
		ga('send', 'social', socialNetwork, socialAction, socialTarget);
	}
}


jQuery(function($) {


	$('#menu-primary-navigation .menu-item a').matchHeight();


	$.fn.validator.Constructor.FOCUS_OFFSET = 60;

	// enable validation of forms
	$('form').validator({ disable: true });


	// handle submission and validation of add your voice form
	$('form[name="addYourVoiceStep1Form"]').on('submit', function(e){
		if (e.isDefaultPrevented() === false) {
			e.preventDefault();

			var $form = $(this);
			var $btn = $form.find('button');
			var $spin = $btn.find('i');
			var $warn = $form.find('.alert-danger');

			$btn.prop('disabled',true);
			$spin.show();

			$.post(API_ENDPOINT + '?action=add_your_voice_step1', $form.serializeObject()).then(
				function(data){
					var $next = $('form[name="addYourVoiceStep2Form"]');
					$form.hide();
					$next.find('input[name="voice"]').val(data.voice);
					$next.find('.checkbox span').html(data.representative.name);
					$next.find('.preview').html(data.letter);
					$next.show();
					$.fn.matchHeight._update();
					trackEvent('MPP Form', 'Step 1', $('form[name="addYourVoiceStep1Form"] input[name="profession"]').val());
					fbq('trackCustom', 'MPP_INPROGRESS');
				},
				function(error){
					if (error.responseJSON && error.responseJSON.error) {
						$warn.html(error.responseJSON.error).show();
					}
					$form[0].reset();
					$btn.prop('disabled',false);
					$spin.hide();
				});
		}
	});


	// handle submission and validation of add your voice form
	$('form[name="addYourVoiceStep2Form"]').on('submit', function(e){
		if (e.isDefaultPrevented() === false) {
			e.preventDefault();

			var $form = $(this);
			var $btn = $form.find('button');
			var $spin = $btn.find('i');
			var $warn = $form.find('.alert-danger');

			$btn.prop('disabled',true);
			$spin.show();

			$.post(API_ENDPOINT + '?action=add_your_voice_step2', $form.serializeObject()).then(
				function(){
					var $thankyou = $form.hide().next().show();
					$.fn.matchHeight._update();
					$('html,body').stop().animate({ 'scrollTop': $thankyou.offset().top - 100 }, 900, 'swing');
					trackEvent('MPP Form', 'Step 2', $('form[name="addYourVoiceStep1Form"] input[name="profession"]').val());
					fbq('trackCustom', 'MPP_COMPLETE');
				},
				function(error){
					if (error.responseJSON && error.responseJSON.error) {
						$warn.html(error.responseJSON.error).show();
					}
					$btn.prop('disabled',false);
					$spin.hide();
				});
		}
	});


	// handle submission and validation of thank mp form
	$('form[name="thankMPStep1Form"]').on('submit', function(e){
		if (e.isDefaultPrevented() === false) {
			e.preventDefault();

			var $form = $(this);
			var $btn = $form.find('button');
			var $spin = $btn.find('i');
			var $warn = $form.find('.alert-danger');

			$btn.prop('disabled',true);
			$spin.show();

			$.post(API_ENDPOINT + '?action=thank_mp_step1', $form.serializeObject()).then(
				function(data){
					var $next = $('form[name="thankMPStep2Form"]');
					$form.hide();
					$next.find('input[name="voice"]').val(data.voice);
					$next.find('.checkbox span').html(data.representative.name);
					$next.find('.preview').html(data.letter);
					$next.show();
					trackEvent('Thank MP Form', 'Submit', 'Step 1');
				},
				function(error){
					if (error.responseJSON && error.responseJSON.error) {
						$warn.html(error.responseJSON.error).show();
					}
					$form[0].reset();
					$btn.prop('disabled',false);
					$spin.hide();
				});
		}
	});


	// handle submission and validation of thank mp form
	$('form[name="thankMPStep2Form"]').on('submit', function(e){
		if (e.isDefaultPrevented() === false) {
			e.preventDefault();

			var $form = $(this);
			var $btn = $form.find('button');
			var $spin = $btn.find('i');
			var $warn = $form.find('.alert-danger');

			$btn.prop('disabled',true);
			$spin.show();

			$.post(API_ENDPOINT + '?action=thank_mp_step2', $form.serializeObject()).then(
				function(){
					var $thankyou = $form.hide().next().show();
					$('html,body').stop().animate({ 'scrollTop': $thankyou.offset().top - 100 }, 900, 'swing');
					trackEvent('Thank MP Form', 'Submit', 'Step 2');
				},
				function(error){
					if (error.responseJSON && error.responseJSON.error) {
						$warn.html(error.responseJSON.error).show();
					}
					$btn.prop('disabled',false);
					$spin.hide();
				});
		}
	});


	// handle submission and validation of contact form
	$('form[name="contactForm"]').on('submit', function(e){
		if (e.isDefaultPrevented() === false) {
			e.preventDefault();

			var $form = $(this);
			var $btn = $form.find('button');
			var $spin = $btn.find('i');
			var $warn = $form.find('.alert-danger');

			$btn.prop('disabled',true);
			$spin.show();

			$.post(API_ENDPOINT + '?action=contact_us', $form.serializeObject()).then(
				function(){
					$form.hide().next().show();
					trackEvent('Contact Form', 'Submit');
				},
				function(error){
					if (error.responseJSON && error.responseJSON.error) {
						$warn.html(error.responseJSON.error).show();
					}
					$btn.prop('disabled',false);
					$spin.hide();
				});
		}
	});


	// setup smooth scrolling for anchor links
	$('body.home a[href*="#"], a[href^="#"]').on('click', function(e){
		e.preventDefault();
		var target = this.hash, offset = $(target).offset();
		if (offset) {
			$('html,body').stop().animate({ 'scrollTop': $(target).offset().top }, 900, 'swing', function(){
				window.location.hash = target;
			});
		}
	});


	// setup the share icons to open in a popup
	$('body').on('click', '.share a[target="_blank"]', function(e){
		e.preventDefault();
		var $this = $(this);
		window.open($this.attr('href'), '_blank', 'height=300,width=550,resizable=1');
		if ($this.data('platform') && $this.data('permalink')) {
			trackSocial($this.data('platform'), 'Share', $this.data('permalink'));
		}
	});


	function sharePopover(e, $this){
		e.preventDefault();
		if (!$this.data('bs.popover')) {
			$this.popover({
				placement: 'bottom',
				trigger: 'manual',
				html: true,
				content: function(){
					return $('#siteshare').html();
				}
			});
			$this.popover('hide');
		}
		return $this;
	}

	// setup the share button to use popover
	$('#menu-item-59').on('mouseenter', function(e){
		sharePopover(e, $(this).find('a')).popover('show');
	});
	$('#menu-item-59').on('mouseleave', function(e){
		sharePopover(e, $(this).find('a')).popover('hide');
	});

	// setup siteshare links to use popover
	$('body').on('click', 'a.siteshare', function(e){
		sharePopover(e, $(this)).popover('toggle');
	});


	// setup the spacing for the pins
	var $pins = $('.section-pins .pin');

	$pins.each(function(index){
		$(this).css({ left: (100/$pins.length*index) + '%' });
	});

	$pins.parent().show();

	// wire up the section pins
	$('.section-pins').on('click', 'button[data-pin]', function(e){
		var $this = $(this);
		$('.pin-content').hide().filter('[data-pin="' + $this.data('pin') + '"]').show();
		$this.parent().addClass('active').siblings().removeClass('active');
		trackEvent('Fact Pin', 'Click', $this.data('share'));
	});

	// toggle for profession
	$('.dropdown').on({
    'mouseover' : function() {
			$('.dropbtn').attr('placeholder',$('.dropbtn').data('placeholder'));
			$('.dropdown-content').show();
			$('#pointer').show();
    },
    mouseout : function() {
			$('.dropbtn').attr('placeholder',$('.dropbtn').data('placeholder'));
			$('.dropdown-content').hide();
			$('#pointer').hide();
    }
  });

	// selecting type of profession
	$('.dropdown-content a').on('click', function(){
		$('input[name="profession"]').val($(this).text());
		$('.dropbtn').val($(this).text());
		$('.dropdown-content').hide();
	});

	$('#mobile-profession').on('change', function(){
		$('input[name="profession"]').val($(this).val());
	});
});
