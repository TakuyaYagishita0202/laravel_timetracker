<template>
  <div class="body">
    <!-- 上部Snackbar -->
    <!-- 計算中タイマー -->
    <div v-if="!isEmpty(timers)">
      <v-snackbar v-model="snackbar.activeTimer" top :timeout="0" :color="counter.timer.category_color">
        <strong class="timer-name pr-4">{{ counter.timer.name }}</strong>
        {{ activeTimerString }}
        <v-btn text @click="stopTimer()">
          <v-icon x-large>mdi-stop</v-icon>
        </v-btn>
      </v-snackbar>
    </div>
    <!-- 記録完了時 twitterシェア -->
    <v-snackbar top v-model="snackbar.done" color="#00acee">
      お疲れ様でした。この記録を友達にシェアしましょう！
      <v-btn
        text
        @click="snackbar.done = false"
        href="https://twitter.com/home"
        　target="_blank"
      >
        <v-icon>mdi-twitter</v-icon>
      </v-btn>
    </v-snackbar>
    <!-- 更新完了時 -->
    <v-snackbar top v-model="snackbar.updated" color="success">
      更新しました。
      <v-btn text @click="snackbar.updated = false">閉じる</v-btn>
    </v-snackbar>
    <!-- 削除完了時 -->
    <v-snackbar top v-model="snackbar.deleted" color="error">
      記録を削除しました。
      <v-btn text @click="snackbar.deleted = false">閉じる</v-btn>
    </v-snackbar>
    <!-- サーバーエラー時 -->
    <v-snackbar top v-model="snackbar.error" color="error">
      エラーが発生しました。メッセージ：{{ errorMessage }}
      <v-btn text @click="snackbar.error = false">閉じる</v-btn>
    </v-snackbar>

    <!-- 右下部Snackbar -->
    <!-- タイマー追加ボタン -->
    <v-speed-dial
      v-model="fab"
      fab
      bottom
      right
      fixed
      direction="top"
      transition="slide-y-reverse-transition"
    >
      <template v-slot:activator>
        <v-hover v-slot:default="{ hover }">
          <v-btn
            v-model="fab"
            color="light-green accent-4"
            dark
            fab
            x-large
            :elevation="hover ? 12 : 6"
          >
            <v-icon v-if="fab">mdi-close</v-icon>
            <v-icon v-else>mdi-plus</v-icon>
          </v-btn>
        </v-hover>
      </template>
      <v-btn
        v-if="isEmpty(counter.timer.name)"
        fab
        dark
        small
        color="pink lighten-3"
        @click.stop="dialog.newTimer = true"
      >
        <v-icon>mdi-timer-outline</v-icon>
      </v-btn>
      <v-btn fab dark small color="blue lighten-2" @click.stop="dialog.saveTimer = true">
        <v-icon>mdi-playlist-plus</v-icon>
      </v-btn>
    </v-speed-dial>

    <!-- ローディングアニメーション -->
    <template v-if="loading">
      <v-row :style="`height:${height}px`" align="center" justify="center">
        <v-col>
          <vue-loading type="bars" color="#B0BEC5" :size="{ width: '50px', height: '50px' }"></vue-loading>
        </v-col>
      </v-row>
    </template>
    <!-- タイマーを一覧で表示 -->
    <template v-else>
      <!-- タイマーを日付ごとに表示 -->
      <template v-if="!isEmpty(timersArray[0][0])">
        <template v-for="timers in timersArray">
          <span class="title">{{ formatDate(timers[0].started_at) }}
            <!-- twitterボタン -->
            <!-- <v-btn
              icon
              color="#00acee"
              href="https://twitter.com/home"
            　target="_blank"
              style="text-decoration:none"
            >
              <v-icon>mdi-twitter</v-icon>
            </v-btn> -->
          </span>
          <v-data-table
            :headers="headers"
            :items="timers"
            :items-per-page="100"
            class="elevation-1 mb-4"
            hide-default-footer
            @click:row="openEditTimer"
          >
            <template v-slot:item.name="{ item }">
              <span>{{ item.name }}</span>
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
            <template v-slot:item.started_at="{ item }">
              {{
              formatTimer(item)
              }}
            </template>
            <template v-slot:item.time="{ item }">
              <span v-if="showTimer(item)">{{ activeTimerString }}</span>
              <span v-else>{{ calculateTimeSpent(item) }}</span>
            </template>
          </v-data-table>
        </template>
          <v-row v-if="!lastPage" justify="center">
            <v-btn :loading="infiniteLoading" @click="loadMore">
              <v-icon left>mdi-chevron-down</v-icon>
              もっと見る</v-btn>
          </v-row>
      </template>
      <!-- 計測データ無し時 -->
      <v-row v-else align="center" justify="center">
        <v-col cols="5">
          <v-img :src="'./svg/no_data.svg'"></v-img>
          <p class="mt-2 title text-center">データがありません。</p>
          <p class="text-center">早速タイマーをセットして、記録を始めましょう。</p>
        </v-col>
      </v-row>
    </template>

    <!-- ダイアログ -->
    <!-- 新規作成：タイマー -->
    <div class="text-center">
      <v-dialog v-model="dialog.newTimer" width="500">
        <v-card>
          <v-card-title class="pink--text text--lighten-3 headline">
            <v-icon class="mr-2" color="pink lighten-3">mdi-timer-outline</v-icon>タイマーモード
          </v-card-title>
          <v-card-text>
            <v-form v-model="newTimerValid" ref="newTimerForm">
              <v-container class="pt-0">
                <v-row>
                  <!-- 記録内容入力 -->
                  <v-col cols="12">
                    <v-text-field
                      v-model="newTimer.name"
                      label="記録する内容*"
                      required
                      prepend-icon="mdi-border-color"
                      :rules="rules.name"
                      :counter="30"
                    ></v-text-field>
                  </v-col>

                  <!-- カテゴリー選択 -->
                  <v-col cols="12">
                    <v-select
                      v-if="isEmpty(categories)"
                      v-model="newTimer.category"
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
                      v-model="newTimer.category"
                      :items="categories"
                      item-text="name"
                      item-value="value"
                      label="カテゴリーを選択する*"
                      return-object
                      required
                      prepend-icon="mdi-folder"
                      :rules="rules.category"
                    ></v-select>
                  </v-col>
                  <!-- カテゴリ追加ボタン -->
                  <div class="text-center">
                    <v-menu
                      v-model="menu.newTimerCategory"
                      :close-on-content-click="false"
                      :nudge-width="200"
                      offset-x
                    >
                      <template v-slot:activator="{ on }">
                        <v-btn text color="grey lighten-1" v-on="on">
                          <v-icon color="light-green accent-4">mdi-plus</v-icon>カテゴリーを追加する
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
                                  v-model="newCategory.name"
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
                                  v-model="newCategory.color"
                                >
                                  <template v-slot:append>
                                    <v-menu
                                      v-model="menu.newTimerColor"
                                      top
                                      nudge-bottom="248"
                                      nudge-left="16"
                                      :close-on-content-click="false"
                                    >
                                      <template v-slot:activator="{on}">
                                        <div :style="swatchStyle" v-on="on" />
                                      </template>
                                      <v-card>
                                        <v-card-text class="pa-0">
                                          <v-color-picker
                                            v-model="newCategory.color"
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
                          <v-btn text @click="menu.newTimerCategory = false">CANCEL</v-btn>
                          <v-btn
                            color="primary"
                            text
                            :disabled="newCategory.name === ''"
                            @click="createCategory()"
                          >SAVE</v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-menu>
                  </div>

                  <!-- メモ入力 -->
                  <v-col cols="12">
                    <v-textarea
                      v-model="newTimer.memo"
                      label="メモ"
                      type="text"
                      prepend-icon="mdi-text-box"
                      :rules="rules.memo"
                      :counter="140"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
            <small>*は必須項目です。</small>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="dialog.newTimer = false">CANCEL</v-btn>
            <v-btn
              color="blue darken-1"
              text
              @click="createTimer()"
              :disabled="!newTimerValid"
            >START</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>

    <!-- 新規作成：手動 -->
    <div class="text-center">
      <v-dialog v-model="dialog.saveTimer" width="500">
        <v-card>
          <v-card-title class="blue--text text--lighten-2 headline">
            <v-icon class="mr-2" color="blue lighten-2">mdi-playlist-plus</v-icon>マニュアルモード
          </v-card-title>
          <v-card-text>
            <v-form ref="saveTimerForm">
              <v-container class="pt-0">
                <v-row>
                  <!-- 記録内容入力 -->
                  <v-col cols="12">
                    <v-text-field
                      v-model="saveTimer.name"
                      label="記録する内容*"
                      required
                      prepend-icon="mdi-border-color"
                      :rules="rules.name"
                      :counter="30"
                    ></v-text-field>
                  </v-col>

                  <!-- カテゴリー選択 -->
                  <v-col cols="12">
                    <v-select
                      v-if="isEmpty(categories)"
                      v-model="saveTimer.category"
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
                      v-model="saveTimer.category"
                      :items="categories"
                      item-text="name"
                      item-value="value"
                      label="カテゴリーを選択する*"
                      return-object
                      required
                      prepend-icon="mdi-folder"
                      :rules="rules.category"
                    ></v-select>
                  </v-col>

                  <!-- カテゴリ追加ボタン -->
                  <div class="text-center">
                    <v-menu
                      v-model="menu.saveTimerCategory"
                      :close-on-content-click="false"
                      :nudge-width="200"
                      offset-x
                    >
                      <template v-slot:activator="{ on }">
                        <v-btn text color="grey lighten-1" v-on="on">
                          <v-icon color="light-green accent-4">mdi-plus</v-icon>カテゴリーを追加する
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
                                  v-model="newCategory.name"
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
                                  v-model="newCategory.color"
                                >
                                  <template v-slot:append>
                                    <v-menu
                                      v-model="menu.saveTimerColor"
                                      top
                                      nudge-bottom="248"
                                      nudge-left="16"
                                      :close-on-content-click="false"
                                    >
                                      <template v-slot:activator="{ on }">
                                        <div :style="swatchStyle" v-on="on" />
                                      </template>
                                      <v-card>
                                        <v-card-text class="pa-0">
                                          <v-color-picker
                                            v-model="newCategory.color"
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
                          <v-btn text @click="menu.saveTimerCategory = false">CANCEL</v-btn>
                          <v-btn
                            color="primary"
                            text
                            :disabled="newCategory.name === ''"
                            @click="createCategory()"
                          >SAVE</v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-menu>
                  </div>

                  <!-- メモ入力 -->
                  <v-col cols="12">
                    <v-textarea
                      v-model="saveTimer.memo"
                      label="メモ"
                      type="text"
                      prepend-icon="mdi-text-box"
                      :rules="rules.memo"
                      :counter="140"
                    ></v-textarea>
                  </v-col>

                  <!-- 開始時刻入力 -->
                  <v-col cols="12">
                    <v-datetime-picker
                      v-model="saveTimer.started_at"
                      :text-field-props="textFieldProps"
                      label="開始日時* / 計測期間*"
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
                      v-model="saveTimer.time.hours"
                      label="時間(hours)"
                      required
                      prepend-icon="mdi-timer-sand-full"
                      :items="time.hours"
                    ></v-select>
                  </v-col>
                  <v-col cols="4">
                    <v-select
                      v-model="saveTimer.time.minutes"
                      label="分(minutes)"
                      required
                      :items="time.minutes"
                    ></v-select>
                  </v-col>
                  <v-col cols="4">
                    <v-select
                      v-model="saveTimer.time.seconds"
                      label="秒(seconds)"
                      required
                      :items="time.seconds"
                    ></v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
            <small>*は必須項目です。</small>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="dialog.saveTimer = false">CANCEL</v-btn>
            <v-btn
              color="blue darken-1"
              text
              @click="addTimer()"
              :disabled="
                                saveTimer.name.length > 30 ||
                                    saveTimer.name === '' ||
                                    saveTimer.category === '' ||
                                    (saveTimer.memo &&
                                        saveTimer.memo.length > 140) ||
                                    saveTimer.started_at === '' || saveTimer.stopped_at === '' || (!saveTimer.time.hours && !saveTimer.time.minutes && !saveTimer.time.seconds)
                            "
            >SAVE</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>

    <!-- タイマー編集 -->
    <div class="text-center">
      <v-dialog v-model="dialog.editTimer" width="500">
        <v-card>
          <v-card-title class="grey--text text--darken-2 headline">
            <v-icon class="mr-2">mdi-update</v-icon>レコードを編集する
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="#00acee"
              href="https://twitter.com/home"
              　target="_blank"
              style="text-decoration:none"
            >
              <v-icon>mdi-twitter</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container class="pt-0">
              <v-row>
                <!-- 記録内容入力 -->
                <v-col cols="12">
                  <v-text-field
                    v-model="editTimer.name"
                    label="記録する内容*"
                    required
                    prepend-icon="mdi-border-color"
                    :rules="rules.name"
                    :counter="30"
                  ></v-text-field>
                </v-col>

                <!-- カテゴリー選択 -->
                <v-col cols="12">
                  <v-select
                    v-model="editTimer.category"
                    :items="categories"
                    item-text="name"
                    item-value="value"
                    label="カテゴリーを選択する*"
                    return-object
                    required
                    prepend-icon="mdi-folder"
                    :rules="rules.category"
                  ></v-select>
                </v-col>

                <!-- メモ入力 -->
                <v-col cols="12">
                  <v-textarea
                    v-model="editTimer.memo"
                    label="メモ"
                    type="text"
                    prepend-icon="mdi-text-box"
                    :rules="rules.memo"
                    :counter="140"
                  ></v-textarea>
                </v-col>

                <!-- 開始時刻入力 -->
                <v-col cols="12">
                  <v-datetime-picker
                    v-model="editTimer.started_at"
                    :text-field-props="textFieldProps"
                    label="開始日時* / 計測期間*"
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
              v-model="menu.delete"
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
                  <v-btn text @click="menu.delete = false">CANCEL</v-btn>
                  <v-btn color="red lighten-1" text @click="deleteTimer()">DELETE</v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>

            <v-spacer></v-spacer>
            <v-btn text @click="dialog.editTimer = false">CANCEL</v-btn>
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
                                    editTimer.started_at === '' || editTimer.stopped_at === ''
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
      newTimer:{
        name:"",
        memo:"",
        category:""
      },
      newCategory:{
        name: "",
        color: "#1CA085",
      },
      saveTimer: {
        name: "",
        category: "",
        memo: "",
        started_at: new Date(),
        stopped_at: "",
        time: { hours: "", minutes: "", seconds: "" }
      },
      editTimer: {
        id: "",
        name: "",
        category: "",
        memo: "",
        started_at: new Date(),
        stopped_at: "",
        time: { hours: "", minutes: "", seconds: "" }
      },
      dialog:{
        newTimer:false,
        saveTimer:false,
        editTimer:false
      },
      snackbar:{
        activeTimer:false,
        done:false,
        updated:false,
        deleted:false,
        error:false
      },
      menu:{
        newTimerCategory:false,
        saveTimerCategory:false,
        newTimerColor:false,
        saveTimerColor:false,
        delete:false
      },
      rules:{
        category:[value => !!value || "選択してください。"],
        memo:[
          value => (value || "").length <= 140 || "最大140文字です。"
        ],
        require:[value => !!value || "入力してください。"],
        name:[
          value => !!value || "入力してください。",
          value => (value || "").length <= 30 || "最大30文字です。"
        ],
      },
      timers: [],
      timersArray: [[]],
      categories: [],
      headers: [
        {
          text: "記録の内容",
          align: "start",
          sortable: false,
          value: "name",
          width: "35%"
        },
        { text: "メモ", value: "memo", sortable: false, width: "  25%" },
        {
          text: "カテゴリー",
          value: "category",
          sortable: false,
          width: "15%"
        },
        { text: "時刻", value: "started_at", sortable: false, width: "15%" },
        { text: "計測期間", value: "time", sortable: false, width: "10%" }
      ],
      time: {
        hours: [],
        minutes: [],
        seconds: []
      },
      counter: { seconds: 0, timer: { name: "", category: "" } },
      activeTimerString: "Calculating...",
      newTimerValid: false,
      height: window.innerHeight,
      loading: true,
      infiniteLoading:false,
      lastPage:false,
      textFieldProps: {
        prependIcon: "mdi-clock",
      },
      fab: false,
      errorMessage: "",
      page:1,
      //カラーコード入力制御
      mask: "!#XXXXXXXX"
    };
  },
  //timersをstateで管理する場合
  beforeCreate() {
    // this.$store.dispatch("timer/fetchTimers");
  },
  created() {
    window.axios.get("/api/timers").then(response => {
      this.timers = response.data.data;
      if(this.page === response.data.last_page){
        this.lastPage = true;
      }
      window.axios.get("/api/timers/active").then(response => {
        if (response.data.id !== undefined) {
          this.startTimer(response.data);
          this.$store.dispatch("timer/fetchActive", true);
        }
      });
    });
    window.axios.get("/api/categories").then(response => {
      this.categories = response.data;
    });

    //time用
    this._arrayHours();
    this._arrayMinutes();
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
     * もっと見る
     */
    loadMore(){
      this.infiniteLoading = true;
      window.axios.get("/api/timers",{
        params: {
            page: this.page + 1,
        },
      }).then(response => {
        this.page += 1
        this.timers.push(...response.data.data)
        this.infiniteLoading = false;
        if(this.page === response.data.last_page){
          this.lastPage = true;
        }
      });
    },

    /**
     * タイマーを開始
     */
    startTimer: function(timer) {
      this.$store.dispatch("timer/fetchActive", true);
      const started = moment(timer.started_at);

      this.counter.timer = timer;
      this.counter.seconds = parseInt(
        moment.duration(moment().diff(started)).asSeconds()
      );
      this.counter.ticker = setInterval(() => {
        const time = this._readableTimeFromSeconds(++this.counter.seconds);

        this.activeTimerString = `${time.hours}:${time.minutes}:${time.seconds}`;
      }, 1000);

      this.snackbar.activeTimer = true;
    },

    /**
     * タイマーをストップ
     */
    stopTimer: function() {
      window.axios
        .post(`/api/timers/stop`)
        .then(response => {
          // activeTimerをストップ
          const activeTimer = this.timers.find(
            timer => timer.id === this.counter.timer.id
          );
          activeTimer.stopped_at = response.data.stopped_at;

          // tickerをストップ
          clearInterval(this.counter.ticker);

          // counterとactiveTimerStringをリセット
          this.counter = { seconds: 0, timer: { name: "", category: "" } };
          this.activeTimerString = "Calculating...";

          this.snackbar.activeTimer = false;
          this.snackbar.done = true;
          confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
          });
          this.$store.dispatch("timer/fetchActive", false);
        })
        .catch(err => {
          this.errorMessage = err;
          this.snackbar.error = true;
        });
    },

    /**
     * 新規のタイマーを作成
     */
    createTimer: function() {
      window.axios
        .post(`/api/timers`, {
          name: this.newTimer.name,
          memo: this.newTimer.memo,
          category_id: this.newTimer.category["id"],
          category_name: this.newTimer.category["name"]
            ? this.newTimer.category["name"]
            : "カテゴリー未登録",
          category_color: this.newTimer.category["color"]
            ? this.newTimer.category["color"]
            : "#E1E2EB"
        })
        .then(response => {
          this.timers.unshift(response.data);
          this.startTimer(response.data);
          this.newTimer.name = "";
          this.newTimer.memo = "";
          this.newTimer.category = "";
          this.dialog.newTimer = false;
        })
        .catch(err => {
          this.errorMessage = err;
          this.snackbar.error = true;
        });
    },

    /**
     * 新規のタイマーを手入力保存
     */
    addTimer() {
      window.axios
        .post(`/api/timers/save`, {
          name: this.saveTimer.name,
          memo: this.saveTimer.memo,
          category_id: this.saveTimer.category.id,
          category_name: this.saveTimer.category.name,
          category_color: this.saveTimer.category.color,
          started_at: this.saveTimer.started_at,
          stopped_at: this.saveTimer.stopped_at
        })
        .then(response => {
          const savedTimer = response.data;
          const oldestTimer = this.timers[this.timers.length - 1]

          // 現在のページがlastPageか
          // またはsavedTimerがtimersの最も古いデータ(oldestTimer)よりも新しい場合のみpushする
          if(
            this.lastPage ||
            moment(savedTimer.started_at).isAfter(oldestTimer.started_at)
          ){
            this.timers.push(savedTimer);
            // 日付降順に並び替え
            this.timers.sort(function(a, b) {
              return a.started_at < b.started_at ? 1 : -1;
            });
          }

          this.dialog.saveTimer = false;
          this.snackbar.done = true;
          this.saveTimer = {
            name: "",
            category: "",
            memo: "",
            started_at: new Date(),
            stopped_at: "",
            time: { hours: "", minutes: "", seconds: "" }
          };
        })
        .catch(err => {
          this.errorMessage = err;
          this.snackbar.error = true;
        });
    },

    /**
     * 新規のカテゴリーを作成
     */
    createCategory: function() {
      window.axios
        .post("/api/categories", {
          name: this.newCategory.name,
          color: this.newCategory.color
        })
        .then(response => {
          this.categories.push(response.data);
          this.newCategory.name = "";
          this.menu.newTimerCategory = false;
          this.menu.saveTimerCategory = false;
        })
        .catch(err => {
          this.errorMessage = err;
          this.snackbar.error = true;
        });
    },

    /**
     * タイマー編集用のダイアログを表示
     */
    openEditTimer(event) {
      const item = event;
      // 計測中は編集不可
      if(item.id === this.counter.timer.id){
        return false
      }
      this.editTimer.id = item.id;
      this.editTimer.name = item.name;

      //修正中
      this.editTimer.category = this.categories.find(
        category => category.id === parseInt(item.category_id)
      );

      this.editTimer.memo = item.memo;

      this.editTimer.started_at = new Date(item.started_at);

      // 計測期間
      const started = moment(item.started_at);
      const stopped = moment(item.stopped_at);
      const time = this._readableTimeFromSeconds(
        parseInt(moment.duration(stopped.diff(started)).asSeconds())
      );
      this.editTimer.time.hours = parseInt(time.hours);
      this.editTimer.time.minutes = parseInt(time.minutes);
      this.editTimer.time.seconds = parseInt(time.seconds);

      this.editTimer.stopped_at = moment(this.editTimer.started_at)
        .add({
          h: this.editTimer.time.hours,
          m: this.editTimer.time.minutes,
          s: this.editTimer.time.seconds
        })
        .toDate();

      this.dialog.editTimer = true;
    },

    /**
     * タイマーを更新
     */
    updateTimer() {
      window.axios
        .put(`/api/timers/${this.editTimer.id}`, {
          name: this.editTimer.name,
          memo: this.editTimer.memo,

          category_id: this.editTimer.category.id,
          category_name: this.editTimer.category.name,
          category_color: this.editTimer.category.color,

          started_at: this.editTimer.started_at,
          stopped_at: this.editTimer.stopped_at
        })
        .then(response => {
          const updatedTimer = response.data;
          const timer = this.timers.find(
            timer => timer.id === updatedTimer["id"]
          );

          // 更新前と更新後の日付データを予め取得
          const startedBefore = moment(timer.started_at);
          const startedAfter = moment(new Date(updatedTimer["started_at"]));

          // timerの値を更新
          timer.name = this.editTimer.name;
          timer.memo = this.editTimer.memo;
          timer.category_id = this.editTimer.category.id;
          timer.category_name = this.editTimer.category.name;
          timer.category_color = this.editTimer.category.color;
          timer.started_at = updatedTimer["started_at"];
          timer.stopped_at = updatedTimer["stopped_at"];

          // 現在のページがlastPageでないかつ
          // 編集後の記録がtimersの最後の記録よりも古い場合は見かけ上削除する
          const oldestTimer = this.timers[this.timers.length - 1]
          if(
            !this.lastPage &&
            moment(updatedTimer["started_at"]).isBefore(oldestTimer.started_at)
          ){
            this.timers = this.timers.filter(
              timer => timer.id !== updatedTimer.id
            );
          }

          // 日付が更新されていた場合はtimesを日付降順に並び替え
          if (!startedBefore.isSame(startedAfter)) {
            this.timers.sort(function(a, b) {
              return a.started_at < b.started_at ? 1 : -1;
            });
          }

          this.dialog.editTimer = false;
          this.snackbar.updated = true;
        })
        .catch(err => {
          this.errorMessage = err;
          this.snackbar.error = true;
        });
    },

    /**
     * タイマーを削除
     */
    deleteTimer() {
      // 物理削除
      window.axios
        .delete(`/api/timers/${this.editTimer.id}`)
        .then(response => {
          const deletedTimer = response.data;
          this.timers = this.timers.filter(
            timer => timer.id !== deletedTimer.id
          );
          this.dialog.editTimer = false;
          this.snackbar.deleted = true;
        })
        .catch(err => {
          this.errorMessage = err;
          this.snackbar.error = true;
        });
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
     * 年月日を表示
     */
    formatDate: function(date) {
      moment.updateLocale("ja", {
        weekdaysShort: ["日", "月", "火", "水", "木", "金", "土"]
      });
      const M = moment(date).format("M");
      const D = moment(date).format("D");
      const W = moment(date).format("ddd");
      return `${M}月${D}日(${W})`;
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
      return {
        backgroundColor: this.newCategory.color,
        cursor: "pointer",
        height: "30px",
        width: "30px",
        borderRadius: this.menu.newTimerColor ? "50%" : "4px",
        transition: "border-radius 200ms ease-in-out"
      };
    }
  },
  watch: {
    /**
     * editTimer.stopped_at計算用
     */
    "editTimer.time": {
      handler: function(val, oldVal) {
        this.editTimer.stopped_at = moment(this.editTimer.started_at)
          .add({
            h: this.editTimer.time.hours,
            m: this.editTimer.time.minutes,
            s: this.editTimer.time.seconds
          })
          .toDate();
      },
      deep: true
    },
    "editTimer.started_at": {
      handler: function(val, oldVal) {
        this.editTimer.stopped_at = moment(this.editTimer.started_at)
          .add({
            h: this.editTimer.time.hours,
            m: this.editTimer.time.minutes,
            s: this.editTimer.time.seconds
          })
          .toDate();
      }
    },

    /**
     * saveTimer.stopped_at計算用
     */
    "saveTimer.time": {
      handler: function(val, oldVal) {
        this.saveTimer.stopped_at = moment(this.saveTimer.started_at)
          .add({
            h: this.saveTimer.time.hours,
            m: this.saveTimer.time.minutes,
            s: this.saveTimer.time.seconds
          })
          .toDate();
      },
      deep: true
    },
    "saveTimer.started_at": {
      handler: function(val, oldVal) {
        this.saveTimer.stopped_at = moment(this.saveTimer.started_at)
          .add({
            h: this.saveTimer.time.hours,
            m: this.saveTimer.time.minutes,
            s: this.saveTimer.time.seconds
          })
          .toDate();
      }
    },

    /**
     * timersを監視しつつ、日付ごとに分類してtimersArrayを再生成
     */
    timers: {
      handler: function() {
        this.timersArray = [[]];
        if (this.timers[0] !== null) {
          // timers[0]をtimesArray[0]に格納する
          this.timersArray[0].push(this.timers[0]);

          //timersでループを回す
          for (let i = 0; i < this.timers.length - 1; i++) {
            // timers[1]のstarted_atの年月日がtimers[0]のstarted_atの年月日と同じ場合は、timers[1]を配列に格納
            if (
              moment(this.timers[i].started_at).isSame(
                moment(this.timers[i + 1].started_at),
                "day"
              )
            ) {
              this.timersArray[this.timersArray.length - 1].push(
                this.timers[i + 1]
              );
            } else {
              // 異なる場合は新たに配列を作成して格納
              this.timersArray.push([]);
              this.timersArray[this.timersArray.length - 1].push(
                this.timers[i + 1]
              );
            }
          }
        }
      },
    },

    /**
     * 新規作成ダイアログがfalseの場合はバリデーションをリセットする
     */
    "dialog.newTimer": {
      handler: function(val) {
        if(!this.dialog.newTimer){
          this.$refs.newTimerForm.resetValidation()
        }
      },
      deep: true
    },
    "dialog.saveTimer": {
      handler: function(val) {
        if(!this.dialog.saveTimer){
          this.$refs.saveTimerForm.resetValidation()
        }
      },
      deep: true
    },

    timersArray: {
      handler: function() {
        this.loading = false;
      }
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
