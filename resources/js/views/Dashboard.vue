<template>
    <div>
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
                    <v-card-title class="pb-2"
                        >今週({{ thisWeek }}~)</v-card-title
                    >
                    <v-divider class="my-0 mx-4"></v-divider>
                    <v-card-text class="display-2">
                        <ICountUp :endVal="record.thisWeek" />
                        <span class="headline">時間</span>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4" md="3">
                <v-card>
                    <v-card-title class="pb-2"
                        >今月({{ thisMonth }})</v-card-title
                    >
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
                    <v-tab>今日</v-tab>
                </v-tabs>
                <!-- パイチャート -->
                <v-tabs-items v-model="pie">
                    <v-tab-item>
                        <v-card>
                            <div class="py-4">
                                <chart :options="chartPie" autoresize></chart>
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
                    <v-tab>今週</v-tab>
                </v-tabs>
                <!-- スタックチャート -->
                <v-tabs-items v-model="stack">
                    <v-tab-item>
                        <v-card>
                            <div class="py-4">
                                <chart
                                    :options="chartStackWeek"
                                    autoresize
                                ></chart>
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
    components: {
        ICountUp
    },
    data() {
        return {
            record: {
                today: "234",
                thisWeek: "20",
                thisMonth: "100",
                total: "267"
            },
            stack: "",
            pie: "",
            currentItem: "",
            snackbar: false,

            chartPie: {
                tooltip: {
                    trigger: "item",
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    bottom: "10",
                    left: "center",
                    data: [
                        "Programming",
                        "Portfolio",
                        "English",
                        "UI UX Design",
                        "Other"
                    ]
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
                            {
                                value: 320,
                                name: "Programming",
                                itemStyle: { color: "#FFC46D" }
                            },
                            {
                                value: 240,
                                name: "Portfolio",
                                itemStyle: { color: "#26C6DA" }
                            },
                            {
                                value: 149,
                                name: "English",
                                itemStyle: { color: "#FA0202" }
                            },
                            {
                                value: 100,
                                name: "UI UX Design",
                                itemStyle: { color: "#5735CE" }
                            },
                            {
                                value: 59,
                                name: "Other",
                                itemStyle: { color: "#BDBDBD" }
                            }
                        ],
                        animationEasing: "cubicInOut"
                    }
                ]
            },

            chartStackWeek: {
                tooltip: {
                    trigger: "axis",
                    axisPointer: {
                        // Axis indicator, axis trigger is valid
                        type: "shadow" // The default is a straight line, which can be selected as: 'line' | 'shadow'
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
                        data: ["月", "火", "水", "木", "金", "土", "日"],
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
                    {
                        name: "Programming",
                        type: "bar",
                        color: "#FFC46D",
                        stack: "vistors",
                        barWidth: "50%",
                        data: [79, 52, 200, 334, 390, 330, 220]
                    },
                    {
                        name: "Portfolio",
                        type: "bar",
                        color: "#26C6DA",
                        stack: "vistors",
                        barWidth: "50%",
                        data: [80, 52, 200, 334, 390, 330, 220]
                    },
                    {
                        name: "English",
                        type: "bar",
                        color: "#FA0202",
                        stack: "vistors",
                        barWidth: "50%",
                        data: [30, 52, 200, 334, 390, 330, 220]
                    },
                    {
                        name: "UI UX Design",
                        type: "bar",
                        color: "#5735CE",
                        stack: "vistors",
                        barWidth: "50%",
                        data: [80, 52, 200, 334, 390, 330, 220]
                    },
                    {
                        name: "Other",
                        type: "bar",
                        color: "#BDBDBD",
                        stack: "vistors",
                        barWidth: "50%",
                        data: [30, 52, 200, 334, 390, 330, 220]
                    }
                ]
            }
        };
    },
    methods: {
        selectRow(event) {
            this.snackbar = true;
            this.currentItem = event;
        }
    },
    computed: {
        thisWeek() {
            return moment()
                .day(1)
                .format("M月D日");
        },
        thisMonth() {
            return moment().format("YYYY年M月");
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
