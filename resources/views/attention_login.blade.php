<style type="text/css">
	.blob {
		background: red;
		width: 100%;

		box-shadow: 0 0 0 0 rgba(0, 0, 0, 1);
		transform: scale(1);
		animation: pulse 2s infinite;
	}

	@keyframes pulse {
		0% {
			/*transform: scale(0.95);*/
			box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7);
		}

		70% {
			/*transform: scale(1);*/
			box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
		}

		100% {
			/*transform: scale(0.95);*/
			box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
		}
	}
</style>
<!--<div class="blob">
	<div style="width: 100%">
		<img src="https://profilsekolahdispendik.surabaya.go.id/attention.png" style="width: 100%">
	</div>
</div>-->