<template>
   
      <li class="">
         <a href="#" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            Notificações
            <span class=" fa fa-angle-down"></span>
         </a>
         <ul class="dropdown-menu dropdown-usermenu pull-right">
            <li v-for="notification in notifications" :key="notification">
               <a href="#"> {{ notification }}</a>
            </li>
           
            <li>
               <a href="#"> <i class="material-icons pull-right">clear</i> Limpar Notificações</a>
            </li>
           
         </ul>
      </li>
   
</template>

<script>
export default {
   computed: {
      notifications (){
         return [1,2,3,4,5,6,7,9,20,25];
      }
   }   
}
</script>


