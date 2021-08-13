class DashboardTable extends React.Component {
    constructor(props){
        super(props);
        this.state = {
			montyly:[],
			montyly2: [],
			keys: [],
			dataValue: [],
			dataValue2: [],
			customer: []
        }
    }

    componentWillMount(){
		this.getData();
    }
        
	// getData = _ => {
	// 	this.getDataChart();
	// 	this.getPieChart();
	// }

	getData = _ => {
		var that = this	
		var d = new Date();
		let keys = [];
		let dataValue = [];
		let dataValue2 = [];

		$.ajax({
			url:'?r=SaleOrders/ajax/monthly-by-sales&year='+ d.getFullYear(),
			type:'GET',
			dataType:'JSON',
			success:function(response){
				that.setState({ montyly: response });
				for (let [key, value] of Object.entries(that.state.montyly)){
					keys.push(key);
					dataValue.push(Math.round(value));					
				}
				that.setState({ keys:keys, dataValue : dataValue })

				$.ajax({
					url:'?r=SaleOrders/ajax/monthly-by-sales&year='+ (d.getFullYear() -1),
					type:'GET',
					dataType:'JSON',
					success:function(response){		
						that.setState({ montyly2: response });
						for (let [key, value] of Object.entries(that.state.montyly2)){
							dataValue2.push(Math.round(value));	
						}
						that.setState({ dataValue2 : dataValue2 })		
						if(that.state.dataValue.length > 0){
							that.renderLineChart(keys,that.state.dataValue,that.state.dataValue2);		
						}					
						
										
					}
				})
				//that.renderPieChart(keys,dataValue);	

			}
		})

		
		fetch("?r=customers/ajax/duedate-for-discount", {
			method: "POST",
			body: JSON.stringify({ id: 1 }),
			headers: {
			  "Content-Type": "application/json",
			  "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
			}
		  })
			.then(res => res.json())
			.then(response => {
			  
				that.setState({customer : response});
			   
			})
			.catch(error => {
			  console.log(error);
			});	
		
		
	}


	renderLineChart = (keys, dataValue, dataValue2) => { 

		$('body').on('click','.line-detail-click',function(){
			var y = $(this).attr('data-year');
			var m = $(this).attr('data-month');	
			window.open("index.php?ViewRcInvoiceSearch[posting_date]=" + m + "/" + y + "&r=SaleOrders/report/invoice-list",'_blank');
		});

		var d = new Date();
		var ctx = document.getElementById("productChart").getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: keys,
				datasets: [
					{
						label : d.getFullYear(),
						backgroundColor : "rgba(75, 192, 192, 0.2)",
						borderColor : "rgba(75, 192, 192, 1)",
						pointBackgroundColor : "rgba(179,181,198,1)",
						pointBorderColor : "#fff",
						pointHoverBackgroundColor : "#fff",
						pointHoverBorderColor : "rgba(179,181,198,1)",
						data:dataValue						 
					},
					{
						label: (d.getFullYear()-1),
						backgroundColor : "rgba(255,99,132,0.2)",
						borderColor : "rgba(255,99,132,1)",
						pointBackgroundColor : "rgba(255,99,132,1)",
						pointBorderColor : "#fff",
						pointHoverBackgroundColor : "#fff",
						pointHoverBorderColor : "rgba(255,99,132,1)",
						data : dataValue2						 
					}
				],
				
			},
			options: {
				onClick: function(e,i) {

					e = i[0];
					let res_data = [];					
		 
					if (typeof e === 'undefined'){
						console.log('The property is not available...');
					}else{	
				 				
						bsModal({
							data:this.data.datasets,
							month:this.data.labels[e._index]
						});						
					}

					

					function bsModal(obj){

						$('#modal-line-chart').modal('show'); 
						$('#modal-line-chart .modal-title').html(t.LABEL_MODAL_TITLE);

						let html = '<table class="table">';
						html+= '<thead><tr >';
						html+= '	<th>' + t.LABEL_DATE + '</th>';
						html+= '	<th class="text-right">' + t.LABEL_AMOUNT + '</th>';
						html+= '</tr ></thead>';

						(obj.data).forEach(element => {

							html+= '<tr >';
							html+= '	<td>' + obj.month + ' ' + element.label + '</td>';

							html+= '	<td class="text-right">'+
										'<button type="button" class="btn btn-primary btn-xs btn-flat line-detail-click" data-month="' + (e._index + 1) + '" data-year="' + element.label + '">' +
										 number_format((element.data[e._index]).toFixed(0)) + '</button></td>';
							html+= '</tr >';
							
							// res_data.push({
							// 	label: element.label,
							// 	index: e._index,
							// 	month: obj.month,
							// 	money: element.data[e._index],
							// 	//element:element
							// });
						});

						html+= '</table>';				
						$('#modal-line-chart .modal-body').html(' '+ html + ' \r\n');

						//console.log(res_data);
						
					}

					
					
				},	
				legend: {
					onHover: function(e) {
					   e.target.style.cursor = 'pointer';
					}
				 },
				 hover: {
					onHover: function(e) {
					   var point = this.getElementAtEvent(e);
					   if (point.length) e.target.style.cursor = 'pointer';
					   else e.target.style.cursor = 'default';
					}
				 }, 		
				tooltips: {
					mode: 'label',
					label: 'mylabel',
					callbacks: {
						label: function(tooltipItem, data) {
						return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }, 
					},
				 },
				scales: {
					yAxes: [{
						ticks: {
							callback: function(label, index, labels) { return label/1000; },
							beginAtZero:true,
							fontSize: 10,
						},
						gridLines: {
							display: false
						},
						scaleLabel: { 
							display: true,
							labelString: t.LABEL_MILLION,
							fontSize: 10,
						}
					}],
					xAxes: [{
						ticks: {
							beginAtZero: true,
							fontSize: 10
						},
						gridLines: {
							display:false
						},
						scaleLabel: {
							display: true,
							fontSize: 10,
					   }
					}]
				},		
				
		 
			}
		});
		
	}

	renderBarChart = (keys, dataValue) => { 
		var ctx = document.getElementById("productChart").getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: keys,
				datasets: [{
					label: 'THB',
					data: dataValue,
					backgroundColor: [
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 99, 142, 0.2)',
						'rgba(54, 162, 245, 0.2)',
						'rgba(255, 206, 96, 0.2)',
						'rgba(75, 192, 182, 0.2)',
						'rgba(153, 102, 265, 0.2)',
						'rgba(255, 159, 74, 0.2)'
					],
					borderColor: [
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255, 99, 142, 1)',
						'rgba(54, 162, 245, 1)',
						'rgba(255, 206, 96, 1)',
						'rgba(75, 192, 182, 1)',
						'rgba(153, 102, 265, 1)',
						'rgba(255, 159, 74, 1)'
					],
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true
						}
					}]
				}
			}
		});
	}

	renderPieChart = (keys, dataValue) => { 
		var ctx = document.getElementById("productPieChart").getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'pie',
			data: {
				labels: keys, 
				datasets: [{
					label: '฿',
					data: dataValue,
					backgroundColor: [
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 99, 142, 0.2)',
						'rgba(54, 162, 245, 0.2)',
						'rgba(255, 206, 96, 0.2)',
						'rgba(75, 192, 182, 0.2)',
						'rgba(153, 102, 265, 0.2)',
						'rgba(255, 159, 74, 0.2)'
					],
					borderColor: [
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255, 99, 142, 1)',
						'rgba(54, 162, 245, 1)',
						'rgba(255, 206, 96, 1)',
						'rgba(75, 192, 182, 1)',
						'rgba(153, 102, 265, 1)',
						'rgba(255, 159, 74, 1)'
					],
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true
						}
					}]
				}
			}
		});
	}


    render (){
		const { customer } = this.state;
		 
        return (	 
				
			<div className="row">
				<div className="col-lg-8 col-md-12">
				
				<div className="panel panel-success">
					<div className="panel-heading">
						<div className="row">
							<div className="col-sm-12">
								{t.LABEL_MONTHLY_RECAP_REPORT}
							</div>
						 						
						</div>
					</div>
					<div className="panel-body row">	 
						<div className="col-sm-12">		  
							<canvas id="productChart" width="745" height="279"></canvas>
						</div>
						
					</div>
					
				</div>
			
			</div>
			<div className="col-lg-4 col-md-12">
				<div className="panel panel-warning">
						<div className="panel-heading">
							<div className="row">						 
								<div className="col-sm-12">
									{t.LABEL_CUSTOMER_DUE}
								</div>								
							</div>
						</div>
						<div className="panel-body-d"> 
							
						<div className="table-customer-due">							 
								<table className="table table-bordered">
									<thead>
										<tr>										 
											<th className="text-name">{t.LABEL_NAME}</th>
											<th className="text-due-date">{t.LABEL_DUE_DATE}</th>
										</tr>
									</thead>
									<tbody>
										{ 
											customer.length > 0 ? customer.map(model => 
												<tr key={model.id} >
																									
													<td><a href={ '?r=SaleOrders%2Fsaleorder&SaleListSearch%5Bcustomer_id%5D='+model.id} target="_blank">{model.name} </a></td>
													<td className="">{t.LABEL_EVERY_DATE} <span className="pull-right">{model.duedate}</span></td>
												</tr>
											) : null
										}
									</tbody>
								</table>
								
							</div>
							
						</div>
						
					</div>
				</div>
				
			</div>
        )
    }
}

ReactDOM.render(
    <DashboardTable />,
    document.getElementById('saleTable')
);
 
    