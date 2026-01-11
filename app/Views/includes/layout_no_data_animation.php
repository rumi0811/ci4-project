<div class="NoDataContainer" style="display: none; margin: 0 auto;">
    <div style="display: flex; justify-content: center; align-items: center">
        <div id="NoDataLottie" style="width: 80px; height: 80px;"></div>
        <div>There is no data</div>
    </div>
</div>


<script type="text/javascript">
    var noDataLottieAnimation = null;

	if (typeof window.loadLottieIfNeeded !== 'function') {
		window.loadLottieIfNeeded = function(callback) {
			if (window.lottie) {
				// Lottie is already loaded
				callback(window.lottie);
			} else {
				// Load the Lottie script dynamically
				const script = document.createElement('script');
				script.src = '<?php echo base_url(); ?>assets/js/lottie.min.js';
				script.onload = () => {
					callback(window.lottie);
				};
				document.head.appendChild(script);
			}
		};
	}

	// Usage: load and then render animation
	loadLottieIfNeeded(function(lottie) {
		noDataLottieAnimation = lottie.loadAnimation({
			container: document.getElementById('NoDataLottie'), // the dom element
			renderer: 'svg', // render using SVG
			loop: true,       // loop the animation
			autoplay: true,   // autoplay on load
			path: '<?php echo base_url(); ?>assets/lottie/no_data.json' // replace with the correct path
		});
	});
	
    var ShowNoData = function() {
        $(".NoDataContainer").fadeToggle(1000, function() {
			if (noDataLottieAnimation != null) {
				noDataLottieAnimation.play();
			}
		}); 
    };

    var HideNoData = function() {
        
        $(".NoDataContainer").fadeToggle(1000, function(){
			if (noDataLottieAnimation != null)
			{
				setTimeout(function(){
					noDataLottieAnimation.stop();
				}, 100);
			}
		});
    };
</script>