class ShowTable extends React.Component {
    constructor(props) {
      super(props);
      this.state = {     
        data:null,
        model:null,
      }
    }

   
    componentWillMount(){
        console.log('willMount');
        
    }

    componentDidMount(){
        console.log('didMount');
        this._handleAjax($('#dataTable').attr('data-key'));
    }

    
    _handleAjax = async (id) => { 
        if ( id ) {
            await fetch('index.php?r=SaleOrders/promotions/get-item-list&id=' + id, {
                method: 'POST',            
                body: JSON.stringify({id:id}),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                },
            })
            .then((response) => { return response.json() })
            .then((model) => {       
                //console.log(model);        
                this.setState({data:model.data[0]});
                this.setState({model:model.name});
            })
            .catch((error) => { 
                console.warn(error);
            });
        }
    };
 
    
    render() {
        const items = this.state.data;
        const model = this.state.model;
        return(       
            <div>
                {
                    ( items ) ?
                    <BootstrapTable data={ items }>
                    <TableHeaderColumn dataField='item_code' isKey>{ t_code }</TableHeaderColumn>
                    <TableHeaderColumn dataField='item_name'>{ t_product }</TableHeaderColumn>
                    </BootstrapTable>
                    :
                    ''
                }
                 
            </div>     
        );
    }
  
  }
  
  ReactDOM.render(
    <ShowTable />,
    document.getElementById('dataTable')
  );
  
    
  
  
  