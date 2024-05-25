define(['jquery', 'ApexCharts', 'core/str'], function($, ApexCharts, str) {
    return {
        init: function(quizdata, sectionname, score, sec) {
            str.get_strings([
                {key: 'correct_label', component: 'local_assessment'},
                {key: 'wrong', component: 'local_assessment'},
                {key: 'unatmpt', component: 'local_assessment'},
                {key: 'section', component: 'local_assessment'}
            ]).done(function(translations) {
                var options = {
                    series: [parseInt(quizdata[0]), parseInt(quizdata[1]), parseInt(quizdata[2])],
                    chart: {
                        width: '100%',
                        type: 'donut',
                    },
                    labels: [translations[0], translations[1], translations[2]],
                    xaxis: {
                        labels: {
                            rotate: 0
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: '100%'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };
                var chart = new ApexCharts.default(document.querySelector("#chart"), options);
                chart.render();
                options = {
                    series: [{
                        name: "",
                        data: score
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    title: {
                        text: translations[3],
                        align: 'left'
                    },
                    grid: {
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },

                    xaxis: {
                        categories: sectionname,
                        labels: {
                            style: {
                                fontSize: '11px',
                            },
                        },
                    }
                };
                chart = new ApexCharts.default(document.querySelector("#bar"), options);
                chart.render();
                for (var i = 0; i < sec.length; i++) {
                    var options = {
                        series: [{
                            name: "",
                            data: [sec[i].correct, sec[i].wrong, sec[i].noattempt]
                        }],
                        chart: {
                            height: 250,
                            type: 'bar',
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 10,
                                dataLabels: {
                                    position: 'top',
                                },
                                columnWidth: '30'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return val;
                            },
                            offsetY: -20,
                            style: {
                                fontSize: '12px',
                                colors: ["#304758"]
                            }
                        },

                        xaxis: {
                            categories: [translations[0], translations[1], translations[2]],
                            colors: ['#F44336', '#E91E63', '#9C27B0'],
                            position: 'top',
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            },
                            crosshairs: {
                                fill: {
                                    type: 'gradient',
                                    gradient: {
                                        colorFrom: '#D8E3F0',
                                        colorTo: '#BED1E6',
                                        stops: [0, 100],
                                        opacityFrom: 0.4,
                                        opacityTo: 0.5,
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true,
                            }
                        },
                        yaxis: {
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false,
                            },
                            labels: {
                                show: false,
                                formatter: function(val) {
                                    return val;
                                }
                            }

                        },
                        title: {
                            text: sec[i].name,
                            floating: true,
                            offsetY: 330,
                            align: 'center',
                            style: {
                                color: '#444'
                            }
                        }
                    };
                    chart = new ApexCharts.default(document.querySelector(".sectionchart" + "-" + sec[i].id), options);
                    chart.render();
                }
            });
        }
    };
});
