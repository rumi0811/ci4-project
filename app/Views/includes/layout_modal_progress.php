<div id="modalLoadingInfo" style="position: absolute; top: 0; left: 0; display: none; width: 100%; height: 100%; background: #ffffff; opacity: 0.8; z-index: 9999">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; justify-content: center; align-items: center">
        <div id="LoadingInfoLottie" style="width: 80px; height: 80px;"></div>
        <div>Loading...</div>
    </div>
</div>


<script type="text/javascript">
    var loadingLottieAnimation = null;

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
		loadingLottieAnimation = lottie.loadAnimation({
			container: document.getElementById('LoadingInfoLottie'), // the dom element
			renderer: 'svg', // render using SVG
			loop: true,       // loop the animation
			autoplay: true,   // autoplay on load
			path: '<?php echo base_url(); ?>assets/lottie/loading.json' // replace with the correct path
		});
	});
	
    var ShowLoadingIndicator = function() {
        $("#modalLoadingInfo").fadeToggle(500, function() {
			if (loadingLottieAnimation != null) {
				loadingLottieAnimation.play();
			}
		}); // 500ms = duration of fade
    };

    var HideLoadingIndicator = function() {
        
        $("#modalLoadingInfo").fadeToggle(500, function(){
			if (loadingLottieAnimation != null)
			{
				setTimeout(function(){
					loadingLottieAnimation.stop();
				}, 100);
			}
		});
    };
</script>