
<div id="nbdownloads"></div>

<script>

Highcharts.chart('nbdownloads', {

title: {text: "{{ $channel->channel_name }}"},
//subtitle: { text: 'Source: thesolarfoundation.com' },
xAxis: { 
	type:'datetime',
	linecolor:'#000000',
		categories: 
	[
		@foreach ($nb_downloads_X as $x)
		'{{ $x }}',
		@endforeach
	] 
},
yAxis: { 
	title: { text: "{{ __('messages.highcharts_line_graph_y_label') }}" } 
},
legend: {
	layout: 'vertical',
	align: 'right',
	verticalAlign: 'middle'
},

//plotOptions: { series: { label: { connectorAllowed: false }} },

series: [{
	name: "{{ __('messages.highcharts_line_graph_legend_label') }}",
	data: [
	@foreach ($nb_downloads_Y as $y)
		{{ $y }},
	@endforeach
	]
}],

responsive: {
	rules: [{
		condition: { maxWidth: 500 },
		chartOptions: {
			legend: { layout: 'horizontal', align: 'center', verticalAlign: 'bottom' }
		}
	}]
}

});
</script>
