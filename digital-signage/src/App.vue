<script setup>
</script>

<template>
  <div class="image-container">
    <img :src="image">
  </div>
  <div class="container">
    <h1 class="room">{{room}}</h1>
    <img src="./assets/UoD-White.svg" class="logo">
    <div class="lower-section">
      <div>
        <h2>{{module}}</h2>
        <sub>{{staff}}</sub>
      </div>
      <h3>{{ time }}</h3>
    </div>
  </div>
</template>
<script>
import CS11001 from "./assets/modules/CS11001.png";
export default {
  data: ()=>{
    return {
      time: "",
      module: "Front End Website Development module (CS11001)",
      staff: "Samuel, Oluwafemi,Janjic, Vladimir,z A N Other (Comp 1)",
      room: "Queen Mother Building - Lab 0",
      image: CS11001
    }
  },
  mounted() {
    setInterval(()=>{
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
.container {
  display: grid;
  width: 100vw;
  height: 100vh;
  position: absolute;
  top: 0;
  left: 0;
  grid-template-rows: 10vh auto 25vh;
  grid-template-columns: auto 25vw;
  z-index: 100;
}
.container > * {
  text-align: left;
  max-height: 100%;
}
.lower-section {
  grid-row: 3;
  grid-column: 1 / 3;
  background: rgba(255, 255, 255, 0.87);
  color: black;
  padding: 2em;
  display: grid;
  grid-template-columns: auto 15vw;
  grid-template-rows: auto auto;
  box-shadow: #1a1a1ab0 0 0 20px 0;
}
h1, h2, h3 {
  margin: 0;
}
h3 {
  text-align: center;
  font-size: 3em;
  grid-row: 1 / 3;
  grid-column: 2;
  align-self: center;
}
.image-container {
  width: 100vw;
  height: 100vh;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 50;
  display: grid;
  justify-content: center;
}
.logo {
  padding: 1.5vw;
  justify-self: end;
}
h1 {
  padding: 1.5vw;
}
h2 {
  font-size: 2em;
}
sub {
  color: #4365e2;
  font-weight: bold;
}
.image-container > img {
  width: 110vw;
  opacity: .5;
  filter: brightness(0.8) contrast(0.5) blur(5px);
}
</style>
