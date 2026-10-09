<div id="rating-aplikasi" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="display: block">
				{{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
				<h4 class="">Penilaian Aplikasi</h4>
			</div>
			<div class="modal-body">
				<center>
					<div>
						<span style="font-weight: bold;font-size: 18pt">
							Bagaimana penilaian Anda terhadap aplikasi SiBunda?
						</span>
					</div>
					<br><br>
					<div>
						<style type="text/css">
							.rating{
								position: block;
								top:50%;
								margin-left: 50%;
								transform: translate(-50%, -50%) rotateY(180deg);
								display: flex;
							}
							.rating input{
								display: none;
							}
							.rating label{
								display: block;
								cursor: pointer;
								width: 50px;
								/*background: #ccc;*/
							}
							.rating label:before{
								content:'\f005';
								font-family: fontAwesome;
								position: relative;
								display: block;
								font-size: 50px;
								color: #101010;
							}
							.rating label:after{
								content:'\f005';
								font-family: fontAwesome;
								position: absolute;
								display: block;
								font-size: 50px;
								color: #fffa00;
								top:0;
								opacity: 0;
								transition: .5s;
								text-shadow: 0 2px 5px rgba(0,0,0,.5);
							}
							.rating label:hover:after,
							.rating label:hover ~ label:after,
							.rating input:checked ~ label:after
							{
								opacity: 1;
							}
						</style>
						<div class="rating">
							<input type="radio" name="star" value="5" id="star1"><label for="star1"></label>
							<input type="radio" name="star" value="4" id="star2"><label for="star2"></label>
							<input type="radio" name="star" value="3" id="star3"><label for="star3"></label>
							<input type="radio" name="star" value="2" id="star4"><label for="star4"></label>
							<input type="radio" name="star" value="1" id="star5"><label for="star5"></label>
						</div>
					</div>
					<div style="display: block"></div>
					<div>
						<span style="font-weight: bold;font-size: 18pt">
							Saran terhadap aplikasi SiBunda?
						</span>
					</div>
					<div>
						<textarea class="form-control" name="saran" placeholder="Komentar dan saran anda"></textarea>
					</div>
				</center>
				{{-- <div style="margin-top: 5%;font-size: 9pt;font-style: italic;">
					FYI*
					Survey akan dilakukan setiap tahun, 
				</div> --}}
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-primary" onclick="simpan()">Simpan</button>
			</div>
		</div>

	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
	setTimeout(cek_survey(),2000);

	function cek_survey(){
		var data = {};
		$.post("{{route('common-rating-cek_survey')}}",data,function(data){
			if(data.code==200){

			}else{
				$('#rating-aplikasi').modal({backdrop: 'static', keyboard: false});
			}
		});
	}

	function simpan(){
		var bintang = $('input[name=star]:checked').val();
		var saran = $('textarea[name=saran]').val();
		var data = {
			bintang: bintang,
			saran: saran,
		};

		$.post("{{route('common-rating-simpan')}}",data,function(data){
			if(data.code==200){
				$('#rating-aplikasi').modal('hide');
			}
		});
	}
</script>