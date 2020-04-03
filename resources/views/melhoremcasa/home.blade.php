@extends('gentelella.layouts.app')


@section('content')
 
	<div class="row tile_count">
		<caixa 
			numero={{$qtd_pacientes}} icone="fas fa-user-injured" titulo="Total de Pacientes" 
			{{-- cor_percent="green"  percent="-10 " descricao=" semana passada"--}}>
		</caixa>

		<caixa 
			numero={{$qtd_pacientes_acompanhamento}} icone="fas fa-clipboard-check" titulo="Em Acompanhamento" 
			{{-- cor_percent="green" percent="67" descricao=" semana passada"--}}>
		</caixa>

		<caixa 
			numero={{ $vetor['qtd_visitas_MA'] }} icone="fas fa-medkit" titulo="Visitas no Mês" 
			{{-- cor_percent="red" --}} {{-- percent="{{ $vetor['percvisitas_mes']}} " descricao=" mês passado" --}}>
		</caixa>
	
	</div>
	
	
	<div class="row">
		<div class="x_panel modal-content ">
			<div id="grafico_visitas" style="width:100%; height:300%;"></div>
		</div>
	</div>
	
	<div class="row">
		<div class="x_panel modal-content " style="width:39%; margin:1px auto; ">
			<div id="grafico" style="width:100%; height:300%;"></div>
		</div>
		<div class="x_panel modal-content " style="width:30%; margin:1px auto;" >
			<div id="grafico2" style="width:100%; height:300%;"></div>
		</div>
		<div class="x_panel modal-content " style="height:322px; width:30%; margin:1px auto;">
			<div id="aa" style="width:100%; height:300%;">
				
				<table class="table table-hover table-striped tabela_compacta" id="tb_cids">
					<thead>
						<tr>
							<th>Código</th> 
							<th>Descrição</th>
						</tr>						
					</thead>

					<tbody>
						@foreach($gcid->reverse() as $key=> $cid)
							<tr>
								<td> {{$cid->codigo}}</td> 
								<td> {{$cid->descricao}}</td> 
							</tr>
						@endforeach
					</tbody>
				</table>
				</div>
		</div>
	</div>
	
	<div class="row">
		<div class="x_panel modal-content "id="address-map-container" style="width:100%;height:1200px; ">
			<div style="width: 100%; height: 100%" id="map"></div>
		</div>
	</div>

@endsection

@push('scripts')

	<script type="text/javascript">
		

		var geocoder;
		var map;
		
			function initMap() {
				var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 14,
				center: { lat: -22.782946, lng: -43.431588},
				});
				geocoder = new google.maps.Geocoder();
				codeAddress(geocoder, map);
			}

			var markers = [];
			console.log(markers);
			@foreach($pacientes as $paciente)
			
			var address = '{{$paciente->logradouro}}  {{$paciente->numero}} {{$paciente->bairro}} - mesquita' ;
			console.log(address);
			function codeAddress(geocoder, map) {
				
				geocoder.geocode({'address': address}, function(results, status) {
						console.log(results);
					if (status === 'OK') {
						var marker = new google.maps.Marker({
						map: map,
						position: results[0].geometry.location
						});

						markers.push(results);
					} else {
						alert('Geocode was not successful for the following reason: ' + status);
					}
				});
		}
		@endforeach


		// var map;
		// function initMap() {
		// 	map = new google.maps.Map(document.getElementById('map'), {
		// 		center: { lat: -22.782946, lng: -43.431588},
		// 		zoom: 14,
		// 		mapTypeControl: false,
		// 		animation: google.maps.Animation.DROP,
		// 		mapTypeId: google.maps.MapTypeId.roadmap,
		// 		scrollwheel: true, //we disable de scroll over the map, it is a really annoing when you scroll through page
		// 		styles: [{"featureType":"water","stylers":[{"saturation":43},{"lightness":-11},{"hue":"#0088ff"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"hue":"#ff0000"},{"saturation":-100},{"lightness":99}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#808080"},{"lightness":54}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ece2d9"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#ccdca1"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#767676"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#b8cb93"}]},{"featureType":"poi.park","stylers":[{"visibility":"on"}]},{"featureType":"poi.sports_complex","stylers":[{"visibility":"on"}]},{"featureType":"poi.medical","stylers":[{"visibility":"on"}]},{"featureType":"poi.business","stylers":[{"visibility":"simplified"}]}],
		// 	});
		// var markers = [];
		// @foreach($pacientes as $paciente)
		// @endforeach
		// }

		let legendas1 = [];
		let dados1 = [];
		
		let legendas2 = [];
		let dados2 = [];
		
	
		@foreach($gbairro as $v)
			legendas1.push("{{$v->bairro}}");
			dados1.push({value:{{$v->qtd}}, name:"{{$v->bairro}}"});
      	@endforeach

		@foreach($gcid as $v)
			legendas2.push("{{$v->codigo}}");
			dados2.push({value:{{$v->qtd}}, name:"{{$v->codigo}}", type:"bar"});
      	@endforeach




		let legendas_gr_visitas = [];
      	let series_gr_visitas = [];

		@foreach($visitas as $visita)
			legendas_gr_visitas.push({!! json_encode($visita->mes) !!});
			series_gr_visitas.push({!! json_encode($visita->quantidade) !!});
      	@endforeach





		//console.log(dados2) ;
		//console.log(legendas2) ;
				
		$(function(){

			var dom = document.getElementById("grafico2");
			var myChart = echarts.init(dom, 'macarons' );
			var app = {};
			option = null;

			option = {
				title: {
					text: 'CIDs com maior ocorrência',
					subtext: 'Total',
					left: 'center'
				},
				color: ['#3398DB'],
				tooltip: {
					trigger: 'axis',
					formatter: '{b} <br/>{c}<br/>',
					axisPointer: {            
						type: 'shadow'        
					}
				},

				legend: {
					data: legendas2
				},

			 	grid: {
					left: '3%',
					right: '4%',
					bottom: '3%',
					containLabel: true
				}, 

				yAxis: [
					{
						type: 'category',
						data: legendas2,
						axisTick: {
							alignWithLabel: true
						},
						
					}
				],
				xAxis: [
					{
						type: 'value'
					}
				],
		
				series: [
					{
						type: 'bar',
						barWidth: '60%',
						data: dados2
					}
				],
				animationEasing: 'elasticOut',
    			animationDelayUpdate: function (idx) {
       				 return idx * 5;
				}
			};

			
			if (option && typeof option === "object") {
				myChart.setOption(option, true);
			}



			//==========================================================================
			//==========================================================================
			//==========================================================================
			//==========================================================================
			//==========================================================================
			//==========================================================================
			//==========================================================================


			var dom = document.getElementById("grafico");
			var myChart = echarts.init(dom, 'macarons' );
			var app = {};
			option = null;

			option = {
				title: {
				text: 'Acompanhamentos por Bairro',
				subtext: 'Total',
				left: 'center'
			},
			tooltip: {
				trigger: 'item',
				formatter: '{a} <br/>{b} : {c} ({d}%)'
			},
			legend: {
				orient: 'vertical',
				left: 'left',
				data: legendas1
			},
			series: [
				{
					name: '',
					type: 'pie',
					radius: '60%',
					center: ['50%', '55%'],
					data: dados1,
					emphasis: {
						itemStyle: {
							shadowBlur: 10,
							shadowOffsetX: 0,
							shadowColor: 'rgba(0, 0, 0, 0.5)'
						}
					}
				}
			],
			animationEasing: 'elasticOut',
    			animationDelayUpdate: function (idx) {
       				 return idx * 5;
				}
			};

			
			if (option && typeof option === "object") {
				myChart.setOption(option, true);
			}


			/* ================================================================================= */
			/* ================================================================================= */
			/* ================================================================================= */
			/* ================================================================================= */
			/* ================================================================================= */
			/* ================================================================================= */


			var dom_visitas = document.getElementById("grafico_visitas");
			var myChart_visitas 	= echarts.init(dom_visitas, 'macarons' );
			var app = {};
			option2 = null;

			

			option2 = {
				title: {
					text: 'Quantidade de Visitas por Mês',
					/* subtext: 'Ultimos 12 meses', */
					x:'center'
				},
				tooltip: {
					trigger: 'axis',
					axisPointer: {
						type: 'cross'
					}
				},
				toolbox: {
					height : 6,
					show : true,
					itensize : 10,
					feature : {
						mark : {show: false},
						saveAsImage : {show: true, title: 'Salva Imagem'},
						dataView : {show: true, readOnly: true, title:'Dados'},
						magicType: {
							type: ['line', 'bar'],
							title: {
								line: 'Linha',
								bar: 'Barras',
							},
						},
						//restore : {show: true, title:'Restaura'},
					}
				},

				xAxis:  {
					type: 'category',
					boundaryGap: false,
					//data: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai','Jun','Jul','Ago','Set','Out', 'Nov' ,'Dez']
					data: legendas_gr_visitas,
					axisLabel:{textStyle:{fontSize:12}},
					nameRotate: 90,
				},
				yAxis: {
				},
				series: [{
					name: 'Valor',
					type: 'line',
					smooth: true,
					showSymbol: false,
					hoverAnimation: true,
					data: series_gr_visitas
				}]
			};

			
			if (option2 && typeof option2 === "object") {
				myChart_visitas.setOption(option2, true);
			}
 
		});


	</script>
@endpush