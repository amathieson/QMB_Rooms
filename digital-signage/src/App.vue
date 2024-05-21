<script setup>
</script>

<template>
  <div class="image-container">
    <img :src="image">
  </div>
  <div class="container">
    <h1 class="room">{{ room }}</h1>
    <img src="./assets/UoD-White.svg" class="logo">
    <div class="lower-section">
      <div>
        <h2>{{ module }}</h2>
        <sub>{{ staff }}</sub>
      </div>
      <h3>{{ time }}</h3>
    </div>
  </div>
</template>
<script>
import CS11001 from "./assets/modules/CS11001.png";

export default {
  data: () => {
    return {
      time: "",
      module: "Front End Website Development module (CS11001)",
      staff: "Samuel, Oluwafemi,Janjic, Vladimir,z A N Other (Comp 1)",
      room: "Queen Mother Building - Lab 0",
      image: CS11001
    }
  },
  mounted() {
    setInterval(() => {
      const date = new Date();
      let hours = date.getHours();
      let minutes = date.getMinutes();
      hours = hours <= 9 ? `${hours}`.padStart(2, 0) : hours;
      minutes = minutes <= 9 ? `${minutes}`.padStart(2, 0) : minutes;
      this.time = `${hours}:${minutes}`
    })
  }
}
</script>

<style scoped>
</style>
