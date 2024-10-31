jQuery(document).ready(function($){
	$('.fs-permissions .fs-trigger').on('click', function () {
		$('.fs-permissions').toggleClass('fs-open');
		return true;
	});
});
