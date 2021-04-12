import './translations/config'
import './translations/javascript/en-GB'
import './translations/javascript/fr-FR'

import Translator from './translator.min.js'
import echarts from 'echarts/lib/echarts'

//Echarts components
require("echarts/lib/chart/line");
require('echarts/lib/chart/bar');
require('echarts/lib/chart/heatmap');
require('echarts/lib/chart/tree');
require('echarts/lib/component/calendar');
require('echarts/lib/component/tooltip');
require('echarts/lib/component/visualMap');

let monthsLabel = [
    Translator.trans('global.months.january'),
    Translator.trans('global.months.february'),
    Translator.trans('global.months.march'),
    Translator.trans('global.months.april'),
    Translator.trans('global.months.may'),
    Translator.trans('global.months.june'),
    Translator.trans('global.months.july'),
    Translator.trans('global.months.august'),
    Translator.trans('global.months.september'),
    Translator.trans('global.months.october'),
    Translator.trans('global.months.november'),
    Translator.trans('global.months.december')
];

let statisticHolder = document.querySelector('.statistics-holder');
let isDarkMode =  statisticHolder.dataset.isDarkMode == 1 ? true : false;
let themeMainHue = statisticHolder.dataset.themeMainHue;
let themeDarkHue = statisticHolder.dataset.themeDarkHue;
let themeLightHue = statisticHolder.dataset.themeLightHue;
let themeLightestHue = statisticHolder.dataset.themeLightestHue;

let itemsEvolutionData = JSON.parse(document.querySelector('#items-evolution-chart').dataset.json);
let calendarsData = JSON.parse(document.querySelector('#calendars').dataset.json);
let treeJson = JSON.parse(document.querySelector('#radial-tree').dataset.json);

loadChart(document.querySelector('#month-days-chart'));
loadChart(document.querySelector('#hours-chart'));
loadChart(document.querySelector('#months-chart'));
loadChart(document.querySelector('#week-days-chart'));

function loadChart(element) {
    const data = JSON.parse(element.dataset.json);
    echarts.init(element).setOption({
        tooltip: {
            formatter: function (params) {
                return Translator.transChoice('statistics.items_added', params.value);
            }
        },
        color: [themeMainHue],
        xAxis: {
            type : 'category',
            data: data.map(element => element.abscissa),
            axisLabel: {
                textStyle: {
                    color: isDarkMode ? '#f0f0f0': '#323233'
                }
            },
            axisTick: {
                alignWithLabel: true,
                lineStyle: {
                    color: isDarkMode ? '#f0f0f0': '#323233'
                }
            },
            axisLine: {
                lineStyle: {
                    color: isDarkMode ? '#f0f0f0': '#323233'
                }
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: isDarkMode ? '#7d7f82': '#ccc'
                }
            },
            axisLabel: {
                textStyle: {
                    color: isDarkMode ? '#f0f0f0': '#323233'
                }
            },
            axisTick: {
                lineStyle: {
                    color: isDarkMode ? '#f0f0f0': '#323233'
                }
            },
            axisLine: {
                lineStyle: {
                    color: isDarkMode ? '#f0f0f0': '#323233'
                }
            }
        },
        series: [{
            type: 'bar',
            data: data.map(element => element.count)
        }]
    });
}

echarts.init(document.getElementById('items-evolution-chart')).setOption({
    tooltip: {
        trigger: 'axis',
        position: function (params) {
            return [params[0], '10%'];
        },
        formatter: function (params) {
            return params[0].axisValue + ': ' + Translator.transChoice('statistics.items', params[0].data);
        }
    },
    color: [themeMainHue],
    xAxis: {
        type: 'category',
        data: Object.keys(itemsEvolutionData),
        axisLabel: {
            textStyle: {
                color: isDarkMode ? '#f0f0f0': '#323233'
            }
        },
        axisTick: {
            alignWithLabel: true,
            lineStyle: {
                color: isDarkMode ? '#f0f0f0': '#323233'
            }
        },
        axisLine: {
            lineStyle: {
                color: isDarkMode ? '#f0f0f0': '#323233'
            }
        }
    },
    yAxis: {
        type: 'value',
        axisLabel: {
            textStyle: {
                color: isDarkMode ? '#f0f0f0': '#323233'
            }
        },
        axisTick: {
            lineStyle: {
                color: isDarkMode ? '#f0f0f0': '#323233'
            }
        },
        axisLine: {
            lineStyle: {
                color: isDarkMode ? '#f0f0f0': '#323233'
            }
        }
    },
    dataZoom: [{
        handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
        handleSize: '80%',
        handleStyle: {
            color: '#fff',
            shadowBlur: 3,
            shadowColor: 'rgba(0, 0, 0, 0.6)',
            shadowOffsetX: 2,
            shadowOffsetY: 2
        }
    }],
    series: [{
        data: Object.values(itemsEvolutionData),
        type: 'line',
        smooth: true,
        symbol: 'none',
        sampling: 'average',
        areaStyle: {
            normal: {
                color: themeMainHue
            }
        }
    }]
});



Object.entries(calendarsData).forEach(([year, yearData]) => {
    var data = [];

    Object.entries(yearData).forEach(([index, value]) => {
        data.push([value[0], "" + value[1]]);
    });

    echarts.init(document.getElementById('calendar_' + year)).setOption({
        tooltip: {
            formatter: function (params) {
                return Translator.transChoice('statistics.items_added', params.value[1]);
            }
        },
        visualMap: {
            type: 'piecewise',
            orient: 'horizontal',
            right: '215',
            bottom: 'bottom',
            pieces: [
                {min: 31, color: themeDarkHue},
                {min: 16, max: 30, color: themeMainHue},
                {min: 6, max: 15, color: themeLightHue},
                {min: 1, max: 5, color: themeLightestHue},
                {min: 0, max: 0, color: '#ededed'}
            ],
        },
        calendar: {
            splitLine: {
                show: false,
            },
            top: 'middle',
            left: 'center',
            range: year,
            cellSize: 20,
            yearLabel: {show: false},
            itemStyle: {
                normal: {
                    borderWidth: 2,
                    borderColor: isDarkMode ? '#36393e' : '#ffffff',
                    color: isDarkMode ? '#7d7f82' : '#ededed'
                },
            },
            dayLabel: {
                show: false
            },
            monthLabel: {
                show: true,
                nameMap: monthsLabel,
                color: isDarkMode ? '#f0f0f0': '#323233'
            }
        },
        series: [
            {
                type: 'heatmap',
                coordinateSystem: 'calendar',
                calendarIndex: 0,
                data: data
            }
        ]
    });
});

echarts.init(document.getElementById('radial-tree')).setOption({
    tooltip: {
        trigger: 'item',
        triggerOn: 'mousemove'
    },
    series: [
        {
            type: 'tree',
            data: [treeJson],
            layout: 'radial',
            symbol: 'emptyCircle',
            symbolSize: 7,
            initialTreeDepth: -1,
            animationDurationUpdate: 750,
            itemStyle: {
                borderColor: themeMainHue,
            },
            lineStyle: {
                color: isDarkMode ? '#4a4b4d' : '#ccc'
            },
            label: {
                color: isDarkMode ? '#a6a7a8' : '#555'
            }
        }
    ]
});

