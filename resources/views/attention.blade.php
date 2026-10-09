<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="row">
				<div class="col-lg-12 col-md-12 m-0">
					<img src="https://profilsekolahdispendik.surabaya.go.id/attention.png" style="width: 100%">
				</div>
			</div>
			<div class="row">
				<div style="width: 100%">
					<div style="height: 5px;background: red;" id="loading_notif">&nbsp;</div>
				</div>
			</div>			
			<div class="row" id="tutup_notif">
			</div>
		</div>
	</div>
</div>

<script src="{{asset('assets/js/core/jquery.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		/*$('#myModal').modal({display: 'show',keyboard: false,backdrop: 'static'});
		hitung_mundur(0,3);*/
	});	

	function hitung_mundur(i,n){
		if(i<n){
			setTimeout(function(){
				var persen = (((i+1)/n)*100).toFixed(0);
				$('#loading_notif').css({'width':persen+'%'});
				i = (i+1);
				console.log(i+' '+n+' '+persen);
				hitung_mundur(i,n);
			},1000);
			$('#tutup_notif').html('<div style="width: 100%;text-align: center"><span>Anda dapat menutup pesan ini dalam ... '+(n-i)+' detik</span></div>');
		}else{
			$('#tutup_notif').html('<button type="button" style="width: 100%;background: #ffafaf" class="btn btn-default" data-dismiss="modal">Tutup</button>');
		}
	}
</script>
