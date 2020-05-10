<template>
  <div class="container mx-auto">
    <!-- 上部Snackbar -->
    <!-- 記録完了時 twitterシェア -->
    <v-snackbar top v-model="doneTimerSnackbar" color="info">
      お疲れ様でした。この記録を友達にシェアしましょう！
      <v-btn
        text
        @click="doneTimerSnackbar = false"
        href="https://twitter.com/home"
        　target="_blank"
      >
        <v-icon>mdi-twitter</v-icon>
      </v-btn>
    </v-snackbar>
    <!-- 更新完了時 -->
    <v-snackbar top v-model="updateTimerSnackbar" color="success">
      更新しました。
      <v-btn text @click="updateTimerSnackbar = false">閉じる</v-btn>
    </v-snackbar>
    <!-- 削除完了時 -->
    <v-snackbar top v-model="deleteTimerSnackbar" color="error">
      記録を削除しました。
      <v-btn text @click="deleteTimerSnackbar = false">閉じる</v-btn>
    </v-snackbar>

    <!-- 右下部Snackbar -->
    <!-- タイマー開始ボタン -->
    <v-hover v-slot:default="{ hover }" v-if="isEmpty(counter.timer)">
      <v-fab-transition>
        <v-btn
          @click.stop="newTimerDialog = true"
          color="light-green accent-3"
          fab
          fixed
          x-large
          dark
          bottom
          right
          :elevation="hover ? 12 : 6"
        >
          <v-icon x-large>mdi-play</v-icon>
        </v-btn>
      </v-fab-transition>
    </v-hover>

    <!-- 計算中タイマー -->
    <div v-if="!isEmpty(timers)">
      <v-snackbar v-model="snackbar" right :timeout="0" :multi-line="true">
        <strong class="timer-name">{{ timers[0].name }}</strong>

        <span
          class="ml-2 mr-4"
          :style="{
                        backgroundColor: timers[0].category_color
                    }"
          style="display: inline-block; height:10px; width:10px; border-radius:50%"
        ></span>
        {{ activeTimerString }}
        <v-btn color="red lighten-1" text @click="stopTimer()">
          <v-icon x-large>mdi-stop</v-icon>
        </v-btn>
      </v-snackbar>
    </div>

    <!-- タイマー一覧 -->
    <div v-if="!isEmpty(timers)">
      <v-data-table :headers="headers" :items="timers" :items-per-page="15" class="elevation-1">
        <template v-slot:item.name="{ item }">
          <v-btn
            text
            :disabled="isEmpty(item.stopped_at)"
            @click="openEditTimer(item)"
          >{{ item.name }}</v-btn>
        </template>

        <template v-slot:item.memo="{ item }">
          <small class="text-muted">{{ item.memo }}</small>
        </template>
        <template v-slot:item.category="{ item }">
          <span
            :style="{ backgroundColor: item.category_color }"
            style="display: inline-block; height:10px; width:10px; border-radius:50%"
          ></span>
          <span :style="{ color: item.category_color }">
            {{
            item.category_name
            }}
          </span>
        </template>
        <template v-slot:item.date="{ item }">
          {{
          formatTimer(item)
          }}
        </template>
        <template v-slot:item.time="{ item }">
          <span v-if="showTimer(item)">{{ activeTimerString }}</span>
          <span v-else>{{ calculateTimeSpent(item) }}</span>
        </template>
      </v-data-table>
    </div>
    <!-- 計測データ無し時 -->
    <div v-else>データがありません。タイマーをセットして計測を始めましょう。</div>

    <!-- ダイアログ -->
    <!-- タイマー作成 -->
    <div class="text-center">
      <v-dialog v-model="newTimerDialog" width="500">
        <v-card>
          <v-card-title>
            <span class="headline">新規作成</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <!-- 記録内容入力 -->
                <v-col cols="12">
                  <v-text-field
                    v-model="newTimerName"
                    label="記録する内容*"
                    required
                    prepend-icon="mdi-border-color"
                    :rules="newTimerNameRules"
                    :counter="30"
                  ></v-text-field>
                </v-col>

                <!-- カテゴリー選択 -->
                <v-col cols="12">
                  <v-select
                    v-if="isEmpty(categories)"
                    v-model="newTimerCategory"
                    :items="categories"
                    item-text="name"
                    item-value="value"
                    label="カテゴリー*を追加してください"
                    return-object
                    required
                    prepend-icon="mdi-folder"
                    disabled
                  ></v-select>
                  <v-select
                    v-else
                    v-model="newTimerCategory"
                    :items="categories"
                    item-text="name"
                    item-value="value"
                    label="カテゴリーを選択する*"
                    return-object
                    required
                    prepend-icon="mdi-folder"
                    :rules="newTimerCategoryRules"
                  ></v-select>
                </v-col>
                <!-- カテゴリ追加ボタン -->
                <div class="text-center">
                  <v-menu
                    v-model="menu"
                    :close-on-content-click="false"
                    :nudge-width="200"
                    offset-x
                  >
                    <template v-slot:activator="{ on }">
                      <v-btn text color="grey lighten-1" v-on="on">
                        <v-icon color="light-green accent-3">mdi-plus</v-icon>カテゴリーを追加する
                      </v-btn>
                    </template>

                    <!-- カテゴリー追加メニュー -->
                    <v-card>
                      <v-card-title>
                        <span class="headline">カテゴリーを追加する</span>
                      </v-card-title>
                      <v-card-text>
                        <v-container>
                          <v-row>
                            <v-col cols="12">
                              <!-- カテゴリー名入力 -->
                              <v-text-field
                                v-model="
                                                                    newCategoryName
                                                                "
                                label="カテゴリー名を入力"
                                prepend-icon="mdi-folder-plus"
                              ></v-text-field>

                              <!-- カラーコード選択 -->
                              <v-text-field
                                prepend-icon="mdi-format-color-fill"
                                v-mask="mask"
                                hide-details
                                class="ma-0 pa-0"
                                label="色を選択"
                                v-model="
                                                                    newCategoryColor
                                                                "
                              >
                                <template v-slot:append>
                                  <v-menu
                                    v-model="
                                                                            colorMenu
                                                                        "
                                    top
                                    nudge-bottom="248"
                                    nudge-left="16"
                                    :close-on-content-click="
                                                                            false
                                                                        "
                                  >
                                    <template
                                      v-slot:activator="{
                                                                                on
                                                                            }"
                                    >
                                      <div
                                        :style="
                                                                                    swatchStyle
                                                                                "
                                        v-on="
                                                                                    on
                                                                                "
                                      />
                                    </template>
                                    <v-card>
                                      <v-card-text class="pa-0">
                                        <v-color-picker
                                          v-model="
                                                                                        newCategoryColor
                                                                                    "
                                          flat
                                          show-swatches
                                        />
                                      </v-card-text>
                                    </v-card>
                                  </v-menu>
                                </template>
                              </v-text-field>
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-card-text>

                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn text @click="menu = false">CANCEL</v-btn>
                        <v-btn
                          color="primary"
                          text
                          :disabled="
                                                        newCategoryName === ''
                                                    "
                          @click="createCategory()"
                        >SAVE</v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-menu>
                </div>

                <!-- メモ入力 -->
                <v-col cols="12">
                  <v-textarea
                    v-model="newTimerMemo"
                    label="メモ"
                    type="text"
                    prepend-icon="mdi-text-box"
                    :rules="newTimerMemoRules"
                    :counter="140"
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-container>
            <small>*は必須項目です。</small>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="newTimerDialog = false">CANCEL</v-btn>
            <v-btn
              color="blue darken-1"
              text
              @click="createTimer()"
              :disabled="
                                newTimerName.length > 30 ||
                                    newTimerName === '' ||
                                    newTimerCategory === '' ||
                                    newTimerMemo.length > 140
                            "
            >START</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
    <!-- タイマー編集 -->
    <div class="text-center">
      <v-dialog v-model="editTimerDialog" width="500">
        <v-card>
          <v-card-title>
            <span class="headline">記録を編集する</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <!-- 記録内容入力 -->
                <v-col cols="12">
                  <v-text-field
                    v-model="editTimer.name"
                    label="記録する内容*"
                    required
                    prepend-icon="mdi-border-color"
                    :rules="newTimerNameRules"
                    :counter="30"
                  ></v-text-field>
                </v-col>

                <!-- カテゴリー選択 -->
                <v-col cols="12">
                  <v-select
                    v-model="editTimer.category_name"
                    :items="categories"
                    item-text="name"
                    item-value="value"
                    label="カテゴリーを選択する*"
                    return-object
                    required
                    prepend-icon="mdi-folder"
                    :rules="newTimerCategoryRules"
                  ></v-select>
                </v-col>

                <!-- メモ入力 -->
                <v-col cols="12">
                  <v-textarea
                    v-model="editTimer.memo"
                    label="メモ"
                    type="text"
                    prepend-icon="mdi-text-box"
                    :rules="newTimerMemoRules"
                    :counter="140"
                  ></v-textarea>
                </v-col>

                <!-- 開始時刻入力 -->
                <v-col cols="12">
                  <v-datetime-picker
                    v-model="editTimer.datetime"
                    :text-field-props="textFieldProps"
                    label="開始日時 / 計測期間*"
                  >
                    <template slot="dateIcon">
                      <v-icon>mdi-calendar</v-icon>
                    </template>
                    <template slot="timeIcon">
                      <v-icon>mdi-clock-outline</v-icon>
                    </template>
                    <template slot="actions" slot-scope="{ parent }">
                      <v-btn text color="primary" @click="parent.okHandler">SAVE</v-btn>
                    </template>
                  </v-datetime-picker>
                </v-col>

                <!-- 計測期間入力 -->
                <v-col cols="4">
                  <v-select
                    v-model="editTimer.time.hours"
                    label="時間(hours)"
                    required
                    prepend-icon="mdi-timer-sand-full"
                    :items="time.hours"
                  ></v-select>
                </v-col>
                <v-col cols="4">
                  <v-select
                    v-model="editTimer.time.minutes"
                    label="分(minutes)"
                    required
                    :items="time.minutes"
                  ></v-select>
                </v-col>
                <v-col cols="4">
                  <v-select
                    v-model="editTimer.time.seconds"
                    label="秒(seconds)"
                    required
                    :items="time.seconds"
                  ></v-select>
                </v-col>
              </v-row>
            </v-container>
            <small>*は必須項目です。</small>
          </v-card-text>

          <v-card-actions>
            <!-- 削除ボタン -->
            <v-menu
              v-model="deleteMenu"
              :close-on-content-click="false"
              :nudge-width="200"
              offset-x
            >
              <template v-slot:activator="{ on }">
                <v-btn text color="red lighten-1" v-on="on">DELETE</v-btn>
              </template>
              <!-- 削除確認メニュー -->
              <v-card>
                <v-card-title>
                  <span class="headline">記録を削除します</span>
                </v-card-title>
                <v-card-text>本当によろしいですか？</v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn text @click="deleteMenu = false">CANCEL</v-btn>
                  <v-btn color="red lighten-1" text @click="deleteTimer()">DELETE</v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>

            <v-spacer></v-spacer>
            <v-btn text @click="editTimerDialog = false">CANCEL</v-btn>
            <v-btn
              color="blue darken-1"
              text
              @click="updateTimer()"
              :disabled="
                                editTimer.name.length > 30 ||
                                    editTimer.name === '' ||
                                    editTimer.category === '' ||
                                    (editTimer.memo &&
                                        editTimer.memo.length > 140) ||
                                    editTimer.datetime === ''
                            "
            >SAVE</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import { TheMask } from "vue-the-mask";

export default {
  data() {
    return {
      time: {
        hours: [],
        minutes: [],
        seconds: []
      },
      textFieldProps: {
        prependIcon: "mdi-clock",
        rules: this.requireRule
      },
      newTimerDialog: false,
      editTimerDialog: false,
      snackbar: false,
      doneTimerSnackbar: false,
      updateTimerSnackbar: false,
      deleteTimerSnackbar: false,
      menu: false,
      colorMenu: false,
      deleteMenu: false,
      mask: "!#XXXXXXXX",
      timers: [],
      categories: [],
      newTimerName: "",
      newTimerMemo: "",
      newTimerCategory: "",
      editTimer: {
        id: "",
        name: "",
        category_name: "",
        memo: "",
        datetime: new Date(),
        time: { hours: "", minutes: "", seconds: "" }
      },
      newCategoryColor: "#1CA085",
      newCategoryName: "",
      activeTimerString: "Calculating...",
      counter: { seconds: 0, timer: null },
      requireRule: [value => !!value || "入力してください。"],
      newTimerNameRules: [
        value => !!value || "入力してください。",
        value => (value || "").length <= 30 || "最大30文字です。"
      ],
      newTimerCategoryRules: [value => !!value || "選択してください。"],
      newTimerMemoRules: [
        value => (value || "").length <= 140 || "最大140文字です。"
      ],
      headers: [
        {
          text: "記録の内容",
          align: "start",
          sortable: false,
          value: "name"
        },
        { text: "メモ", value: "memo", sortable: false },
        { text: "カテゴリー", value: "category", sortable: false },
        { text: "時刻", value: "date", sortable: false },
        { text: "計測期間", value: "time", sortable: false }
      ]
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
     * ゼロパディング
     */
    _padNumber: number => (number > 9 ? number : "0" + number),

    /**
     * タイマーの表示を調整
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
     * timerオブジェクトを用いて経過時間の計測
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
     * アクティブなタイマーの有無をチェック
     */
    showTimer: function(timer) {
      return this.counter.timer && this.counter.timer.id === timer.id;
    },

    /**
     * タイマーを開始
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

      this.snackbar = true;
    },

    /**
     * タイマーをストップ
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

        this.snackbar = false;
        this.doneTimerSnackbar = true;
      });
    },

    /**
     * 新規のタイマーを作成
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
          this.timers.unshift(response.data);
          this.startTimer(response.data);
        });

      this.newTimerName = "";
      this.newTimerMemo = "";
      this.newTimerCategory = "";
      this.newTimerDialog = false;
    },

    /**
     * 新規のカテゴリーを作成
     */
    createCategory: function() {
      window.axios
        .post("/categories", {
          name: this.newCategoryName,
          color: this.newCategoryColor
        })
        .then(response => this.categories.push(response.data));

      this.newCategoryName = "";
      this.menu = false;
    },

    /**
     * カテゴリーを編集
     */
    openEditTimer(item) {
      //以下仕様変更の必要あり
      this._arrayHours();
      this._arrayMinutes();

      this.editTimer.id = item.id;
      this.editTimer.name = item.name;
      this.editTimer.category_name = item.category_name;
      this.editTimer.memo = item.memo;

      this.editTimer.datetime = new Date(item.started_at);

      // 計測期間
      const started = moment(item.started_at);
      const stopped = moment(item.stopped_at);
      const time = this._readableTimeFromSeconds(
        parseInt(moment.duration(stopped.diff(started)).asSeconds())
      );
      this.editTimer.time.hours = parseInt(time.hours);
      this.editTimer.time.minutes = parseInt(time.minutes);
      this.editTimer.time.seconds = parseInt(time.seconds);

      this.editTimerDialog = true;
    },

    /**
     * タイマーを更新
     */
    updateTimer() {
      this.editTimerDialog = false;
      this.updateTimerSnackbar = true;
    },

    /**
     * タイマーを削除
     */
    deleteTimer() {
      console.log(
        `削除対象はTimerテーブルのid:${this.editTimer.id}の${this.editTimer.name}です。`
      );
      // idを受け取って論理削除を実装する
      // window.axios
      //     .delete(`/timers/${this.editTimer.id}`)
      //     .then(response => this.categories = response.data);
      this.editTimerDialog = false;
      this.deleteTimerSnackbar = true;
    },

    /**
     * 時刻をフォーマット
     */
    _formatTime: function(date) {
      if (date) {
        const d = new Date(date);
        const h = d.getHours();
        const m = d.getMinutes();
        return `${this._padNumber(h)}:${this._padNumber(m)}`;
      }
      return "";
    },
    formatTimer: function(timer) {
      const started = timer.started_at;
      const stopped = timer.stopped_at;
      return `${this._formatTime(started)} - ${this._formatTime(stopped)}`;
    },

    /**
     * 値が空( null or undefined or ''(空文字) or [](空の配列) or {}(空のオブジェクト) )を判定
     * 返り値 true: 空
     */
    isEmpty: function(val) {
      if (!val) {
        //null or undefined or ''(空文字) or 0 or false
        if (val !== 0 && val !== false) {
          return true;
        }
      } else if (typeof val == "object") {
        //array or object
        return Object.keys(val).length === 0;
      }
      return false; //値は空ではない
    },
    /**
     * 時分秒の配列を生成
     */
    _arrayHours() {
      let i = 0;
      for (i; i <= 24; i++) {
        this.time.hours.push(i);
      }
    },
    _arrayMinutes() {
      let i = 0;
      for (i; i <= 59; i++) {
        this.time.minutes.push(i);
        this.time.seconds.push(i);
      }
    }
  },
  computed: {
    /**
     * カテゴリーカラー選択用のスタイル
     */
    swatchStyle() {
      const { newCategoryColor, menu } = this;
      return {
        backgroundColor: newCategoryColor,
        cursor: "pointer",
        height: "30px",
        width: "30px",
        borderRadius: menu ? "50%" : "4px",
        transition: "border-radius 200ms ease-in-out"
      };
    }
  }
};
</script>
<style scoped>
.v-btn {
  text-transform: none !important;
}
.modal-header .close {
  padding: 0px;
  margin: 0px;
}
.modal-title {
  margin: 0 auto;
}
.v-btn {
  outline: none !important;
}
.v-snack__content .v-btn {
  margin-left: 0 !important;
}
</style>
