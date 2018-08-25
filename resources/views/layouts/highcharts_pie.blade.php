
<div id="podcastApps"></div>

<script>
Highcharts.chart('podcastApps', 
{
	chart: 
	{
		plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
	},
	title: 
	{
		text: 'Applications'
	},
	tooltip: 
	{
		pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	},
	plotOptions: 
	{
		pie: 
		{
			allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: 
				{
					enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
						style: 
						{
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
				}
		}
	},
	series: [
	{
		name: 'Applications',
			colorByPoint: true,
			data: [
                @foreach ($pie_results as $item)
                    {
                        name: '{{ $item->ua_appName}}'
                        ,y: {{ $item->percentage }}
                        @if ($loop->first) 
                            ,sliced: true
                            ,selected: true
                        @endif                
                    }
                    @if (!$loop->last) , @endif
                @endforeach
			]
		}]
	});
</script>
