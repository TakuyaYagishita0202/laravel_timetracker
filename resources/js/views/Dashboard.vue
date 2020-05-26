<template>
  <div>
    <!-- snackbar -->
    <!-- サーバーエラー時 -->
    <v-snackbar top v-model="snackbar.error" color="error">
      エラーが発生しました。メッセージ：{{ errorMessage }}
      <v-btn text @click="snackbar.error = false">閉じる</v-btn>
    </v-snackbar>

    <!-- レコード -->
    <span class="title">
      <v-icon class="mr-2 mb-1">mdi-file</v-icon>レコード
    </span>
    <v-row>
      <v-col cols="12" sm="4" md="3">
        <v-card>
          <v-card-title class="pb-2">今日</v-card-title>
          <v-divider class="my-0 mx-4"></v-divider>
          <v-card-text class="display-2">
            <ICountUp :endVal="record.today" />
            <span class="headline">分</span>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="4" md="3">
        <v-card>
          <v-card-title class="pb-2">今週({{ thisWeek }}~)</v-card-title>
          <v-divider class="my-0 mx-4"></v-divider>
          <v-card-text class="display-2">
            <ICountUp
              :endVal="record.thisWeek"
              :options="{
                                decimals: '.',
                                decimalPlaces: 1
                            }"
            />
            <span class="headline">時間</span>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="4" md="3">
        <v-card>
          <v-card-title class="pb-2">今月({{ thisMonth }})</v-card-title>
          <v-divider class="my-0 mx-4"></v-divider>
          <v-card-text class="display-2">
            <ICountUp :endVal="record.thisMonth" />
            <span class="headline">時間</span>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card>
          <v-card-title class="pb-2">総合計</v-card-title>
          <v-divider class="my-0 mx-4"></v-divider>
          <v-card-text class="display-2">
            <ICountUp :endVal="record.total" />
            <span class="headline">時間</span>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- チャート -->
    <v-row>
      <v-col cols="12" md="5">
        <span class="title">
          <v-icon class="mr-2 mb-1">mdi-chart-pie</v-icon>内訳
        </span>
        <v-tabs v-model="pie">
          <v-tab>今月(時間)</v-tab>
        </v-tabs>
        <!-- パイチャート -->
        <v-tabs-items v-model="pie">
          <v-tab-item>
            <v-card>
              <div class="py-4">
                <chart :options="chartPie" autoresize v-if="!isEmpty(chartPie.series[0].data)"></chart>
                <div v-else class="echarts">
                  <v-row align="center" justify="center">
                    <v-col>
                      <v-img :contain="true" max-height="260px" :src="'./svg/no_data.svg'"></v-img>
                      <p class="mt-2 subtitle-1 text-center">データがありません。</p>
                    </v-col>
                  </v-row>
                </div>
              </div>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-col>
      <v-col cols="12" md="7">
        <span class="title">
          <v-icon class="mr-2 mb-1">mdi-equalizer</v-icon>推移
        </span>

        <v-tabs v-model="stack">
          <v-tab>今週(分)</v-tab>
        </v-tabs>
        <!-- スタックチャート -->
        <v-tabs-items v-model="stack">
          <v-tab-item>
            <v-card>
              <div class="py-4">
                <chart
                  v-if="!isEmpty(this.chartStackWeek.series)"
                  :options="chartStackWeek"
                  autoresize
                ></chart>
                <div v-else class="echarts">
                  <v-row align="center" justify="center">
                    <v-col>
                      <v-img :contain="true" max-height="270px" :src="'./svg/no_data.svg'"></v-img>
                      <p class="mt-2 subtitle-1 text-center">データがありません。</p>
                    </v-col>
                  </v-row>
                </div>
              </div>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import moment from "moment";
import ICountUp from "vue-countup-v2";
export default {
  created() {
    // 今月の記録を取得
    window.axios
      .get("/api/records")
      .then(response => {
        this.timers.month = response.data;
      })
      .catch(err => {
        this.errorMessage = err;
        this.snackbar.error = true;
      });
    // これまでの総合計を取得
    window.axios
      .get("/api/records/total")
      .then(response => {
        this.record.total = response.data;
      })
      .catch(err => {
        this.errorMessage = err;
        this.snackbar.error = true;
      });
  },
  components: {
    ICountUp
  },
  data() {
    return {
      timers: {
        month: []
      },
      record: {
        today: 0,
        thisWeek: 0,
        thisMonth: 0,
        total: 0
      },
      stack: "",
      pie: "",
      snackbar: {
        error: false
      },
      errorMessage: "",

      // パイチャート用のプロパティ
      chartPie: {
        tooltip: {
          trigger: "item",
          formatter: "{b} : {c}時間 ({d}%)"
        },
        legend: {
          bottom: "10",
          left: "center"
        },
        calculable: true,
        series: [
          {
            name: "カテゴリーごとの記録",
            type: "pie",
            roseType: "radius",
            radius: [15, 95],
            center: ["50%", "38%"],
            data: [
              // 以下データの雛形
              //   {
              //     value: 11,
              //     name: "ポートフォリオ",
              //     itemStyle: { color: "#039BE5" }
              //   }
            ],
            animationEasing: "cubicInOut"
          }
        ]
      },

      // スタックチャート用のプロパティ
      chartStackWeek: {
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "shadow"
          }
        },
        grid: {
          top: 10,
          left: "2%",
          right: "2%",
          bottom: "3%",
          containLabel: true
        },
        xAxis: [
          {
            type: "category",
            data: ["日", "月", "火", "水", "木", "金", "土"],
            axisTick: {
              alignWithLabel: true
            }
          }
        ],
        yAxis: [
          {
            type: "value",
            axisTick: {
              show: false
            }
          }
        ],
        series: [
          // 以下データの雛形
          //   {
          //     name: "Programming",
          //     type: "bar",
          //     color: "#FFC46D",
          //     stack: "vistors",
          //     barWidth: "50%",
          //     data: [79, 52, 200, 334, 390, 330, 220]
          //   },
        ]
      }
    };
  },
  methods: {
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
    }
  },
  computed: {
    thisWeek() {
      return moment()
        .day(0)
        .format("M月D日");
    },
    thisMonth() {
      return moment().format("YYYY年M月");
    }
  },
  watch: {
    "timers.month": {
      handler: function(val) {
        // 現在のmomentオブジェクトを生成
        const now = moment();

        /**
         * 今日のレコード
         */
        // 今日のレコードのみに絞り込むためのコールバック関数
        function today(val) {
          return (
            // 計測中のタイマーを弾く
            val.stopped_at !== null &&
            // 今日と同じ日付の記録のみに絞り込み
            moment(val.started_at).isSame(now, "day")
          );
        }
        // 今日のレコード
        const timers_today = this.timers.month.filter(today);
        // 今日の全レコードの経過秒を初期化
        let amountToday = 0;
        // 今日の全レコードの経過分を計算
        for (let i = 0; i < timers_today.length; i++) {
          const started_at = moment(timers_today[i].started_at);
          const stopped_at = moment(timers_today[i].stopped_at);
          amountToday += stopped_at.diff(started_at, "seconds");
        }
        // 表示する値
        this.record.today = Math.round(amountToday / 60);

        /**
         * 今週のレコード
         */
        // 今週のレコードのみに絞り込むためのコールバック関数
        function thisWeek(val) {
          return (
            // 計測中のタイマーを弾く
            val.stopped_at !== null &&
            // 今週の記録のみに絞り込み
            moment(val.started_at).week() === now.week()
          );
        }
        // 今週のレコード
        const timers_this_week = this.timers.month.filter(thisWeek);
        // 今週の全レコードの経過秒を初期化
        let amountThisWeek = 0;
        // 今週の全レコードの経過時間(少数第一位まで)を計算
        for (let i = 0; i < timers_this_week.length; i++) {
          const started_at = moment(timers_this_week[i].started_at);
          const stopped_at = moment(timers_this_week[i].stopped_at);
          amountThisWeek += stopped_at.diff(started_at, "seconds");
        }
        // 表示する値(少数点第一位まで表示)
        this.record.thisWeek = Math.round((amountThisWeek / 3600) * 10) / 10;

        // 今月のレコード
        // 今月の全レコードの経過秒を初期化
        let amountThisMonth = 0;
        // 今月の全レコードの経過時間を計算
        for (let i = 0; i < this.timers.month.length; i++) {
          const started_at = moment(this.timers.month[i].started_at);
          // もし計測中の場合はスキップ
          if (this.timers.month[i].stopped_at === null) {
            continue;
          }
          const stopped_at = moment(this.timers.month[i].stopped_at);
          amountThisMonth += stopped_at.diff(started_at, "seconds");
        }
        // 表示する値
        this.record.thisMonth = Math.round(amountThisMonth / 3600);

        /**
         * パイチャート用に今月のデータを整形
         */
        // 今月のデータが空でない場合のみ実行
        if (!this.isEmpty(this.timers.month)) {
          // まず最初のデータをオブジェクトとして保存
          let started_at = moment(this.timers.month[0].started_at);
          let stopped_at = moment(this.timers.month[0].stopped_at);
          let id = this.timers.month[0].category_id;
          let value = stopped_at.diff(started_at, "seconds");
          let name = this.timers.month[0].category_name;
          let color = this.timers.month[0].category_color;
          // パイチャート用に渡すデータ
          let data = [
            {
              id,
              value,
              name,
              itemStyle: { color }
            }
          ];
          // 今月の記録でループを回す
          for (let i = 1; i < this.timers.month.length; i++) {
            // 計算中のタイマーは含めないので回避
            if (this.timers.month[i].stopped_at === null) {
              continue;
            }
            let started_at = moment(this.timers.month[i].started_at);
            let stopped_at = moment(this.timers.month[i].stopped_at);
            let value = stopped_at.diff(started_at, "seconds");
            let added = false;
            for (let j = 0; j < data.length; j++) {
              if (data[j]["id"] === this.timers.month[i].category_id) {
                data[j]["value"] += value;
                added = true;
                break;
              }
            }
            if (added === false) {
              let id = this.timers.month[i].category_id;
              let name = this.timers.month[i].category_name;
              let color = this.timers.month[i].category_color;
              data.push({
                id,
                value,
                name,
                itemStyle: { color }
              });
            }
          }
          // 計測期間が長い順に並び替え
          // 降順並び替え用の比較関数
          function compare(a, b) {
            let comparison = 0;
            if (a.value > b.value) {
              comparison = 1;
            } else {
              comparison = -1;
            }
            return comparison * -1;
          }
          // 計測期間が長い順に並び替え
          data.sort(compare);
          // 秒を時間に変換
          for (let i = 0; i < data.length; i++) {
            data[i].value = Math.round((data[i].value / 3600) * 10) / 10;
          }
          // 表示する値
          this.chartPie.series[0].data = data;
        }

        /**
         * パイチャート用に今月のデータを整形
         */
        // 今週のデータが空でない場合のみ実行
        if (!this.isEmpty(timers_this_week)) {
          // まず最初のデータでオブジェクトを生成
          let series = [
            {
              id: timers_this_week[0]["category_id"],
              name: timers_this_week[0]["category_name"],
              type: "bar",
              color: timers_this_week[0]["category_color"],
              stack: "vistors",
              barWidth: "50%"
            }
          ];
          for (let i = 1; i < timers_this_week.length; i++) {
            let isSame = false;
            for (let j = 0; j < series.length; j++) {
              if (series[j]["id"] === timers_this_week[i]["category_id"]) {
                isSame = true;
                break;
              }
            }
            if (isSame === false) {
              series.push({
                id: timers_this_week[i]["category_id"],
                name: timers_this_week[i]["category_name"],
                type: "bar",
                color: timers_this_week[i]["category_color"],
                stack: "vistors",
                barWidth: "50%"
              });
            }
          }
          // それぞれのカテゴリーで下記形式の日毎のデータを生成する
          // [日, 月, 火, 水, 木, 金, 土]
          // [79, 52, 200, 334, 390, 330, 220]
          // まずはカテゴリーごとに分類
          // 最初のデータでオブジェクトを生成
          let categories = [
            {
              id: timers_this_week[0]["category_id"],
              data: [timers_this_week[0]]
            }
          ];
          for (let i = 1; i < timers_this_week.length; i++) {
            let isSame = false;
            for (let j = 0; j < categories.length; j++) {
              if (categories[j]["id"] === timers_this_week[i]["category_id"]) {
                isSame = true;
                categories[j]["data"].push(timers_this_week[i]);
                break;
              }
            }
            if (isSame === false) {
              categories.push({
                id: timers_this_week[i]["category_id"],
                data: [timers_this_week[i]]
              });
            }
          }
          // 次にカテゴリーごとでループを回して日毎のデータを生成する
          for (let i = 0; i < categories.length; i++) {
            let data = [0, 0, 0, 0, 0, 0, 0];
            for (let j = 0; j < categories[i].data.length; j++) {
              const started_at = moment(categories[i].data[j].started_at);
              const stopped_at = moment(categories[i].data[j].stopped_at);
              const diff = stopped_at.diff(started_at, "seconds");
              const day = started_at.day();
              // 曜日をチェックして当てはまるものを足していく
              switch (day) {
                case 0:
                  data[0] += diff;
                  break;
                case 1:
                  data[1] += diff;
                  break;
                case 2:
                  data[2] += diff;
                  break;
                case 3:
                  data[3] += diff;
                  break;
                case 4:
                  data[4] += diff;
                  break;
                case 5:
                  data[5] += diff;
                  break;
                case 6:
                  data[6] += diff;
                  break;
              }
            }
            categories[i].data = data;
          }
          // seriesオブジェクトにcategoriesのdataオブジェクトをマージする
          for (let i = 0; i < series.length; i++) {
            for (let j = 0; j < categories.length; j++) {
              if (series[i].id === categories[j].id) {
                series[i] = Object.assign(series[i], categories[j]);
                break;
              }
            }
          }
          // dataの秒を分に整形
          for (let i = 0; i < series.length; i++) {
            for (let j = 0; j < series[i].data.length; j++) {
              series[i].data[j] =
                Math.round((series[i].data[j] / 60) * 10) / 10;
            }
          }
          // 表示する値
          this.chartStackWeek.series = series;
        }
      }
    }
  }
};
</script>
<style scoped>
.echarts {
  width: 100%;
  height: 350px;
}
</style>
