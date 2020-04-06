<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Acesso;
use App\Models\Local;
use App\Models\Setor;
use App\Models\User;

if (! function_exists('proximaVisita')) {
   function proximaVisita($dt, $atendimento_domiciliar) {

      $data = new DateTime($dt); // Y-m-d
      
      //$a = json_decode($acompanhamento) ;

      //return $dt;
      //return $acompanhamento;

      
      switch ($atendimento_domiciliar) {
         case 'AD1':
            $data->add(new DateInterval('P90D'));
            return $data->format('Y-m-d');
            break;

         case 'AD2':
            $data->add(new DateInterval('P30D'));
            return $data->format('Y-m-d');
            break;

         case 'AD3':
            $data->add(new DateInterval('P7D'));
            return $data->format('Y-m-d');
            break;
         
         default:
            $data->add(new DateInterval('P0D'));
            return $data->format('Y-m-d');
            break;
      }
      return $a;
  
      


   }
}

if (! function_exists('pegaValorEnum')) {
   function pegaValorEnum($table, $column, $ordena = false) {
      $type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '{$column}'"))[0]->Type ;
         preg_match('/^enum\((.*)\)$/', $type, $matches);
         $enum = array();
         foreach( explode(',', $matches[1]) as $value )
         {
            $v = trim( $value, "'" );
            $enum[] = $v;
         } 

      if($ordena){
         sort($enum);
      }
         
      return $enum;
   }
}

if (! function_exists('calculaIdade')) {
   function calculaIdade($nascimento = null, $obito = null) {

      if($nascimento != null){
         // separando yyyy, mm, ddd do NASCIMENTO
         list($ano, $mes, $dia) = explode('-', $nascimento);

         // Descobre a unix timestamp da data de nascimento do fulano
         $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);


         if($obito != null){
            // separando yyyy, mm, ddd do OBITO
            list($obito_ano, $obito_mes, $obito_dia) = explode('-', $obito);

            // Descobre a unix timestamp da data de nascimento do fulano
            $obito = mktime( 0, 0, 0, $obito_mes, $obito_dia, $obito_ano);
            

            // cálculo
            $idade = intval(floor((((($obito - $nascimento) / 60) / 60) / 24) / 365.25));
            //echo "Idade: $idade Anos";
            

         }else{

            // data atual
            $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
      
      
            // cálculo
            $idade = intval(floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25));
            //echo "Idade: $idade Anos";
               
         }

         return $idade;
      }else{
         return null;
      }
   }
}

if (! function_exists('diasEntreDatas')) {
   function diasEntreDatas($data1, $data2) {
      if($data1 == null)
         $data1 = date("Y-m-d");
      

      // Calcula a diferença em segundos entre as datas
      $diferenca = strtotime($data1) - strtotime($data2);

      //Calcula a diferença em dias
      $dias = floor($diferenca / (60 * 60 * 24));
      return $dias;
   }
}


if (! function_exists('retiraMascaraCPF')) {
   function retiraMascaraCPF($cpf) {
      $cpf = trim($cpf);
      $cpf = str_replace(".", "", $cpf);
      $cpf = str_replace("-", "", $cpf);
      return $cpf;
   }
}



if (! function_exists('formataTelefone')) {
   function formataTelefone($TEL){
      $tam = strlen(preg_replace("/[^0-9]/", "", $TEL));
      if ($tam == 13) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS e 9 dígitos
         return "+".substr($TEL,0,$tam-11)."(".substr($TEL,$tam-11,2).")".substr($TEL,$tam-9,5)."-".substr($TEL,-4);
      }
      if ($tam == 12) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS
         return "+".substr($TEL,0,$tam-10)."(".substr($TEL,$tam-10,2).")".substr($TEL,$tam-8,4)."-".substr($TEL,-4);
      }
      if ($tam == 11) { // COM CÓDIGO DE ÁREA NACIONAL e 9 dígitos
         return "(".substr($TEL,0,2).")".substr($TEL,2,5)."-".substr($TEL,7,11);
      }
      if ($tam == 10) { // COM CÓDIGO DE ÁREA NACIONAL
         return "(".substr($TEL,0,2).")".substr($TEL,2,4)."-".substr($TEL,6,10);
      }
      if ($tam <= 9) { // SEM CÓDIGO DE ÁREA
         return substr($TEL,0,$tam-4)."-".substr($TEL,-4);
      }
   }
}


if (! function_exists('formataCPF_CNPJ')) {
   function formataCPF_CNPJ($cnpj_cpf)
   {
   if (strlen(preg_replace("/\D/", '', $cnpj_cpf)) === 11) {
      $response = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
   } else {
      $response = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
   }

   return $response;
   }
}

if (! function_exists('parametros')) {
   function parametros()
   {
      $parametros = [];
      $local = Local::where('nome', substr( Auth::user()->roles[0]->sistema->nome, 4))->first();
      
      $parametros = [
         'nome_modulo'  => $local->nome,
         'modulo'       => $local,
         'setores'      => Setor::where('local_id', $local->id)->get() ,
      ];
      
      return $parametros;
   }
}

if (! function_exists('tirarAcentos')) {
   function tirarAcentos($string){
      return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
  }
}


function pega_ip() 
{

   $ip;
   if     (getenv("HTTP_CLIENT_IP"))         $ip = getenv("HTTP_CLIENT_IP");
   else if(getenv("HTTP_X_FORWARDED_FOR"))   $ip = getenv("HTTP_X_FORWARDED_FOR"); 
   else if(getenv("REMOTE_ADDR"))            $ip = getenv("REMOTE_ADDR");
   else                                      $ip = "UNKNOWN";
   return $ip;
}

