<script setup>
import Timetable_viewer from "./components/timetable_viewer.vue";
</script>

<template>
  <header>QMB_Rooms</header>
  <div class="view">
    <Transition name="notice">
    <div class="room-note" v-if="rooms[room] !== undefined && notes[rooms[room].name] !== undefined">Please Note - {{notes[rooms[room].name]}}</div>
    </Transition>
    <marquee class="note"><i>{{notification}}</i></marquee>

      <div class="room-card" v-if="rooms[room] !== undefined">
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
  <timetable_viewer ref="timetable" :open="showtimetable" @close="showtimetable = false" :room="rooms[room]?.next?.roomID"></timetable_viewer>
</template>

<style scoped>

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
      "notification": "Platform in early access please excuse any issues.",
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
      fetch(that.root + "/get_notification.php").then((d)=>{d.json().then((dta)=>{that.notification = dta.notification})});
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
