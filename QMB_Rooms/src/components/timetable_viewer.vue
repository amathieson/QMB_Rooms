<script setup>

</script>

<template>
  <div :open="open" class="dialog-backdrop" ref="backdrop" @click="close">
    <transition mode="in-out">
      <div :open="open" class="dialogue">
        <h1>{{ roomName }} - Week {{ week.number }} Timetable</h1>
        <div class="timeline">
          <div v-for="(grp,day) in days">
            <div class="separator">{{day}}</div>
            <div class="event" v-for="event in grp">
              <div>
                <h1>{{event.module.name === undefined ? event.title : event.module.name}}</h1>
                <sub>{{ event.module.code }}</sub>
                <h2>{{ event.staff }}</h2>
              </div>
              <div>
                <span>{{toTime(event.start*1000)}}</span>
                <span>{{toTime(event.end*1000)}}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  mounted() {
  },
  props: {
    "open":false,
    "room":""
  },
  data:()=>{
    return{
      "roomName":"",
      "days":[],
      "week":{
        "number":"",
        "dates":""
      }
    }
  },
  methods: {
    close(ev){
      if(ev.target === this.$refs.backdrop){
        this.$emit('close');
      }
    },
    toTime(tme) {
      const time = new Date(tme);
      return time.getHours() + ":"
          + ("0" + time.getMinutes()).slice(-2);
    }
  },
  watch: {
    room(){
      fetch(this.$root.$data.root + "/get_timetable.php?room=" + this.room).then((d)=>d.json().then((dta)=>{
        this.days = dta.days;
        this.week = dta.week;
        this.roomName = dta.room;
      }))
    }
  }
}
</script>

<style scoped>
.dialogue[open=true] {
  transform: translateY(0%);
  height: fit-content;
  max-height: 80vh;
  transition: transform 250ms ease-in-out, max-height 250ms ease-in-out;
}
.dialogue[open=false] {
  transform: translateY(1000%);
  max-height: 0;
  transition: transform 250ms ease-in-out, max-height 250ms ease-in-out;
}
.dialogue {
  border: 0;
  padding: 1em;
  width: calc(100% - 2em);
  margin: auto 0 0;
  transform: translateY(100%);
  transition: transform 250ms ease-in-out, max-height 250ms ease-in-out;
  outline: none;
  background: var(--background);
  max-height: 0;
  z-index: 10;
  position: absolute;
  overflow: auto;
  bottom: 0;
  text-align: left;
}

.dialog-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  width: 0;
  height: 0;
  overflow: hidden;
  z-index: 5;
  background: rgba(0,0,0,0.5);
  opacity: 0;
  pointer-events: none;
  transition: opacity 250ms ease-in-out 0ms, width 0ms linear 250ms, height 0ms linear 250ms;
}

.dialog-backdrop[open=true] {
  pointer-events: auto;
  opacity: 1;
  width: 100%;
  height: 100%;
  transition: opacity 250ms ease-in-out 0ms, width 0ms linear 0ms, height 0ms linear 0ms;
}

.dialog-backdrop[open=false] {
  pointer-events: auto;
  opacity: 0;
  width: 0;
  height: 0;
}

.v-enter-active,
.v-leave-active {
  transition: opacity 250ms ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}

.dialogue > h1 {
  font-size: 16pt;
}

.timeline {
  max-height: 60vh;
  overflow-y: scroll;
}

.timeline > div > .separator {
  background: rgba(128, 128, 128, 0.8);
  text-align: center;
  font-weight: bold;
  margin-top: 0.25em;
}

.timeline > div > .event {
  display: grid;
  grid-template-columns: 80% 20%;
  border-bottom: rgba(128, 128, 128, 0.8) solid 1px;
}
.event > div > h1 {
  margin: 0.5em 0 0;
  font-size: 12pt;
  line-height: 1;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}
.event > div > h2 {
  font-size: 11pt;
  line-height: 1;
  margin: 0 0 0.5em;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}
.event > div:nth-of-type(2) {
  display: flex;
  flex-direction: column;
  text-align: right;
  justify-content: space-around
}

@media (width >= 1024px) {
  .timeline::-webkit-scrollbar {
    width: 20px;
  }

  .timeline::-webkit-scrollbar-track {
    background-color: transparent;
  }

  .timeline::-webkit-scrollbar-thumb {
    background-color: rgba(128, 128, 128, 0.8);
    border-radius: 20px;
    border: 6px solid transparent;
    background-clip: content-box;
  }

  .timeline::-webkit-scrollbar-thumb:hover {
    background-color: rgba(128, 128, 128, 1);
  }
}
</style>