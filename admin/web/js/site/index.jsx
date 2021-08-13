class SaleChart extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      sales: [],
      fulldata: [],
      text : {
        salePeople : 'Sale People'
      }
    }
  }

  componentDidMount() {
    this.getSales();   
    this.setState({ text : {salePeople : 'Sale People' }})
  }
 
  getSales = _ => {
    var that = this
 
    $.ajax({
      url:'?r=SaleOrders/ajax/data-chart&api=react',
      type:'GET',
      dataType:'JSON',
      success:function(response){
        that.setState({ fulldata: response.fulldata });
        that.setState({ sales: response });
        that.getDataChart(that.state.sales);        
      }
    })
  }

  
 getDataChart = (data) => { 
    var cts = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(cts, {
        type: 'pie',
        data: {
            labels: data.sales,
            datasets: [{
                label: 'THB (m) ',
                data: data.data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)', 
                    'rgba(255, 159, 64, 1)'
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


  tableRender = ({ id, img, name, total }) => {
    
    return (
        <tr key={id}>
            <td><img src={img} className="img-responsive" width="50"/></td>
            <td>{name}</td>          
            <td className="text-right">
              <div >
                {new Intl.NumberFormat('th-TH', { 
                  style: 'currency', 
                  currency: 'THB',
                  minimumFractionDigits: 0, 
                  maximumFractionDigits: 0 
                }).format(total)}                
              </div>
              <small className="text-gray">Invoice</small>
            </td>
            <td className="text-right hidden-xs" width="210px">
                <a className="btn btn-sm btn-default btn-flat" href={'?r=salepeople%2Fpeople%2Fview&id='+id} target="_blank"><i className="far fa-edit"></i></a>
                <button className="btn btn-sm btn-default btn-flat"><i className="fas fa-cog"></i></button>
                <button className="btn btn-sm btn-default btn-flat"><i className="far fa-envelope"></i></button>
                <button className="btn btn-sm btn-default btn-flat"><i className="fas fa-sync-alt"></i></button>
                <button className="btn btn-sm btn-default btn-flat"><i className="fas fa-file"></i></button>                
            </td>
            <td className="text-center">
              {
                total ? (
                    <i className="fas fa-circle text-green"></i>
                  ) : (
                    <i className="fas fa-circle text-red"></i>
                  )               
              }                
            </td>
        </tr>
    )
  }
  
  
  render() {
    const { fulldata } = this.state;
    return(       
        <div className="row">
          <div className="col-md-8">						
            <div className="panel panel-default" >
                <table className="table">
                  <tbody>{fulldata.map(this.tableRender)}</tbody>
                </table>
                <div className="panel-footer">
                  {this.state.text.salePeople}  
                </div>
            </div>						
          </div>
          <div className="col-md-4">
            <div className="box box-info">              
              <canvas id="myChart" width="400" height="700"></canvas>        
            </div>
          </div>
        </div>      
    );
  }

}

ReactDOM.render(
  <SaleChart />,
  document.getElementById('Sec3')
);

  


