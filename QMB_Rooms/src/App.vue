<script setup>
import Timetable_viewer from "./components/timetable_viewer.vue";
</script>

<template>
  <header>QMB_Rooms</header>
  <div class="view">
    <Transition name="notice">
    <div class="room-note" v-if="notes[rooms[room].name] !== undefined">Please Note - {{notes[rooms[room].name]}}</div>
    </Transition>
    <marquee class="note"><i>Platform in early access please excuse any issues.</i></marquee>

      <div class="room-card">
        <Transition mode="out-in">
          <h3 :key="rooms[room].name">{{ rooms[room].name.startsWith("Lab") ? "Lab" : "" }}</h3>
        </Transition>
        <Transition mode="out-in">
          <h1 :key="rooms[room].name">{{rooms[room].name.startsWith("Lab") ? rooms[room].name.substring(4) : rooms[room].name}}</h1>
        </Transition>

        <Transition mode="out-in">
          <span :key="rooms[room].name" class="state" :data-state="rooms[room].next.start <= dateSec && rooms[room].next.end > dateSec ? 'busy' : (rooms[room].next.start <= dateSec+60*60 && rooms[room].next.end > dateSec ? 'soon':'free')">&bullet;</span>
        </Transition>
        <Transition mode="out-in">
          <sub :key="rooms[room].name" v-if="rooms[room].next.start >= 9223372036854776000">No more scheduled classes.</sub>
          <sub :key="rooms[room].name" v-else-if="rooms[room].next.start <= dateSec && rooms[room].next.end > dateSec">Busy until at least {{ new Date(rooms[room].next.end*1000).getHours() }}:{{ ("0" + new Date(rooms[room].next.end*1000).getMinutes()).slice(-2) }}</sub>
          <sub :key="rooms[room].name" v-else>Free until {{ new Date(rooms[room].next.start*1000).getHours() }}:{{("0" + new Date(rooms[room].next.start*1000).getMinutes()).slice(-2) }} <span v-if="!sameDay(new Date(rooms[room].next.start*1000), new Date())">On {{days[new Date(rooms[room].next.start*1000).getDay()]}}</span></sub>
        </Transition>
      </div>

      <div class="room-list">
        <div class="room" v-for="rm in rooms" @click="room = rooms.indexOf(rm);">
          <h5>{{ rm.name.startsWith("Lab") ? "Lab" : "&nbsp;" }}</h5>
          <h4 v-if="rm.name.startsWith('Lab')">{{rm.name.substring(4)}}</h4>
          <h5 v-else>{{ rm.name }}</h5>
          <span class="state" :data-state="rm.next.start <= dateSec && rm.next.end > dateSec ? 'busy' : (rm.next.start <= dateSec+60*60 && rm.next.end > dateSec ? 'soon':'free')">&bullet;</span>
        </div>
      </div>
      <sub class="foot">Last Updated: <code>{{dateFormat(last_fetch)}}</code> - Data Pulled: <code>{{dateFormat(data_pulled*1000)}}</code></sub>
  </div>
  <div class="fab icon" @click="showtimetable=true">📅</div>
  <timetable_viewer ref="timetable" :open="showtimetable" @close="showtimetable = false" :room="rooms[room].next.roomID"></timetable_viewer>
</template>

<style scoped>
.fab {
  position: absolute;
  background: #ff3333;
  padding: 0.5em;
  border-radius: 50%;
  font-size: 18pt;
  right: 1em;
  bottom: 2em;
  z-index: 5;
  box-shadow: #1a1a1a 0 0 16px 2px;
  color: #ffffff;
  aspect-ratio: 1;
  width: 24pt;
  height: 24pt;
  cursor: pointer;
}
.fab:active {
  background: #ff3333AA;
}
.fab:hover {
  background: #ff3333CC;
}
.room-list {
  width: 80vw;
  display: grid;
  grid-template-columns: repeat(7, calc(80vw / 7));
  align-items: center;
  justify-items: center;
  margin: auto;
}
.state {
  display: block;
  font-size: 3em;
  color: rgb(128, 128, 128);
}
.room {
  font-size: 10pt;
  display: grid;
  grid-template-rows: 1em 4em 1em;
  align-items: center;
  width: 100%;
  cursor: pointer;
  padding: 1em 0;
  transition: background 250ms ease;
}
.room:active {
  background: rgba(0,0,0,1);
}
.room:hover {
  background: rgba(0,0,0,0.2);
}
.room > h4 {
  font-size: 2em;
}

.state[data-state=free] {
  color: green;
}
.state[data-state=soon] {
  color: orange;
}
.state[data-state=busy] {
  color: red;
}
.room-card {
  font-size: 18pt;
  width: 80vw;
  margin: 0 auto 2em ;
}
.room-card > h1 {
  margin: 0;
}
.room-card > sub {
  font-size: 0.75em;
}
header {
  width: 100%;
  height: 36pt;
  background: #646cff;
  top: 0;
  left: 0;
  position: absolute;
  font-size: 24pt;
  font-weight: bold;
  color: #ffffff;
}
.foot {
  display: block;
  margin-top: 5em;
  width: 100%;
}
.view {
  position: absolute;
  top: 36pt;
  left: 0;
  max-height: calc(100vh - 36pt);
  width: 100%;
  overflow-y: auto;
}
.room-note {
  margin: auto;
  width: fit-content;
  padding: 1.5em;
  background: hsl(56 75% 61% / 1);
  color: #242424;
  font-weight: bold;
}

.v-enter-active,
.v-leave-active {
  transition: opacity 250ms ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
.notice-enter-active,
.notice-leave-active {
  transition: transform 250ms ease;
}

.notice-enter-from,
.notice-leave-to {
  transform: translateY(-100%);
}
</style>
<script>
export default {
  computed: {
    dateSec: ()=>{
      return Date.now() / 1000;
    }
  },
  data: ()=>{
    return {
      "showtimetable":false,
      "data_pulled":0,
      "last_fetch":0,
      "room":0,
      "rooms": [
        {
          "name":"Lab 0",
          "next":{}
        },
        {
          "name":"Lab 1",
          "next":{}
        },
        {
          "name":"Lab 2",
          "next":{}
        },
        {
          "name":"Lab 3",
          "next":{}
        },
        {
          "name":"Lab 4",
          "next":{}
        },
        {
          "name":"Lab 5",
          "next":{}
        },
        {
          "name":"Seminar Room",
          "next":{}
        }
      ],
      root: "https://adam.mathieson.dev/qmb",
      // root: "http://localhost:8080",
      days: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
      notes: {}
    }
  },
  mounted() {
    this.fetchData(this);
  },
  methods: {
    fetchData: (that)=>{
      fetch(that.root + "/get_notices.php").then((d)=>{d.json().then((dta)=>{that.notes = dta})});
      fetch( that.root + "/get_availabilities.php").then((d)=>{d.json().then((dta)=>{
        let arr = [];
        for (const dtaKey in dta.rooms) {
          let rm = dta.rooms[dtaKey];
          arr.push(
              {
                "name":rm.room,
                "next":rm
              });
        }
        that.rooms = arr;
        that.last_fetch = Date.now();
        that.data_pulled = dta.pull_time;
        setTimeout(()=>{that.fetchData(that)}, 60*1000);
        })
      })
    },
    sameDay: (d1, d2)=> {
      return d1.getFullYear() === d2.getFullYear() &&
          d1.getMonth() === d2.getMonth() &&
          d1.getDate() === d2.getDate();
    },
    dateFormat: (date)=> {
      var currentdate = new Date(date);
      return currentdate.getDate() + "/"
          + (currentdate.getMonth()+1)  + "/"
          + currentdate.getFullYear() + " @ "
          + currentdate.getHours() + ":"
          + ("0" + currentdate.getMinutes()).slice(-2) + ":"
          + ("0" + currentdate.getSeconds()).slice(-2);
    }
  }
}
</script>
