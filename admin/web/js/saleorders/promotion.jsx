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
        this._handleAjax($('form#promotions').attr('data-key'));
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
        return (
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
                <div className="row" style={{marginTop:10}}>
                    <div className="col-sm-6"><a href={ "index.php?r=SaleOrders/promotions-item-group/index" }  >{ "⌥ " + t_manage }</a>  </div>
                    <div className="col-sm-6 text-right"><a href={ "index.php?r=SaleOrders%2Fpromotions-item-group%2Fview-name&name=" + model }  >{ "✎ " + t_edit }</a></div>
                </div>
            </div>
          );
        
    }
  
  }
  
  ReactDOM.render(
    <ShowTable />,
    document.getElementById('itemTable')
  );
  
    
  
  
  