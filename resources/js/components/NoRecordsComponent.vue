<template>
    <center>

        <v-img src="/img/empty.svg" width="120"></v-img>
        <h2>{{title}}</h2>
        <p class="text-disabled pa-3">{{description}}</p>
    </center>
</template>

<script>
export default {
    props:{
      title:{
          default:"No Records",
          type:String
      } ,
      description:{
          default:"No records found",
          type:String
      }
    },
    name: "NoRecordsComponent"
}
</script>

<style scoped>

</style>
