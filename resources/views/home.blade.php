@extends('gentelella.layouts.app')


@section('content')
 
	
	<div class="x_panel modal-content ">
      {{-- Grafico Melhor em Casa --}}
      <div id="grafico" style="width:100%; height:300%;">
         <div id="grafico" style="width:100%; height:300%;"></div>
      </div>
      {{-- FIM Grafico Melhor em Casa --}}
   </div>

@endsection

@push('scripts')

<script type="text/javascript">
   let legendas1 = [];
   let dados1 = [];
   
   @foreach($gbairro as $v)
      legendas1.push("{{$v->bairro}}");
      dados1.push({value:{{$v->qtd}}, name:"{{$v->bairro}}"});
   @endforeach
  
   $(function(){
   
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
      

   });
</script>


@endpush