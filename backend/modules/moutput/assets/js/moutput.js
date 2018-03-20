/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function()
{

    $('#chart_type_line').click(function() {
        var chart = $('#chart_moutput_division').highcharts();
        chart.update({
            chart: {
                type: 'line',
            },
            subtitle: {
                text: 'Plain'
            }
        });
    });
    $('#chart_type_column').click(function() {
        var chart = $('#chart_moutput_division').highcharts();
        chart.update({
            chart: {
                type: 'column',
            },
            subtitle: {
                text: 'Plain'
            }
        });
    });
});

