@extends('gentelella.layouts.app')


@section('content')
 
	

@endsection

@push('scripts')

	
    <script type="text/javascript">

        
                
        $(function(){

            //console.log("{{$pacientes[0]['situacao']}}") ;
            //endereco = "{{$pacientes[0]['logradouro']}} , {{$pacientes[0]['numero']}} - {{$pacientes[0]['bairro']}} - {{$pacientes[0]['cep']}} - mesquita - brasil";
            
            @foreach($pacientes as $paciente)

                @if ( is_null($paciente->latitude) ) 

                    var endereco = "{{$paciente->logradouro}} , {{$paciente->numero}} - {{$paciente->bairro}} - {{$paciente->cep}} - mesquita - brasil";
                    var paciente = "{{$paciente->id}}";

                    let pacientes = [];
                    //console.log(endereco) ;
                    $.get("https://maps.googleapis.com/maps/api/geocode/json", {
                        address:    endereco,
                        key:        'AIzaSyD88keSNZva3fJ2F01M6YOw78uf3xrtU1I'
                    },function(resultado){
                        if (resultado['status'] == 'OK') {
                            console.log(resultado);
                            console.log(resultado['results'][0]['geometry']['location']['lat']);

                            $.post('/api/adicionaGeocodePaciente',{
                                _token:	    '{{ csrf_token() }}',
                                id: 	    paciente,
                                latitude: 	resultado['results'][0]['geometry']['location']['lat'],
                                longitude: 	resultado['results'][0]['geometry']['location']['lng'],
                            },function(data){
                                console.log(data);    
                                /* if (data == 'OK') {
                                    console.log("atualizado");    
                                }else{
                                    
                                    console.log(data);    
                                } */
                            });
                        }
                    });
                
                    
                @endif
                

                

                

            @endforeach

        
        });


    </script>
@endpush