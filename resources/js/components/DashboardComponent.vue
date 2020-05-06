<template>
  <div class="col-md-8 col-md-offset-2">
    <div class="no-projects">
      <!-- タイマー開始ボタン -->
      <button
        class="btn btn-success btn-sm pull-right"
        :disabled="counter.timer"
        data-toggle="modal"
        data-target="#timerCreate"
      >
        <i class="fas fa-play"></i>
      </button>

      <!-- タイマー停止ボタン -->
      <button v-if="counter.timer" class="btn btn-sm btn-danger" @click="stopTimer()">
        <i class="fas fa-stop"></i>
      </button>

      <!-- 計算中タイマー表示 -->
      <div v-if="timers">
        <div class="panel panel-default" v-if="timers && !timers[0].stopped_at">
          <div class="panel-heading clearfix">
            <h4 class="pull-left">記録中</h4>
          </div>

          <div class="panel-body">
            <ul class="list-group">
              <li class="list-group-item clearfix">
                <strong class="timer-name">{{ timers[0].name }}</strong>
                <span
                  :style="{ backgroundColor: timers[0].category_color }"
                  style="display: inline-block; height:10px; width:10px; "
                ></span>
                <div class="pull-right">
                  <span style="margin-right: 10px">
                    <strong>{{ activeTimerString }}</strong>
                  </span>
                  <button v-if="counter.timer" class="btn btn-sm btn-danger" @click="stopTimer()">
                    <i class="fas fa-stop"></i>
                  </button>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- タイマー一覧を表示 -->
      <div v-if="timers && timers.length > 0">
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <h4 class="pull-left">学習の記録</h4>
          </div>

          <div class="panel-body">
            <ul class="list-group" v-if="timers.length > 0">
              <li v-for="timer in timers" :key="timer.id" class="list-group-item clearfix">
                <strong>{{ timer.name }}</strong>
                <br />
                <div>{{ timer.memo }}</div>
                <span
                  :style="{ backgroundColor : timer.category_color }"
                  style="display: inline-block; height:10px; width:10px; "
                ></span>
                <span :style="{ color: timer.category_color }">{{ timer.category_name }}</span>
                <div>
                  {{ formatTime(timer.started_at) }} -
                  {{ formatTime(timer.stopped_at) }}
                </div>
                <div class="pull-right">
                  <span v-if="showTimer(timer)" style="margin-right: 10px">
                    <strong>{{ activeTimerString }}</strong>
                  </span>
                  <span v-else style="margin-right: 10px">
                    <strong>{{ calculateTimeSpent(timer) }}</strong>
                  </span>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div v-else>
        <h4 class="pull-left">まだ記録がありません。タイマーをセットして記録を始めましょう。</h4>
      </div>
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">First</th>
            <th scope="col">Last</th>
            <th scope="col">Handle</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">1</th>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
          </tr>
          <tr>
            <th scope="row">2</th>
            <td>Jacob</td>
            <td>Thornton</td>
            <td>@fat</td>
          </tr>
          <tr>
            <th scope="row">3</th>
            <td colspan="2">Larry the Bird</td>
            <td>@twitter</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create Timer Modal -->
    <div class="modal fade" id="timerCreate" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h4 class="modal-title">新規作成</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <p>学習内容</p>
              <input
                v-model="newTimerName"
                type="text"
                class="form-control"
                placeholder="今から何をしますか？"
              />
              <br />
              <p>カテゴリーを選択する</p>
              <select v-model="newTimerCategory">
                <option disabled value>カテゴリーを選択してください</option>
                <option value :value="{ id: null, name: null }">カテゴリーを選択しない</option>
                <option
                  v-for="category in categories"
                  :value="{
                              id: category.id,
                              name: category.name,
                              color: category.color
                            }"
                >{{ category.name }}</option>
              </select>
              <br />
              <br />
              <button
                class="btn btn-link btn-sm center-block"
                data-toggle="modal"
                data-target="#categoryCreate"
                data-backdrop="false"
              >カテゴリーを新規作成する</button>
              <br />
              <p>メモ</p>
              <textarea
                v-model="newTimerMemo"
                type="text"
                class="form-control"
                placeholder="メモを入力してください"
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button
              data-dismiss="modal"
              v-bind:disabled=" newTimerName === '' || newTimerCategory === '' "
              @click="createTimer()"
              type="submit"
              class="btn btn-default btn-primary"
            >
              <i class="fas fa-play"></i>
              Start
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="categoryCreate" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h4 class="modal-title">カテゴリーを登録する</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <p>カテゴリーを新規登録</p>
              <input
                v-model="newCategoryName"
                type="text"
                class="form-control"
                placeholder="カテゴリー名を入力してください"
              />
              <br />
              <p>カラーを選択してください</p>
              <v-swatches
                v-model="newCategoryColor"
                show-fallback
                row-length="10"
                popover-y="up"
                shapes="circles"
                swatch-size="30"
                fallback-ok-text="OK"
              ></v-swatches>
            </div>
          </div>
          <div class="modal-footer">
            <button
              data-dismiss="modal"
              v-bind:disabled="newCategoryName === ''"
              @click="createCategory()"
              type="submit"
              class="btn btn-default btn-primary"
            >登録</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import VSwatches from "vue-swatches";
import "vue-swatches/dist/vue-swatches.css";

export default {
  components: {
    VSwatches
  },
  data() {
    return {
      timers: null,
      categories: null,
      newTimerName: "",
      newTimerMemo: "",
      newTimerCategory: "",
      newCategoryColor: "#1CA085",
      newCategoryName: "",
      activeTimerString: "Calculating...",
      counter: { seconds: 0, timer: null }
    };
  },
  created() {
    window.axios.get("/timers").then(response => {
      this.timers = response.data;
      window.axios.get("/timers/active").then(response => {
        if (response.data.id !== undefined) {
          this.startTimer(response.data);
        }
      });
    });
    window.axios.get("/categories").then(response => {
      this.categories = response.data;
    });
  },
  methods: {
    /**
     * Conditionally pads a number with "0"
     */
    _padNumber: number => (number > 9 ? number : "0" + number),

    /**
     * Splits seconds into hours, minutes, and seconds.
     */
    _readableTimeFromSeconds: function(seconds) {
      const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10);
      return {
        hours: this._padNumber(hours),
        seconds: this._padNumber(seconds % 60),
        minutes: this._padNumber(parseInt(seconds / 60, 10) % 60)
      };
    },

    /**
     * Calculate the amount of time spent on the project using the timer object.
     */
    calculateTimeSpent: function(timer) {
      if (timer.stopped_at) {
        const started = moment(timer.started_at);
        const stopped = moment(timer.stopped_at);
        const time = this._readableTimeFromSeconds(
          parseInt(moment.duration(stopped.diff(started)).asSeconds())
        );
        return `${time.hours}:${time.minutes}:${time.seconds}`;
      }
      return "";
    },

    /**
     * Determines if there is an active timer
     * passed into the function.
     */
    showTimer: function(timer) {
      return this.counter.timer && this.counter.timer.id === timer.id;
    },

    /**
     * Start counting the timer. Tick tock.
     */
    startTimer: function(timer) {
      const started = moment(timer.started_at);

      this.counter.timer = timer;
      this.counter.seconds = parseInt(
        moment.duration(moment().diff(started)).asSeconds()
      );
      this.counter.ticker = setInterval(() => {
        const time = this._readableTimeFromSeconds(++this.counter.seconds);

        this.activeTimerString = `${time.hours}:${time.minutes}:${time.seconds}`;
      }, 1000);
    },

    /**
     * Stop the timer from the API and then from the local counter.
     */
    stopTimer: function() {
      window.axios.post(`/timers/stop`).then(response => {
        // Stop the activeTimer
        this.timers[0].stopped_at = response.data.stopped_at;

        // Stop the ticker
        clearInterval(this.counter.ticker);

        // Reset the counter and timer string
        this.counter = { seconds: 0, timer: null };
        this.activeTimerString = "Calculating...";
      });
    },

    /**
     * Create a new timer.
     */
    createTimer: function() {
      window.axios
        .post(`/timers`, {
          name: this.newTimerName,
          memo: this.newTimerMemo,
          category_id: this.newTimerCategory["id"],
          category_name: this.newTimerCategory["name"]
            ? this.newTimerCategory["name"]
            : "カテゴリー未登録",
          category_color: this.newTimerCategory["color"]
            ? this.newTimerCategory["color"]
            : "#E1E2EB"
        })
        .then(response => {
          // project.timers.push(response.data); //後で消す
          this.timers.unshift(response.data);
          this.startTimer(response.data);
        });

      this.newTimerName = "";
      this.newTimerMemo = "";
      this.newTimerCategory = "";
    },

    /**
     * Create a new category.
     */
    createCategory: function() {
      window.axios
        .post("/categories", {
          name: this.newCategoryName,
          color: this.newCategoryColor
        })
        .then(response => this.categories.push(response.data));

      this.newCategoryName = "";
    },

    /**
     * Format time.
     */
    formatTime: function(date) {
      if (date) {
        const d = new Date(date);
        const h = d.getHours();
        const m = d.getMinutes();
        return `${this._padNumber(h)}:${this._padNumber(m)}`;
      }
      return "";
    }
  }
};
</script>
<style scoped>
.modal-header .close {
  padding: 0px;
  margin: 0px;
}
.modal-title {
  margin: 0 auto;
}
</style>
