@extends('gentelella.layouts.app')

{{--  Page Content  --}}
@section('content')


	
	<div class="row tile_count">
		<caixa 
			numero={{ $qtdVeiculos }} icone="fa fa-car" titulo="Total de Veículos" >
		</caixa>

		<caixa 
			numero={{ $vetor['qtd_abast_SA'] }} icone="fas fa-gas-pump" titulo="Abast. na Semana" 
			{{-- cor_percent="red" --}} percent="{{ $vetor['perc_qtd_abast_sem']}} " descricao=" semana passada">
		</caixa>

		<caixa 
			numero={{ $vetor['qtd_abast_MA'] }} icone="fas fa-gas-pump" titulo="Abast. no Mês" 
			{{-- cor_percent="red" --}} percent="{{ $vetor['perc_qtd_abast_mes']}} " descricao=" mês passado">
		</caixa>
		

		<caixa 
			numero=R${{ number_format($vetor['sum_abast_SA'], 3, ',', '.')  }} icone="fas fa-dollar-sign" titulo="Gasto na Semana" 
			{{-- cor_percent="green" --}} percent="{{ $vetor['perc_sum_abast_sem']}} " descricao=" semana passada">
		</caixa>
	

		<caixa 
			numero=R${{ number_format($vetor['sum_abast_MA'], 3, ',', '.')  }} icone="fas fa-dollar-sign" titulo="Gasto no Mês" 
			{{-- cor_percent="green" --}} percent="{{ $vetor['perc_sum_abast_mes']}} " descricao=" mês passado">
		</caixa>
	</div>

	<div class="x_panel modal-content ">
		<div id="grafico" style="width:100%; height:300%;"></div>
	</div>

	<div class="x_panel modal-content ">
		<div id="grafico2" style="width:100%; height:300%;"></div>
	</div>

	

@endsection

@push('scripts')
	{{-- <script src="{{ asset('js/Chart.min.js') }}"></script> --}}
	{{-- <script src="{{ asset('js/echarts.min.js') }}"></script> --}}
	
	<script type="text/javascript">

		
		
		let valor_total_mensal = [];
		let legendas = [];
     	 let series = [];
		
		let legendas_gr2 = [];
      	let series_gr2 = [];


		@foreach($valor_total_mensal as $valor)
			valor_total_mensal.push({!! json_encode($valor) !!});
			legendas.push({!! json_encode($valor->mes) !!});
			series.push({!! json_encode($valor->total) !!});
     	@endforeach

		@foreach($abastecimentos as $abastecimento)
			legendas_gr2.push({!! json_encode($abastecimento->mes) !!});
			series_gr2.push({!! json_encode($abastecimento->quantidade) !!});
      	@endforeach


		$(function(){
			 
			var dom = document.getElementById("grafico");
			var myChart = echarts.init(dom, 'macarons' );
			var app = {};
			option = null;

			

			option = {
				title: {
					text: 'Gasto Total',
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
					data: legendas
				},
				yAxis: {
				},
				series: [{
					name: 'Valor',
					type: 'line',
					smooth: true,
					showSymbol: false,
					hoverAnimation: true,
					data: series,
					
				}]
			};

			
			if (option && typeof option === "object") {
				myChart.setOption(option, true);
			}



			/* =================================================================================================== */	

			var dom2 		= document.getElementById("grafico2");
			var myChart2 	= echarts.init(dom2, 'macarons' );
			var app = {};
			option2 = null;

			

			option2 = {
				title: {
					text: 'Quantidade de Abastecimentos por Semana',
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
					data: legendas_gr2,
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
					data: series_gr2
				}]
			};

			
			if (option2 && typeof option2 === "object") {
				myChart2.setOption(option2, true);
			}

		});


	</script>
@endpush