/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function()
{
    var chart = $('#chart_moutput_division').highcharts();
    $('#chart_type').click(function() {
        console.log(chart);
//        chart.update({
//            series: {
//                type: 'line',
//            },
//            subtitle: {
//                text: 'Plain'
//            }
//        });
    });
});

