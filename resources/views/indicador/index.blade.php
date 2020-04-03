@extends('gentelella.layouts.app')

@section('content')
   <div class="x_panel modal-content ">
	  	<div class="x_title">
		 	<h2><i class="fas fa-chart-line"></i> Indicadores </h2>
		 	<div class="clearfix"></div> 
		</div>
		<div class="x_panel">
			<label class="col-md-1 col-sm-1 col-xs-12" for="selecao_periodo" style="margin-top: 5px;" >Período</label>
			<div class="col-md-3 col-sm-3 col-xs-12">
				<select name = "selecao_periodo" id="selecao_periodo" class="form-control">
					@foreach($datas as $data)
						<option value="{{$data->periodo}}" selected="selected"> {{$data->mes}} - {{$data->ano}} </option>
					@endforeach
				</select>	
			</div>	
		</div>


		{{-- PERMANENCIA --}}
	    <div class="x_panel ">
		    <div class="x_content">
				<table class=" table table-hover table-striped compact" id="tb_permanencia">
					<thead>
						<tr>
							<th>Mês</th> 
							<th>Ano</th> 
							<th>Total de dias de permanência dos usuários que tiveram alta do SAD no período</th> 
							<th>Total de usuários que tiveram alta do SAD no mesmo período</th> 
							<th>Média de permanência (em dias)</th>
						</tr>						
					</thead>

					<tbody>
						
					</tbody>
				</table>
                
            </div>
		</div>

		{{-- ADMISSAO --}}
	    <div class="x_panel ">
		    <div class="x_content">
				<table class=" table table-hover table-striped compact" id="tb_admissao">
					<thead>
						<tr>
							<th>Mês</th> 
							<th>Ano</th> 
							<th>Total de usuários admitidos no SAD</th> 
							<th>Total de usuários classificados em AD1 na admissão</th> 
							<th>Total de usuários classificados em AD2 na admissão</th> 
							<th>Total de usuários classificados em AD3 na admissão</th> 
							<th>Percentual por AD</th> 
						</tr>						
					</thead>

					<tbody>
						
					</tbody>
				</table>
                
            </div>
		</div>
 
		{{-- ORIGEM --}}
		<div class="x_panel ">
		    <div class="x_content">
				<table class=" table table-hover table-striped compact" id="tb_origem">
					<thead>
						<tr>
							<th>Mês</th> 
							<th>Ano</th> 
							<th>Total de usuários admitidos no SAD</th> 
							<th>Atenção Básica</th> 
							<th>Internação Hospitalar</th> 
							<th>Urgência / Emergência</th> 
							<th>Espontânea</th> 
							<th>Percentual por Origem</th> 
						</tr>						
					</thead>

					<tbody>
						
					</tbody>
				</table>
                
            </div>
		</div>
 
		{{-- OBITO --}}
		<div class="x_panel ">
		    <div class="x_content">
				<table class=" table table-hover table-striped compact" id="tb_obito">
					<thead>
						<tr>
							<th>Mês</th> 
							<th>Ano</th> 
							<th>Total de usuários em acompanhamento no SAD</th> 
							<th>Total de óbitos</th> 
							<th>Percentual de óbitos</th> 
						</tr>						
					</thead>

					<tbody>
						
					</tbody>
				</table>
                
            </div>
		</div>
		
		{{-- BOTÕES --}}
		<div class="clearfix"></div>
		<div class="footer text-right"> {{-- col-md-3 col-md-offset-9 --}}
			<button type="cancel" id="btn_voltar" class="botoes-acao btn btn-round btn-primary" >
				<span class="icone-botoes-acao mdi mdi-backburger"></span>   
				<span class="texto-botoes-acao"> Voltar </span>
				<div class="ripple-container"></div>
			</button>
		</div>

    </div>
	<!-- /page content -->

@endsection


@push("scripts")
	
	{{-- Vanilla Masker --}}
	<script src="{{ asset('js/vanillaMasker.min.js') }}"></script>
	<script>
		$(document).ready(function(){

			//botão de voltar
			$("#btn_voltar").click(function(){
		      	event.preventDefault();
				//window.history.back();
				window.location.href = "{{ URL::route('home') }}";
			});

			$("select#selecao_periodo").change(function() {
				console.log("{{url('/indicador/permanencia')}}"  +"/" +$("select#selecao_periodo").val());

				datatables_permanencia.ajax.url("{{url('/indicador/permanencia')}}"  	+"/" +$("select#selecao_periodo").val()).load();
				datatables_admissao.ajax.url("{{url('/indicador/admissao')}}"  			+"/" +$("select#selecao_periodo").val()).load();
				datatables_origem.ajax.url("{{url('/indicador/origem')}}"  				+"/" +$("select#selecao_periodo").val()).load();
				datatables_obito.ajax.url("{{url('/indicador/obito')}}"  				+"/" +$("select#selecao_periodo").val()).load();
			});


			$.fn.dataTable.moment( 'DD/MM/YYYY' );
			//console.log( $("select#selecao_periodo").val() );

			/* PERMANENCIA */
			var datatables_permanencia = $("#tb_permanencia").DataTable({
				language : {'url' : '{{ asset('js/portugues.json') }}',"decimal": ",","thousands": "."}, 
				stateSave: 	true, stateDuration: -1, responsive: true, processing: true, serverSide: true, searching: false, paginate: false, ordering:	false, info: false, 
				ajax      : "{{ url('/indicador/permanencia') }}" +"/" +$("select#selecao_periodo").val(),
				columns   : [
					{ data : 'mes',    			name : 'mes' },
					{ data : 'ano',     		name : 'ano' },
					{ data : 'total_dias',   	name : 'total_dias' },
					{ data : 'total_alta',   	name : 'total_alta' },
					{ data : 'media',       	name : 'media' },
				],
							
				columnDefs: [
					{ "width": "10%", "targets": [0,1] },
					{ "width": "30%", "targets": [2,3] },
					{ "width": "20%", "targets": [4] },
				]
			});

			/* ADMISSAO  */
			var datatables_admissao = $("#tb_admissao").DataTable({
				language : {'url' : '{{ asset('js/portugues.json') }}',"decimal": ",","thousands": "."}, 
				stateSave: 	true, stateDuration: -1, responsive: true, processing: true, serverSide: true, searching: false, paginate: false, ordering:	false, info: false, 

				ajax      : "{{ url('/indicador/admissao') }}" +"/" +$("select#selecao_periodo").val(),
				columns   : [

					{ data : 'mes',    					name : 'mes' },
					{ data : 'ano',     				name : 'ano' },
					{ data : 'total_admitidos', 		name : 'total_admitidos' },
					{ data : 'total_ad1',   			name : 'total_ad1' },
					{ data : 'total_ad2',   			name : 'total_ad2' },
					{ data : 'total_ad3',   			name : 'total_ad3' },
					{ data : 'percentual_admissao',   	name : 'percentual_admissao' },
					

				],
							
				columnDefs: [
					{ "width": "10%", "targets": [0,1] },
					{ "width": "15%", "targets": [2,3,4,5] },
					{ "width": "20%", "targets": [6] },
					
				]
			});

			/* ORIGEM */
			var datatables_origem = $("#tb_origem").DataTable({
				language : {'url' : '{{ asset('js/portugues.json') }}',"decimal": ",","thousands": "."}, 
				stateSave: 	true, stateDuration: -1, responsive: true, processing: true, serverSide: true, searching: false, paginate: false, ordering:	false, info: false, 

				ajax      : "{{ url('/indicador/origem') }}" +"/" +$("select#selecao_periodo").val(),
				columns   : [

					{ data : 'mes',    					name : 'mes' },
					{ data : 'ano',     				name : 'ano' },
					{ data : 'total_admitidos', 		name : 'total_admitidos' },
					{ data : 'basica',   				name : 'basica' },
					{ data : 'hospital',   				name : 'hospital' },
					{ data : 'emergencia',   			name : 'emergencia' },
					{ data : 'espontanea',   			name : 'espontanea' },
					{ data : 'percentual_admissao',   	name : 'percentual_admissao' },
					

				],
							
				columnDefs: [
					{ "width": "10%", "targets": [0,1] },
					{ "width": "15%", "targets": [2] },
					{ "width": "10%", "targets": [3,4,5,6] },
					{ "width": "20%", "targets": [7] },
					
				]
			});
			  			
			/* OBITO */
			var datatables_obito = $("#tb_obito").DataTable({
				language : {'url' : '{{ asset('js/portugues.json') }}',"decimal": ",","thousands": "."}, 
				stateSave: 	true, stateDuration: -1, responsive: true, processing: true, serverSide: true, searching: false, paginate: false, ordering:	false, info: false, 

				ajax      : "{{ url('/indicador/obito') }}" +"/" +$("select#selecao_periodo").val(),
				columns   : [

					{ data : 'mes',    					name : 'mes' },
					{ data : 'ano',     				name : 'ano' },
					{ data : 'total_acompanhamento', 	name : 'total_acompanhamento' },
					{ data : 'total_obito', 			name : 'total_obito' },
					{ data : 'percentual_obito',   		name : 'percentual_obito' },
				],
							
				columnDefs: [
					{ "width": "5%", "targets": [0,1] },
					{ "width": "15%", "targets": [2] },
					{ "width": "10%", "targets": [3,4] },
				]
			});
			  						  

		});
		
	</script>

@endpush