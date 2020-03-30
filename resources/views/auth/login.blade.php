<!DOCTYPE html>
<html lang="pt-br">
	@section('htmlheader_title', 'Login')
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	
		<title>DashBoards</title>
	
		<!--     Fonts and icons     -->
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons" rel="stylesheet">
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		
		
		{{-- datatables --}}
			<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/zf/dt-1.10.18/r-2.2.2/datatables.min.css"/>
	
			{{-- bootstrap-datetimepicker --}}
			{{-- <link rel="stylesheet" href="{{ asset('/bootstrap-datetimepicker/bootstrap-datetimepicker.css') }}">       --}}
		
			{{-- jquery-timepicker --}}
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker@1.11.14/jquery.timepicker.min.css">       
			
		 <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
		 <link rel="stylesheet" href="{{ asset('/css/login.css') }}">
		 
	</head>

	<body class="login">
		<div id="app"> 
			<a class="hiddenanchor" id="signup"></a>
			<a class="hiddenanchor" id="signin"></a>
			<div class="cor_fundo_roxo" style="width:100%; height:150px; text-align: center;">
				<img class="logo_topo" src="{{ asset("img/logoretangular.jpg") }}">
			</div>
			<div class="row">  
				<div class="col-lg-12">
					@if($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{$error}}</li>
								@endforeach
							</ul>
						</div>
					@endif
					@if(session('msg'))
						<div class="alert alert-danger">
							{{session('msg')}}
						</div>
					@endif
				</div>
			</div>
			<div class="login_wrapper">
				{{--  login  --}}
				<div class="animate form login_form">
					<section class="login_content">
                     <form action="{{ url('/entrar')}}" method="POST">
                        {!! csrf_field() !!}
							
							<h1 class="cor_texto_roxo">DashBoards</h1>
							
							<div>
                           <label for="password" class="form-group">Email</label>   
							<input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>	
						</div>
							<div>
                           <label for="password" class="form-group">Senha</label>
						   <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required autocomplete="off">
							</div>
							<div>	
								<button type="submit" class="btn btn-default submit">
									Entrar
								</button>               
							</div>
							<div class="clearfix"></div>
							<div class="separator">
								<div class="clearfix"></div>
								<br/>
								
								<div>
									<h5><i  style="font-size: 18px" class="fab fa-free-code-camp"></i> Equipe de Desenvolvimento de Sistemas</h5>
									<h6> Subsecretaria da Tecnologia da Informação - Prefeitura Municipal de Mesquita - RJ </h6>
									<h6> Rua Arthur Oliveira Vecchi, 120 - Centro - Mesquita - RJ - CEP: 26553-080</h6>
								</div>
							</div>
						</form>
					</section>
				</div>
		
			</div>
		</div>
	</body>
</html>
